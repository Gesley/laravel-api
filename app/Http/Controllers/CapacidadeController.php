<?php
/**
 * Created by PhpStorm.
 * User: lhsantos3
 * Date: 13/09/2017
 * Time: 10:29
 */

namespace App\Http\Controllers;

use App\Models\AgenciaApta;
use App\Services\Correios\CorreiosServices;

class CapacidadeController extends Controller
{
    use CorreiosServices;

    public function listarCtcs(){

        $listCtcs = AgenciaApta::all()->where('drky_tipo_unidade_operacional', '=','CTC       ');

        //$listCtcs = AgenciaApta::all()->where('drky_tipo_unidade_operacional', '=','CTC       ');
        //$listactcs = AgenciaApta::where('drky_tipo_unidade_operacional', '=', 'ac')->get();

        return response()->json($listCtcs);
    }
}