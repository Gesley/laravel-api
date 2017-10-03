<?php

namespace App\Services\Correios\classes;

class Agencias
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/unidadeNegocioWS-v2/agencia/agenciaService?wsdl',
            true,
            [
                'login' => '13431',
                'password' => 'bauru123'
            ],
            [
                'classmap' => [
                    'getAgenciaResponse' => MapResponse::class,
                    'listarAgenciasResponse' => MapResponse::class,
                    'listarAgenciasPorMunicipioResponse' => MapResponse::class,
                    'getTelefoneAgenciaResponse' => MapResponse::class
                ]
            ]
        );
    }

    public function get($agencia)
    {
        return $this->service->soap->getAgencia(['codigo' => $agencia]);
    }

    public function listarAgencias($codigos)
    {

        return $this->service->soap->listarAgencias(['listaCodigos' => $codigos]);
    }

    public function listarAgenciasPorMunicipio($uf, $cidade, $bairro = null)
    {
        return $this->service->soap->listarAgenciasPorMunicipio(['uf' => $uf, 'cidade' => $cidade, 'bairro' => $bairro]);
    }


}
