<?php

namespace App\Services\Correios\classes;

class Funcionario
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/funcionarioWS-v2.0.0/funcionario/funcionarioService?wsdl',
            true,
            [
                'login' => '13431',
                'password' => 'bauru123'
            ],
            [
                'classmap' => [
                    'consultaFuncionarioPorMatricula' => MapResponse::class,

                ]
            ]
        );
    }

    public function consultaFuncionarioPorMatricula($matricula)
    {
        return $this->service->soap->consultaFuncionarioPorMatricula(['matricual' => $matricula,]);
    }

}