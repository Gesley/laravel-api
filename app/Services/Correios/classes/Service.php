<?php

namespace App\Services\Correios\classes;

class Service
{
    private
        $wsdl,
        $trace = true,
        $credentials = [],
        $options = [];

    public function __construct($wsdl, $trace = true, $credentials = [], $options = [])
    {
        try {
            $defaultOpts = [
                'login' => $credentials['login'],
                'password' => $credentials['password'],
                'trace' => $trace,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        // set some SSL/TLS specific options
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ])
            ];

            $this->soap = new \SoapClient($wsdl, array_merge($defaultOpts, $options));

        } catch (\Exception $ex) {
            throw new \Exception('Não foi possíver iniciar o serviço. ' . $ex->getMessage(), 0, $ex);
        }
    }
}