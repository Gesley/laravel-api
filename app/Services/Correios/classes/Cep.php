<?php

namespace App\Services\Correios\classes;

class Cep
{
    private $service;
    
    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/cepWS-v2/cep/cepService?wsdl',
            true,
            [
                'login' => '15371',
                'password' => 'g1e2o3m4k5t'
            ],
            [
                'classmap' => [
                    'getDadosCepResponse' => MapResponse::class,
                    'consultaCepsPorEnderecoResponse' => MapResponse::class
                ]
            ]
        );
    }


    public function get($cep)
    {
        return $this->service->soap->getDadosCep(['cep' => $cep]);
    }

    public function getPorEndereco($endereco)
    {
        return $this->service->soap->consultaCepsPorEndereco(['endereco' => $endereco]);
    }
}