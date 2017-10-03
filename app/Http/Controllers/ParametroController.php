<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use App\Services\Correios\CorreiosServices;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    use CorreiosServices;

    public function listar(){
        $parametros = [];
        foreach (Parametro::all() as $parametro) {
            $parametros[strtolower($parametro['par_sg'])] = (int) $parametro['par_tx'];
        }

        return response()->json($parametros);
    }

    public function salvar(Request $request){
        $parametros = collect($request->input('parametros'));

        $parametros->each(function($parametro, $key){
            $param = Parametro::find(strtoupper($key));
            $param->par_tx = $parametro;
            $param->save();
        });
    }

}