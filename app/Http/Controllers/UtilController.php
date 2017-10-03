<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 28/04/2017
 * Time: 12:43
 */

namespace app\Http\Controllers;

use App\Enums\Parametro;
use App\Enums\Precos;
use App\Enums\Segmentos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class UtilController extends Controller
{
    public function getQtdDiasCampanha(Request $request)
    {
        $dias = [];
        for ($i = 15; $i <= 60; $i++) {
            $dias[] = $i;
        }
        return response()->json($dias);
    }

    public function getFaixasPeso(Request $request)
    {
        return response()->json(collect(Precos::FAIXAS)->map(function($preco, $key){
            return ["descricao" => $preco, "id" => $key];
        })->values());
    }

    public function chancelas()
    {
        $file = storage_path('app/chancelas.jpg');
        if (file_exists($file)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            header("Content-Type: " . $type);
            readfile($file);
        }
    }

    public function orientacoes()
    {
        $file = storage_path('app/orientacoes.pdf');
        if (file_exists($file)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            header("Content-Type: " . $type);
            readfile($file);
        }
    }

    public function segmentos()
    {
        $segmentos = collect(DB::select('select pkg_dne.lista_segmento_cursor from dual'))->toArray();
        return response()->json($segmentos);
    }

    public function validaCupom(Request $request)
    {
        return $request->input('cupom');
    }

    public function procedure(){
        dd(DB::executeProcedure('geomkt.segmento_bairro_cursor', [
            'vnu_bai' => '25243',
            'vnu_seg' => '22'
        ]));
    }
}