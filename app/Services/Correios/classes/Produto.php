<?php

namespace App\Services\Correios\classes;

class Produto
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/precoprazoservice/precoprazo/precoprazows?wsdl',
            true,
            [
                'login' => '13431',
                'password' => 'bauru123'
            ],
            [
                'classmap' => [
                    'listaPrecosResponse' => MapResponse::class,

                ]
            ]
        );
    }


    public function listaPrecos($codigo, $cepOrigem, $cepDestino, $peso, $unidade = null, $vl = null)
    {
        return $this->service->soap->consultaPreco([
            'coProduto' => $codigo,
            'cepOrigem' => $cepOrigem,
            'cepDestino' => $cepDestino,
            'psObjeto' => $peso,
            'nuUnidade' => $unidade,
            'vlRemessa' => $vl
        ]);
    }

}