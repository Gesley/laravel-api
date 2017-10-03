<?php

namespace App\Services\Correios\classes;

class ClienteContrato
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/clienteContratoWS-v2/clienteContrato/clienteContratoService?wsdl',
            true,
            [
                'login' => '13431',
                'password' => 'bauru123'
            ],
            [
                'classmap' => [
                    'consultaContratoPorCartaoPostagemResponse' => MapResponse::class,
                    'consultaClientePorCartaoDePostagemResponse' => MapResponse::class,
                    'consultaClientePorCNPJResponse' => MapResponse::class,
                    'consultaCartoesPostagemDoContratoResponse' => MapResponse::class,
                    'consultaContatoDoClientePeloCodigoResponse' => MapResponse::class,
                    'consultaContratosDoClienteResponse' => MapResponse::class,
                    'consultaServicosDoContratoResponse' => MapResponse::class,
                    'consultaCartaoPostagemResponse' => MapResponse::class,
                    'consultaServicosDoCartaoPostagemResponse' => MapResponse::class,
                ]
            ]
        );
    }

    public function consultaServicosDoCartaoPostagem($cartao)
    {
        return $this->service->soap->consultaServicosDoCartaoPostagem(['numeroCartaoPostagem' => $cartao]);
    }

    public function consultaClientePorCartaoDePostagem($cartao){
        return $this->service->soap->consultaClientePorCartaoDePostagem(['cartaoDePostagem' => $cartao]);
    }


    public function consultaCartaoPostagem($cartao)
    {
        return $this->service->soap->consultaCartaoPostagem(['numeroCartaoPostagem' => $cartao]);
    }

    public function get($cartao)
    {
        return $this->service->soap->consultaContratoPorCartaoPostagem(['cartaoDePostagem' => $cartao]);
    }

    public function consultaClientePorCNPJ($cnpj)
    {
        return $this->service->soap->consultaClientePorCNPJ(['cnpj' => $cnpj]);
    }

    public function consultaCartoesPostagemDoContrato($contrato, $dr = null)
    {
        return $this->service->soap->consultaCartoesPostagemDoContrato([
            'numeroContrato' => $contrato,
            'DR' => $dr
        ]);
    }

    public function consultaContatoDoClientePeloCodigo($codigo)
    {
        return $this->service->soap->consultaContatoDoClientePeloCodigo([
            'codigoCliente' => $codigo
        ]);
    }

    public function consultaContratosDoCliente($cnpj)
    {
        return $this->service->soap->consultaContratosDoCliente(['cnpj' => $cnpj]);
    }

    public function consultaServicosDoContrato($numeroContrato, $dr = null)
    {
        return $this->service->soap->consultaServicosDoContrato([
            'numeroContrato' => $numeroContrato,
            'DR' => $dr
        ]);
    }

    public function consultaContratoPorCartaoPostagem($cartaoDePostagem)
    {
        return $this->service->soap->consultaContratoPorCartaoPostagem (['cartaoDePostagem' => $cartaoDePostagem ]);
    }

    public function consultaContratoValido($numeroContrato)
    {
        return $this->service->soap->consultaContrato(['numeroContrato' => $numeroContrato]);
    }

}