<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:46
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;

class AgendaCampanha extends Model
{
    protected $table = 'agenda_campanha';
    protected $primaryKey = 'ica_nu';
    protected $dates = ['agc_dt_inicio', 'agc_dt_fim'];
    protected $fillable = [
        'agc_dt_inicio',
        'agc_dt_fim',
        'ica_nu',
        'agc_qt_peca',
        'mcmcu_unidade_distribuicao'
    ];
    protected $casts = [
        'mcmcu_unidade_distribuicao' => 'string'
    ];
    public $timestamps = false;

    public function scopeCampanhasSimultaneas($query, Carbon $dtInicio, Carbon $dtFim)
    {
        return $query->where(function ($query) use ($dtInicio, $dtFim) {
            $query->where(function ($query) use ($dtInicio, $dtFim) {
                $query->whereBetween('agc_dt_inicio', [
                    $dtInicio->format('Y-m-d'),
                    $dtFim->format('Y-m-d')
                ]);
            });
            $query->orWhere(function ($query) use ($dtInicio, $dtFim) {
                $query->whereBetween('agc_dt_fim', [
                    $dtInicio->format('Y-m-d'),
                    $dtFim->format('Y-m-d')
                ]);
            });
            $query->orWhere(function ($query) use ($dtInicio) {
                $query->whereBetween(DB::raw("'{$dtInicio->format('Y-m-d')}'"), [
                    'agc_dt_inicio',
                    'agc_dt_fim'
                ]);
            });
            $query->orWhere(function ($query) use ($dtFim) {
                $query->whereBetween(DB::raw("'{$dtFim->format('Y-m-d')}'"), [
                    'agc_dt_inicio',
                    'agc_dt_fim'
                ]);
            });
        });
    }

    public function informacaoCampanha(){
        return $this->belongsTo(\App\Models\InformacaoCampanha::class, 'ica_nu', 'ica_nu');
    }
}