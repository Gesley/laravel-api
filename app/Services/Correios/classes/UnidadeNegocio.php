<?php

namespace App\Services\Correios\classes;

class UnidadeNegocio
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/unidadeNegocioWS-v2/unidadeNegocio/unidadeNegocioService?wsdl',
            true,
            [
                'login' => '15371',
                'password' => 'g1e2o3m4k5t',
            ],
            [
                'classmap' => [
                    'getUnidadeNegocioPorCEP' => MapResponse::class,
                    'getUnidadeNegocioPorCEPResponse' => MapResponse::class,
                ]
            ]
        );
    }

    public function getUnidadeNegocioPorCEP($cep)
    {
        return $this->service->soap->getUnidadeNegocioPorCEP(['cep' => $cep]);
    }

    public function getEnderecoUnidadeNegocio($codigoUnidade)
    {
        return $this->service->soap->getEnderecoUnidadeNegocio(['codigoUnidade' => $codigoUnidade]);
    }

}