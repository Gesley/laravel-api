<?php

namespace App\Services\Correios\classes;


class Cas
{
    private $service;

    public function __construct()
    {
        $this->service = new Service(
            'https://jhom2.correiosnet.int/cas/proxyValidate',
            true,
            [
                'login' => '15371',
                'password' => 'g1e2o3m4k5t'
            ]
        );
    }


    public function check()
    {
        dd($this->service->soap);
    }
}