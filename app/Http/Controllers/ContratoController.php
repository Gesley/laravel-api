<?php
namespace App\Http\Controllers;

use App\Enums\Servicos;
use App\Http\Controllers\Controller;
use App\Services\Correios\CorreiosServices;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Carbon\Carbon;
use Illuminate\Http\Request;
USE App\Enums;

class ContratoController extends Controller
{
    use CorreiosServices;

    /**
     * Use the SoapWrapper
     */

    public function show(Request $request, $contrato)
    {
        $cartoes = $this->contrato()->consultaCartoesPostagemDoContrato($contrato);
        return response()->json($cartoes->listaDeCartoesPostagem);

    }

    public function cnpj(Request $request, $cnpj)
    {
        return response()->json($this->contrato()->consultaServicosDoContrato($cnpj)->toArray());
    }

    public function consultaContrato(Request $request, $contrato)
    {
        return response()->json($this->contrato()->consultaServicosDoContrato($contrato)->toArray());
    }

    public function consultaValidadeContrato($cartao)
    {
        $nContrato = trim($this->contrato()->consultaContratoPorCartaoPostagem($cartao)->contrato->numeroContrato);
        $contrato = $this->contrato()->consultaContratoValido($nContrato);
        $status = trim($contrato->listaDeContratos->descricaoStatus);
        $hoje = $now = Carbon::today();
        $fimVigencia = new Carbon($contrato->listaDeContratos->fimVigencia);
        /* gt greater than */
        $validade = $fimVigencia->gt($hoje);
        $servicos = ($this->consultaServicosCartao($cartao)) ? true : false;

        return response()->json([
            'numeroContrato' => $nContrato,
            'possueServicos' => $servicos,
            'numeroCartao' => $cartao,
            'contratoValido' => $validade,
            'situacao' => $status
        ]);
   }

    public function consultaContratoPorCartaoPostagem($cartaoDePostagem)
    {
        return response()->json($this->contrato()->consultaContratoPorCartaoPostagem($cartaoDePostagem));
    }

    public function consultaServicosCartao($cartao)
    {
        $dados = $this->contrato()->consultaServicosDoCartaoPostagem($cartao)->listaDeServicos;

        $servicos = [];
        if(!is_array($dados)):
            $servicos[] = trim($dados->codigo);
        else:
            foreach($dados as $s):
                $servicos[] = trim($s->codigo);
            endforeach;
        endif;

        foreach($servicos as $s){
            if(in_array($s, Servicos::CODIGOS_SERVICOS)){
                $servicosHablitados[] = $s;
            }
        }

        if(empty($servicosHablitados))
            return [];

        return  response()->json($this->contrato()->consultaServicosDoCartaoPostagem($cartao));
    }



}