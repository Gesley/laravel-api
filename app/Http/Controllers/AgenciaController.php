<?php

namespace App\Http\Controllers;

use App\Enums\Parametro;
use App\Http\Controllers\Controller;
use App\Models\AgenciaAgenda;
use App\Models\UnidadeOperacional as AgenciaApta;
use App\Models\AgendaCampanha;
use App\Models\LocalidadeApta;
use App\Services\Correios\CorreiosServices;
use Carbon\Carbon;
use function FastRoute\TestFixtures\empty_options_cached;
use Illuminate\Database;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PDO;

class AgenciaController extends Controller
{
    use CorreiosServices;

    public function listaAgenciasAptas($loc_nu_sequencial)
    {
        $lista = collect(AgenciaApta::where('loc_nu_sequencial', '=', $loc_nu_sequencial)->get());
        foreach ($lista as $agencia) {
            $codigos[] = $agencia->mcmcu;
        };
        $agencias = $this->listarAgencias($codigos);
        return response()->json($agencias);
    }

    public function listarAgencias($codigos)
    {
        return response()->json($this->agencias()->listarAgencias($codigos));
    }

    public function get(Request $request, $agencia)
    {
        return response()->json($this->agencias()->get($agencia));
    }

    public function getAgenciaMunicipio(Request $request)
    {
        $uf = trim($request->input('uf'));
        $cidade = trim($request->input('cidade'));
        $bairro = trim($request->input('bairro'));

        return response()->json($this->agencias()->listarAgenciasPorMunicipio($uf, $cidade, $bairro));
    }

    public function listAgencias(Request $request)
    {
        $localidades = collect($request->json('localidades'));
        $dados = collect($request->json('dados'));


        $dtInicio = new Carbon(collect(explode('/', $dados['dataInicioDistribuicao']))->reverse()->implode('-'), 'America/Sao_Paulo');
        $dtFim = $dtInicio->copy()->addDays($dados['qtdDiasCampanha']);
        $agenciaService = $this->agencias();
        $localidadeService = $this->localidade();
        $agenciaInstance = $this;
        $localidades = $localidades->map(function ($localidade) use ($agenciaService, $localidadeService, $dados, $dtInicio, $dtFim, $agenciaInstance) {
//            $localTemp = $localidadeService->get($localidade['uf'])->toCollection();
//            $localTemp = isset($localTemp['Localidades']['Localidade']['localidade']) ? collect([$localTemp['Localidades']['Localidade']]) : collect($localTemp['Localidades']['Localidade']);

            $numeroLocalidade = rtrim($localidade['numeroLocalidade'], 1);
            $agencias = $this->agencias()->listarAgenciasPorMunicipio($localidade['uf'], $this->trataStrings($localidade['localidade']), $this->trataStrings($localidade['bairro']))->toArray();
//            $local = LocalidadeApta::where('loc_nu_sequencial', '=', $numeroLocalidade)->first();
            /*
             * @todo corrigir whre de quantidade
             */
//            $agencias = $local->agencias()->where('aap_qt_media_atendimento', '>', 0)->get();

//            $agencias = $local->agencias()->get();


            if(!isset($agencias['listaAgencia'])){
                $agencias['listaAgencia'] = $agencias;
            }

            $agencias = collect($agencias['listaAgencia'])->map(function ($agenciacol) use ($agenciaService, $dados, $dtInicio, $dtFim) {

                $agencia = AgenciaApta::where(
                    'mcmcu_unidade_operacional', 'like', $agenciacol['codigo'])->where('uop_in_ativo', 'like', 'S')
                    ->where('uop_qt_media_atendimento', '>', 0)
                    ->get()
                    ->first();

                if ($agencia == null) return null;
                if (!empty($agencia) && !isset($agencia['codigo']))
                    //$agenciacol = $agenciaService->listarAgenciasPorMunicipio()->toArray();

                    $agenciacol['total_pecas'] = floor($agencia['uop_qt_media_atendimento'] * $dados['qtdDiasCampanha']);

                if ($agencia->agenciaCampanha->isEmpty() || $agencia->agenciaCampanha()->campanhasSimultaneas($dtInicio, $dtFim)->count() < Parametro::get(Parametro::QTD_CAMPANHAS_SIMULT)) $agenciacol['disponibilidade'] = 'Total'; else {
                    $periodo = $agencia->agenciaCampanha()->campanhasSimultaneas($dtInicio, $dtFim)->get()->map(function ($agencia) use ($dtInicio, $dtFim) {
                        $agencia['diffIn'] = $dtInicio->diffInDays($agencia['agc_dt_inicio'], false);
                        $agencia['diffOut'] = $dtFim->diffInDays($agencia['agc_dt_fim'], false);
                        return $agencia;
                    });

                    $diffIn = $periodo->pluck('diffIn')->max();
                    $diffOut = $periodo->pluck('diffOut')->min();

                    if ($diffOut < 0) if ($diffOut * (-1) > $diffIn) {
                        $agenciacol['disponibilidade'] = 'A partir do dia ' . $dtFim->subDays($diffOut * (-1) - 1)->format('d/m/Y');
                        $agenciacol['total_pecas'] = floor($agencia['aap_qt_media_atendimento'] * $diffOut * (-1) - 1);
                    } else {
                        $agenciacol['disponibilidade'] = 'Até o dia ' . $dtInicio->addDays($diffIn - 1)->format('d/m/Y');
                        $agenciacol['total_pecas'] = floor($agencia['aap_qt_media_atendimento'] * $diffIn - 1);
                    } else {
                        $agenciacol['disponibilidade'] = 'Até o dia ' . $dtInicio->addDays($diffIn - 1)->format('d/m/Y');
                        $agenciacol['total_pecas'] = floor($agencia['aap_qt_media_atendimento'] * $diffIn - 1);
                    }

                }

                if ($agenciacol['total_pecas'] > Parametro::get(Parametro::QTD_MAXIMA_PECAS)) $agenciacol['total_pecas'] = (int)Parametro::get(Parametro::QTD_MAXIMA_PECAS);

                return $agenciacol;
            })->filter(function ($agencia) {
                return !empty($agencia);
            });
            $localidade['agencias'] = $agencias;
            return $localidade;
        });

        return response()->json($localidades);
    }

    public function listAgenciasPorBairro(Request $request)
    {
        $localidades = collect($request->json('localidades'));
        $dados = $request->json('dados');
        $dtInicio = new Carbon(collect(explode('/', $dados['dataInicioDistribuicao']))->reverse()->implode('-'), 'America/Sao_Paulo');
        $dtFim = $dtInicio->copy()->addDays($dados['qtdDiasCampanha']);
        foreach ($localidades as $local) {
            $bairro = $this->trataStrings($local['bairro']);
            $agencias[$local['bairro']] = $this->agencias()->listarAgenciasPorMunicipio($local['uf'], $this->trataStrings($local['localidade']), $bairro);
        }

        $dadosAgencia = [];
        foreach ($agencias as $ag) {
            foreach ($ag->listaAgencia as $a) {
                $bairro = $this->trataStrings($a->endereco->bairro);
                $mcmcu = trim($a->codigo);
                $dadosAgencia[trim(str_replace(" ", "", $a->endereco->bairro))][] = ['bairro' => $bairro, 'denominacao' => trim($a->denominacao), 'codigo' => $mcmcu, 'periodo' => 'total', 'total_pecas' => floor(AgenciaApta::whereRaw("TRIM(mcmcu) = '$mcmcu'")->first()['aap_qt_media_atendimento'] * $dados['qtdDiasCampanha'])];

            }
        }
        return response()->json($dadosAgencia);
    }

    public function agenciasPostagem(Request $request)
    {
        $localidade = collect($request->input('localidade'));
        $agencias = $this->agencias()->listarAgenciasPorMunicipio($localidade['uf'], $this->trataStrings($localidade['municipio']), $this->trataStrings($localidade['bairro']))->toArray();

        if (isset($agencias['listaAgencia']['codigo'])) $agencias['listaAgencia'] = [$agencias['listaAgencia']];

        $agencias = collect($agencias['listaAgencia'])->filter(function ($agenciacol) {
            $agencia = AgenciaApta::where('mcmcu', 'like', $agenciacol['codigo'])->where('aap_in_ativo', 'like', 'S')->get()->first();
            return $agencia;
        })->toArray();

        return response()->json(array_values($agencias));

    }

    public function trataStrings($string, $slug = false)
    {
        $string = trim(strtolower($string));
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    public function trataBairros($strBairro)
    {
        if (strpos($strBairro, '(')):
            $pl = strpos($strBairro, '(') + 1;
            $pr = strpos($strBairro, ')');
            $newStrBairro = substr($strBairro, 19, $pr - $pl);
            return $newStrBairro;
        endif;
    }
}