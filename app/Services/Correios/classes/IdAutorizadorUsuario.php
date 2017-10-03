<?php

namespace App\Services\Correios\classes;

class IdAutorizadorUsuario
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/idCorreiosWS-v1.2/idAutorizadorUsuario/idAutorizadorUsuarioService?wsdl',
            true,
            [
                'login' => '15371',
                'password' => 'g1e2o3m4k5t',
            ],
            [
                'classmap' => [
//                    'getUsuarioExternoAtivoPeloCPF' => Map::class,
//                    'getUsuarioExternoAtivoPeloCNPJ' => Map::class,
                    'getUsuarioExternoAtivoPeloCPFResponse' => MapResponse::class,
                    'getUsuarioExternoAtivoPeloCNPJResponse' => MapResponse::class,
                    'getUsuarioInternoAtivoPelaMatriculaResponse' => MapResponse::class,
                ]
            ]
        );
    }


    public function getUserByCpf($cpf)
    {
        return $this->service->soap->getUsuarioExternoAtivoPeloCPF(['cpf' => $cpf]);
    }

    public function getUserByCnpj($cnpj)
    {
        return $this->service->soap->getUsuarioExternoAtivoPeloCNPJ(['cnpj' => $cnpj]);
    }

    public function getUserByCpfCnpj($cpfCnpj)
    {
        if (strlen($cpfCnpj) == 11)
            return $this->service->soap->getUsuarioExternoAtivoPeloCPF(['cpf' => $cpfCnpj]);
        else if (strlen($cpfCnpj) == '14')
            return $this->service->soap->getUsuarioExternoAtivoPeloCNPJ(['cnpj' => $cpfCnpj]);
        throw new \Exception('CPF ou CNPJ inválido');
    }

    public function getUsuarioPFAutorizadoNoSistema($idCorreiosPJ = null, $idCorreiosPF = null){
        return $this->service->soap->getUsuarioPFAutorizadoNoSistema([
            'idCorreiosPJ' => $idCorreiosPJ,
            'idCorreiosPF' => $idCorreiosPF]);
    }

    public function getUsuarioInternoAtivoPelaMatricula($login){
        return $this->service->soap->getUsuarioInternoAtivoPelaMatricula(['matricula' => $login]);
    }
}