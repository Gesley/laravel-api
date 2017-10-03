<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Correios\CorreiosServices;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use CorreiosServices;

    public function redirectCas(Request $request){
        return redirect()->to('https://apps.correios.com.br/cas/login?service=http://geomarketing.api/api/v1/login');
    }

    public function login(Request $request){
        return $request->all();

    }

    public function show(Request $request, $cpf)
    {
        $user = ['usuarioExterno' => ['nome' => 'Luis Fernando Meireles Arantes']];
        //return response()->json($user);
//        return response()->json($this->idAutorizadorUsuario()->getUserByCpf($cpf));
    }

}