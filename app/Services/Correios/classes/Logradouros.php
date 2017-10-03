<?php

namespace App\Services\Correios\classes;

class Logradouros   
{
    private $service;
    
    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/localidadeWS/Localidade/LocalidadeService?wsdl',
            true,
            [
                'login' => '15371',
                'password' => 'g1e2o3m4k5t'
            ],
            [
                'classmap' => [
                    'consultaBairrosResponse' => MapResponse::class
                ]
            ]
        );
    }


    public function get( $uf, $localidade )
    {   
        return $this->service->soap->consultaBairros(['uf' => $uf, 'localidade' => $localidade]);
    }
}