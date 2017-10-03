<?php

namespace App\Http\Controllers;

use App\Models\Segmento;
use App\Models\SegmentoCampanha;
use App\Services\Correios\CorreiosServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegmentoController extends Controller
{
    use CorreiosServices;

    public function save(Request $request){
        $newSegmento = $request->input('segmento');

        $segmentosDNE = $this->get();
        $segmentos = [];
        foreach($segmentosDNE->original as $seg){
            $segmentos[$seg->segnu] = $seg->segtx;
        }

        if( in_array($newSegmento, $segmentos)){
            return 'segmento já existe, segmento ++';
        }

        $objNovoSegmento = new SegmentoCampanha();
        $objNovoSegmento->SOC_NO = $newSegmento;
        $objNovoSegmento->SOC_QT_OCORRENCIA = 1;

        dd( $objNovoSegmento->save() );

    }

    public function get()
    {
        $segment = collect(DB::select('select pkg_dne.lista_segmento_cursor from dual'))->toArray();
        return response()->json($segment);
    }

}