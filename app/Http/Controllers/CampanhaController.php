<?php

namespace App\Http\Controllers;

use App\Enums\Parametro;
use App\Enums\Precos;
use App\Enums\Servicos;
use App\Http\Controllers\Controller;
use App\Models\AgendaCampanha;
use App\Models\AndamentoCampanha;
use App\Models\TipoAndamento;
use App\Models\UnidadeOperacional;
use App\Services\Correios\classes\Campanha;
use App\Services\Correios\classes\ClienteContrato;
use App\Services\Correios\CorreiosServices;
use App\Models\AgenciaApta;
use App\Models\LogLocalidade;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\InformacaoCampanha;
use DB;
use Illuminate\Validation\Rules\In;

class CampanhaController extends Controller
{
    use CorreiosServices;

    public function listarCargas(Request $request)
    {
        $agencia = $request->input('agencia', null);
        $query = $request->query('query', null);

        if (empty($agencia)) return response()->json('Informe a agencia ', 422);

        $cargas = AgendaCampanha::where('mcmcu', 'like', '%' . $agencia['codigo'])->get();
        $idAutorizador = $this->idAutorizadorUsuario();
        $contrato = $this->contrato();
        $cargas->each(function (AgendaCampanha $carga) use ($idAutorizador, $contrato) {
            $carga->informacaoCampanha['servico'] = Servicos::CODIGOS_SERVICOS_DESCRICAO[collect(Servicos::CODIGOS_SERVICOS_DESCRICAO)->search(function ($value) use ($carga) {
                return $value['codigo'] == $carga->informacaoCampanha['jsitm_codigo_servico'];
            })];
            $info = $carga->informacaoCampanha;
            if (!empty($info['ica_co_cpf_cnpj_cliente'])) $carga['cliente'] = $idAutorizador->getUserByCpfCnpj($info['ica_co_cpf_cnpj_cliente']); else
                $carga['cliente'] = $contrato->consultaClientePorCartaoDePostagem(str_pad($info['cpt_nu_cartao_postagem'], 10, '0', STR_PAD_LEFT))->cliente;

            $carga->informacaoCampanha->andamentoCampanha;

            $carga['ultimo_status'] = $carga->informacaoCampanha->andamentoCampanha->last();
        });

        return response()->json($cargas->filter(function ($carga) {
            return !empty($carga->informacaoCampanha->andamentoCampanha->last()) && $carga->informacaoCampanha->andamentoCampanha->last()['tan_nu'] >= 3;
        })->values());
    }

    public function atualizar(Request $request, $id)
    {
        $campanha = InformacaoCampanha::find($id);
        $andamento = $request->input('andamento');

        $campanha->andamentoCampanha()->attach($andamento['tipoAndamento'], ['aca_dt' => new Carbon($andamento['dt_andamento'])]);


        return response()->json($campanha->andamentoCampanha->last());
    }

    public function listar(Request $request)
    {
        $query = $request->input('query', []);

        $campanhas = InformacaoCampanha::filtering($query)->get();
        $contratoService = $this->contrato();
        $campanhas->map(function (InformacaoCampanha $campanha) use ($contratoService) {
            $campanha['cliente'] = $contratoService->consultaClientePorCartaoDePostagem('0067599109')->toCollection()->first();
            $campanha['tipo_carga'] = Servicos::CODIGOS_SERVICOS[$campanha['jsitm_codigo_servico']];
            $campanha['faixa'] = 12;
            $campanha['servico'] = collect(Servicos::CODIGOS_SERVICOS_DESCRICAO)->filter(function ($cod) use ($campanha) {
                return $cod['codigo'] == $campanha['jsitm_codigo_servico'];
            })->first();

            $postagem = $campanha->andamentoCampanha()->where('tipo_andamento.tan_nu', '=', 3)->first();
            $campanha['dt_postagem'] = !empty($postagem) ? $postagem['pivot']['aca_dt'] : 'não postado';

            $dtInicio = $campanha->andamentoCampanha()->where('tipo_andamento.tan_nu', '=', 5)->first();
            $campanha['dt_inicio_distribuicao'] = !empty($dtInicio) ? $dtInicio['pivot']['aca_dt'] : '';

            $dtFim = $campanha->andamentoCampanha()->where('tipo_andamento.tan_nu', '=', 6)->first();
            $campanha['dt_fim_distribuicao'] = !empty($dtFim) ? $dtFim['pivot']['aca_dt'] : '';


            $campanha->agendaAgencias;
            return $campanha;

        })->filter(function (InformacaoCampanha $campanha) use ($query) {
            $ret = false;
            if (isset($query['valorInicio'])) $ret = $campanha['ica_qt_peca'] * $campanha['faixa'] >= $query['valorInicio'];
            if (isset($query['valorFinal'])) $ret = $campanha['ica_qt_peca'] * $campanha['faixa'] <= $query['valorFinal'];

            return $ret;
        });
        return response()->json($campanhas);
    }

    public function validaCupom(Request $request, $id)
    {
        $campanha = InformacaoCampanha::find($id);
        if (empty($campanha)) return response()->json('campanha não encontrada', 404);

        $andamento = $campanha->andamentoCampanha()->orderBy('aca_dt', 'desc')->first();
        if ($andamento['tan_nu'] != 3) return response()->json('campanha não postada', 422);

        return response()->json($campanha['ica_nu']);
    }

    public function calcularDados(Request $request)
    {
        $peso = $request->input('peso');
        $segmentos = strtolower(collect($request->input('segmentos', null))->implode('segtx', ','));
        $bairros = $request->input('bairros');

//        if(!empty($segmentos)){
        foreach ($bairros as $bairro) {
            $out[] = rand(180, 200);
        }
        $preco = Precos::LOCAL[Precos::getFaixa($request->json('dados')['peso'])];
        $out = array_sum($out);
//        }

        return response()->json(['total_pecas' => $out, 'faixa_preco' => $preco, 'segmentos' => $segmentos]);
    }

    public function calculaPrazo(Request $request)
    {
        $origem = $request->json('origem');
        $destinos = collect($request->json('destinos'));

        $cep = $this->cep()->getPorEndereco($origem['uf'] . ',' . $origem['municipio']);
        $cepOrigem = isset($cep->toArray()[0]['CEPS']['CEP'][0]) ? $cep->toArray()[0]['CEPS']['CEP'][0][0]['VCEP'] : $cep->toArray()['CEPS']['CEP'][0]['VCEP'];

        $cepService = $this->cep();
        $campanhaService = $this->campanha();

        return response()->json($destinos->map(function ($destino) use ($cepService, $campanhaService, $cepOrigem) {
            $cep = $cepService->getPorEndereco($destino['uf'] . ',' . $destino['municipio']);
            $cepDestino = isset($cep->toArray()[0]['CEPS']['CEP'][0]) ? $cep->toArray()[0]['CEPS']['CEP'][0][0]['VCEP'] : $cep->toArray()['CEPS']['CEP'][0]['VCEP'];
            return $campanhaService->calcPrazo(Servicos::COD_MALA_DIRETA_PERFIL_INTERNO, $cepOrigem, $cepDestino)->toArray()['CalcPrazoResult']['Servicos']['cServico']['PrazoEntrega'];
        })->max());

    }

    public function salvar(Request $request)
    {


        $dados = collect($request->json('dados'));

        DB::beginTransaction();

        try {
            $campanha = new InformacaoCampanha();
            //$flight->name = $request->name;
//            $campanha->ica_nu = ;
            if ($dados->get('local', 'W') === 'W') {
                $campanha->ica_ps_faixa = (int)$dados->get('faixaPeso');
                $campanha->ica_co_cpf_cnpj_cliente = $dados->get('cpf');
            } else {
                $campanha->ica_ps_individua = (int)$dados->get('peso', null);
                $campanha->ica_in_externa_interna = Servicos::CODIG_SERVICO_EXTERNA_INTERNA[$dados->get('servico')];
                $campanha->cpt_nu_cartao_postagem = $dados->get('cartaoPostagem', 9000);
                $campanha->atridfuncionario_cadastro = (int)$dados->get('atendente', 12345678);
            }
            $campanha->ica_dt_previsao_postagem = new Carbon($dados->get('dataEntregaCarga'), 'America/Sao_Paulo');
            $campanha->ica_in_entrega_urgente = $dados->get('urgente', 'N');
            $campanha->ica_in_ativo = $dados->get('ativo', 'S');
            $campanha->ica_qt_peca = (int)$dados->get('totalPecas', 0);
            $campanha->ica_in_local = $dados->get('local', 'W');
//            $campanha->ica_vr_total_campanha = $dados->get('precoTotal', 0.00);
            $campanha->jsitm_codigo_servico = $dados->get('servico');
            $sequence = DB::getSequence();
            $campanha->ica_nu_dpv = 1234;
            $campanha->save();
            $campanha->ica_nu_dpv = $campanha['ica_nu'];
            $campanha->save();

            foreach ($request->json('agencias') as $agencias) {
                foreach ($agencias['agencias'] as $agencia) {

                    $agenda = new AgendaCampanha();
                    $agenda->agc_dt_inicio = new Carbon($dados->get('dataInicioDistribuicao'), 'America/Sao_Paulo');
                    $agenda->agc_dt_fim = new Carbon($dados->get('datafimCampanha'), 'America/Sao_Paulo');
                    $agenda->ica_nu = $campanha->ica_nu;
                    $agenda->mcmcu_unidade_distribuicao = $agencia['codigo'];
                    $agenda->agc_qt_peca = $agencia['total_pecas'];
                    $agenda->save();
                }
            }

            $andamentoCampanha = new AndamentoCampanha();
            $andamentoCampanha->ACA_DT = new Carbon();
            $andamentoCampanha->ICA_NU = $campanha->ica_nu;
            $andamentoCampanha->TAN_NU = 1;
            $andamentoCampanha->save();

//            $campanha->andamentoCampanha()->attach(1, ['aca_dt' => new Carbon()]);

            DB::commit();
            $campanha->agendaAgencias;
            $campanha->andamentoCampanha;
            return response()->json($campanha);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }


    }

    public function calculaPreco(Request $request)
    {
        $faixaPeso = Precos::getFaixa($request->json('dados')['peso']);
        return ["local" => Precos::LOCAL[$faixaPeso], "estadual" => Precos::ESTADUAL[$faixaPeso], "nacional" => Precos::NACIONAL[$faixaPeso]];
    }

    public function conclusao($id)
    {
        $campanha = InformacaoCampanha::find($id);
        $campanha->agendaAgencias;
        $andamento = $campanha->andamentoCampanha()->orderBy('andamento_campanha.aca_dt', 'desc')->first();
        $campanha['andamento_campanha'] = $andamento;
        $campanha->segmentos;
        return response()->json($campanha);
    }

    public function validaPostagem(Request $request)
    {
        //TODO DIGITO
        $dpv = $request->input('dpv');
        $mcu = $request->input('mcu');

        $this->validate($request, [//                Valida o numero dpv, inclusive se o mesmo existe no banco
            'dpv' => 'required|min:6|exists:informacao_campanha,ica_nu_dpv', 'mcu' => 'required'], ['required' => 'O campo :attribute é obrigatório', 'min' => 'O campo :attribute deve conter ao menos :min caracteres', 'exists' => 'O campo :attribute não existe']);

//        Verifica se a unidade é apta a receber a carga
        $unidade = AgenciaApta::where(DB::raw("TRIM(mcmcu_unidade_operacional)"), 'like', trim($mcu))->where('uop_in_ativo', '=', 'S')->first();
//        Se a agencia não for apta
        if (empty($unidade)) {
//            Pega a agencia pelo mcu
            $agencia = $this->agencias()->get($mcu)->toCollection();
//            Busca as agencias na mesma localidade da unidade atual
            $agencias = $this->agencias()->listarAgenciasPorMunicipio($agencia->get('agencia')['endereco']['uf'], $agencia->get('agencia')['endereco']['municipio'])->toCollection()->get('listaAgencia');
//            Caso retorne uma única agencia
            if (isset($agencias['codigo'])) $agencias = [$agencias];
            $agencias = collect($agencias)//            Remove as agencias que não estão ativas no banco
            ->filter(function ($agencia) {
                return !empty(AgenciaApta::where(DB::raw("TRIM(mcmcu_unidade_operacional)"), 'like', trim($agencia['codigo']))->where('uop_in_ativo', '=', 'S')->first());
            })//                Retorna o nome da agencia e o tipo
            ->map(function ($agencia) {
                return implode(' - ', ['tipo' => 'TODO', 'nome' => trim($agencia['denominacao'])]);
            });

            $erromsg = 'Esta unidade não está apta a receber a postagem, favor encaminhar para uma das unidades a seguir: ';
            $erromsg .= $agencias->implode(', ');
//            Retorna a mensagem de erro
            return response()->json($erromsg, 422);
        }

//        Pega a campanha pelo numero dpv
        $campanha = InformacaoCampanha::where('ica_nu_dpv', '=', $dpv)->first();
//        Se a campanha está postada, retorna o erro
        $andamentoCampanha = AndamentoCampanha::where('ica_nu', '=', $campanha->toArray()['ica_nu'])->first()->toArray()['tan_nu'];
        if ($andamentoCampanha >= 3) return response()->json('Campanha já postada', 422);
//        Verifica se a campanha está no prazo de postagem
        $dataAndamento = Carbon::createFromFormat('d/m/Y', $campanha['ica_dt_previsao_postagem']);
        if (Carbon::now()->gt($dataAndamento)) return response()->json("Carga após o Prazo Previsto {$dataAndamento->format('d/m/Y')}. Abrir o sistema GeoMarketing para reagendamento.", 422);

//        Cria o array de retorno
        $res = collect(['pesoUnidade' => $campanha['ica_ps_faixa'] ?: $campanha['ica_ps_individual'], 'codigoServico' => $campanha['jsitm_codigo_servico'], 'cartaoPostagem' => $campanha['cpt_nu_cartao_postagem']]);

        $agSvc = $this->agencias();
//        Adiciona a abrangencia baseado na localidade de cada agencia
        $agencias = $campanha->agendaAgencias->map(function (AgendaCampanha $agenda) use ($agSvc, $unidade) {
            $unidade = $agSvc->get($unidade['mcmcu'])->toCollection()->get('agencia');
            $agencia = $agSvc->get($agenda['mcmcu'])->toCollection()->get('agencia');
            if ($unidade['endereco']['municipio'] == $agencia['endereco']['municipio']) $agenda['abrangencia'] = 'local'; else if ($unidade['endereco']['uf'] == $agencia['endereco']['uf']) $agenda['abrangencia'] = 'estadual'; else
                $agenda['abrangencia'] = 'nacional';

            return $agenda;
        });

//        Adiciona a qtd de peças com abrangencia local (da mesma cidade)
        $res->put('qtdLocal', $agencias->filter(function ($agencia) {
            return $agencia['abrangencia'] == 'local';
        })->sum('agc_qt_peca'));

//        Adiciona a qtd de peças com abrangencia estadual (do mesmo estado)
        $res->put('qtdEstadual', $agencias->filter(function ($agencia) {
            return $agencia['abrangencia'] == 'estadual';
        })->sum('agc_qt_peca'));

//        Adiciona a qtd de peças com abrangencia nacional (varios)
        $res->put('qtdNacional', $agencias->filter(function ($agencia) {
            return $agencia['abrangencia'] == 'nacional';
        })->sum('agc_qt_peca'));

//        Pega da tabela de parametos o peso maximo por peca
        $res->put('pesoMaxPeca', Parametro::get(Parametro::PESO_MAX_PECA));
        $total = $res['qtdLocal'] + $res['qtdEstadual'] + $res['qtdNacional'];
//        Calcula o total minimo e maximo de pecas baseado no servico
        if ($campanha['jsitm_codigo_servico'] == 2275 || $campanha['jsitm_codigo_servico'] == 2267) {
            $res->put('qtdMinimaPostagem', $total);
            $res->put('qtdMaximaPostagem', $total);
        } else {
            $qtdmin = $campanha['ica_qt_peca'] / 2;

            $res->put('qtdMinimaPostagem', $qtdmin > 999 ? $qtdmin : 1000);
            $res->put('qtdMaximaPostagem', Parametro::get('CMP'));
        }

        return response()->json($res);
    }

    //Detalhar campanha na tela 22A - Historia 55 - Sprint 7
    public function detalharCampanha($id)
    {
        $campanha = InformacaoCampanha::find($id);
        $localidade = LogLocalidade::where("loc_nu_sequencial", '=',  $campanha->loc_nu_sequencial)->first();
        $campanha->localidade = $localidade->loc_no;
        $campanha->agendaAgencias;

        return response()->json($campanha);
    }

    //funcao de teste
    public function teste($info){
        $contratoService = $this->contrato();
        return response()->json($contratoService->consultaClientePorCartaoDePostagem($info));
    }

}