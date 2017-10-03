<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Correios\CorreiosServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Support\Facades\DB;
use App\Enums\Servicos;


class UserController extends Controller
{
    use CorreiosServices;

    public function getUser(Request $request, $cpfCnpj)
    {

        $servicosPadrao = [
            'MALA DIRETA PERFIL' => true,
            'CORREIOS LISTAS – DISTRIBUIÇÃO URGENTE' => false,
            'CORREIOS LISTAS – DISTRIBUIÇÃO' => false
        ];

        if (Cache::has($cpfCnpj)) {
            $usuario = Cache::get($cpfCnpj);
        } else {
            $usuario = $this->idAutorizadorUsuario()
                ->getUserByCpfCnpj($cpfCnpj)
                ->toCollection()
                ->first();
            if ($usuario['tipo'] == 2) {
                $contrato = $this->contrato()->consultaContratosDoCliente($cpfCnpj)->toCollection();
                $servicos = $this->contrato()
                    ->consultaServicosDoContrato($contrato->get('listaDeContratos')['numero'])
                    ->toCollection()->first();

                $servicos = collect($servicos)->filter(function ($servico) {
                    return trim($servico['codigo']) == 15371;
                })->map(function ($servico) {
                    return trim($servico['descricaoServico']);
                })
                    ->values()
                    ->map(function ($servico) {
                        return [$servico => true];
                    });

            } else {
                $servicos = collect($servicosPadrao);
            }

            $usuario = collect($usuario)
                ->put('servicos', $servicos)
//                ->put('contrato', $contrato)
                ->toArray();
            Cache::put($cpfCnpj, $usuario, Carbon::now()->addDays(5));
        }

        if (!empty($usuario['servicos']))
            return response()->json($usuario);

        return response()->json([
            'message' => 'Nenhum serviço disponível',
        ], 400);
    }

    public function validateToken(Request $request)
    {
        $parametros = http_build_query([
            'service' => $request->input('service', 'localhost:3000'),
            'ticket' => $request->input('ticket')
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "cURL");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://jhom2.correiosnet.int/cas/proxyValidate?' . $parametros);
        curl_setopt($ch, CURLOPT_POST, false);
        $xml = curl_exec($ch);
        curl_close($ch);
        preg_match_all('/<cas:([^>]+)>([^<]+)<\/cas:[^>]+>/', $xml, $out);
        if (strpos($out[0][0], 'INVALID_TICKET'))
            return response()->json('invalid_ticket', 400);
        $cas = [];
        foreach ($out[1] as $i => $chave) {
            $cas[$chave] = $out[2][$i];
        }
        return response()->json($cas);
    }

    public function consultaServicosCartao(Request $request)
    {
        $cartao = $request->input('numeroCartao', null);
        $cpfCnpj = $request->input('identificacao', null);
        $servicos = Servicos::CODIGOS_SERVICOS_DESCRICAO;

        if(!empty($cartao) && !empty($cpfCnpj)){
            $usuario = $this->idAutorizadorUsuario()
                ->getUserByCpfCnpj($cpfCnpj)
                ->toCollection()->first();
            $servicos = collect($this->contrato()
                ->consultaServicosDoCartaoPostagem("$cartao")
                ->toCollection()->get('listaDeServicos', []));

            if(isset($servicos['codigo']))
                $servicos = collect([$servicos]);

            $servicos = $servicos->map(function($servico){
                $servico['codigo'] = (int) trim($servico['codigo']);
                $servico['descricaoServico'] = trim($servico['descricaoServico']);
                return $servico;
            })->filter(function($servico){
                $out = false;
                foreach (Servicos::CODIGOS_SERVICOS_DESCRICAO as $serv){
                    if($serv['codigo'] == $servico['codigo'])
                        $out = true;
                }
                return $out;
            });
        }
        else{
            $usuario = $this->idAutorizadorUsuario()
                ->getUserByCpfCnpj($cpfCnpj)
                ->toCollection()->first();

            $servicos = Servicos::CODIGOS_SERVICOS_DESCRICAO;
        }

        return response()->json([
            'usuario' => $usuario,
            'servicos' => $servicos
        ]);
    }

    public function consultaCartaoPostagem($cartao)
    {
        return response()->json($this->contrato()->consultaCartaoPostagem($cartao));
    }

    public function tokenRedirect(Request $request)
    {
        $token = $this->validateToken($request->input('ticket', null));

        return redirect('http://localhost:3000/login/' . $token['user']);
    }

    public function getCartoesPostagem(Request $request, $contrato, $dr)
    {
        return $this->contrato()->consultaCartoesPostagemDoContrato($contrato, $dr);
    }

    public function consultaFuncionarioPorMatricula($matricula)
    {
        $funcionario = collect($this->funcionario()->consultaFuncionarioPorMatricula($matricula))->toArray();
        dd($funcionario);
        $pessoa = collect($funcionario['return']->pessoa)->toArray();
        $localidade = collect($this->unidade()->getEnderecoUnidadeNegocio(trim($funcionario['return']->lotacaoFisica->codigo)))->toArray()['endereco'];
        $resultado = [$pessoa, $localidade];
        return response()->json($resultado);
    }


}