<?php

namespace App\Services\Correios\classes;

class Campanha
{

    public function __construct()
    {
        $this->service = new Service(
            'http://cnsdesenv/calculador/CalcPrecoPrazo.asmx?wsdl',
            true,
            [
                'login' => '13431',
                'password' => 'bauru123'
            ],
            [
                'classmap' => [
                    'CalcPrecoResponse' => MapResponse::class,
                    'CalcPrecoDataResponse' => MapResponse::class,
                    'CalcPrecoPrazoResponse' => MapResponse::class,
                    'CalcPrecoPrazoDataResponse' => MapResponse::class,
                    'CalcPrecoPrazoRestricaoResponse' => MapResponse::class,
                    'CalcPrazoResponse' => MapResponse::class,
                    'CalcPrazoDataResponse' => MapResponse::class,
                    'CalcPrazoRestricaoResponse' => MapResponse::class,
                    'CalcPrecoFACResponse' => MapResponse::class,
                    'CalcPrecoFACResponse' => MapResponse::class
                ]
            ]
        );
    }

    public function calcPrazo($nCdServico, $sCepOrigem, $sCepDestino)
    {
        return $this->service->soap->CalcPrazo ([
            'nCdServico' => $nCdServico, 'sCepOrigem' => $sCepOrigem, 'sCepDestino' => $sCepDestino
        ]);
    }
}