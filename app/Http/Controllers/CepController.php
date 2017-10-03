<?php

namespace App\Http\Controllers;

use App\Services\Correios\CorreiosServices;
use Illuminate\Http\Request;

class CepController extends Controller
{
    use CorreiosServices;

    public function show(Request $request, $cep)
    {
        return response()->json($this->cep()->get($cep));
    }

    public function cpf(Request $request, $cpf)
    {
        return response()->json($this->idAutorizadorUsuario()->getUserByCpfCnpj($cpf));
    }
}