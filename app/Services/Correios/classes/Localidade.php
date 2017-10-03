<?php

namespace App\Services\Correios\classes;

class Localidade
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
                    'listaMunicipioPorCodigoResponse' => MapResponse::class,
                    'consultaLocalidadesResponse' => MapResponse::class,
                    'consultaUfsResponse' => MapResponse::class,
                    'consultaCodigoMunicipioResponse' => MapResponse::class,
                    'consultaBairros' => MapResponse::class,
                ]
            ]
        );
    }


    public function get( $uf = null)
    {
        if(!$uf):
            return $this->service->soap->consultaUfs();
        else:
            return $this->service->soap->consultaLocalidades(['uf' => $uf]);
        endif;
    }

    public function listaMunicipioPorCodigo($codigo){
        return $this->service->soap->listaMunicipioPorCodigo(['codigoMunicipio' => $codigo]);
    }

    public function consultaCodigoMunicipio($uf, $municipio){
        return $this->service->soap->consultaCodigoMunicipio(['uf' => $uf, 'municipio' => $municipio]);
    }

    public function consultaBairros($uf, $localidade = null)
    {
        return $this->service->soap->consultaBairros(['uf' => $uf, 'localidade' => $localidade]);
    }

}