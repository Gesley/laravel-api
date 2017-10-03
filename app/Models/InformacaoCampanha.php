<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:41
 */

namespace App\Models;


use App\Enums\Precos;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Database\Eloquent\Model;

class InformacaoCampanha extends Model
{
    protected $table = 'informacao_campanha';
    protected $primaryKey = 'ica_nu';
    public $sequence = 'sq_ica_nu';
    protected $fillable = [
        'ica_nu',
        'ica_dt_previsao_postagem',
        'ica_in_entrega_urgente',
        'ica_in_ativo',
        'ica_co_cpf_cnpj_cliente',
        'ica_in_local',
        'cpt_nu_cartao_postagem',
        'atridfuncionario_cadastro',
        'ica_nu_dpv',
        'ica_in_externa_interna',
        'ica_dt_geracao_dpv',
        'loc_nu_sequencial',
        'mcmcu_unidade_postagem',
        'ica_qt_peca',
        'ica_ps_faixa',
        'ica_ps_individua',
        'ica_no_cliente',
        'jsitm_codigo_servico'
    ];

    protected $dates = ['ica_dt_previsao_postagem'];

    public $timestamps = false;

    public function agendaAgencias()
    {
        return $this->hasMany(\App\Models\AgendaCampanha::class, 'ica_nu', 'ica_nu');
    }

    public function segmentos()
    {
        return $this->hasMany(\App\Models\SegmentoCampanha::class, 'ica_nu');
    }

    public function andamentoCampanha()
    {
        return $this->belongsToMany(\App\Models\TipoAndamento::class, 'andamento_campanha', 'ica_nu', 'tan_nu')
            ;
    }

    public function scopeFiltering($query, $filter)
    {
        if (isset($filter['cupomInicio']))
            $query->where('ica_nu_dpv', '>=', $filter['cupomInicio']);
        if (isset($filter['cupomFinal']))
            $query->where('ica_nu_dpv', '<=', $filter['cupomFinal']);

        if (isset($filter['cartaoInicio']))
            $query->where('cpt_nu_cartao_postagem', '>=', $filter['cartaoInicio']);
        if (isset($filter['cartaoFinal']))
            $query->where('cpt_nu_cartao_postagem', '<=', $filter['cartaoFinal']);

        if (isset($filter['quantidadeinicio']))
            $query->where('ica_qt_peca', '>=', $filter['quantidadeinicio']);
        if (isset($filter['quantidadeFinal']))
            $query->where('ica_qt_peca', '<=', $filter['quantidadeFinal']);

        if(isset($filter['tipo']) && $filter['tipo'] == 'NAO_ENDERECADA')
            $query->whereIn('jsitm_codigo_servico', [2275, 2267]);

        if(isset($filter['tipo']) && $filter['tipo'] == 'ENDERECADA')
            $query->whereIn('jsitm_codigo_servico', [2259]);

        if(isset($filter['urgencia']))
            $query->where('ica_in_entrega_urgente', '=', $filter['urgencia']);
//        if ($filter['dataCupomFinal'] > 0)
//            $query->whereBetween('ica_nu_dpv', [$filter['dataCupomInicio'], $filter['dataCupomFinal']]);



        return $query;
    }

    public function getIcaDtPrevisaoPostagemAttribute($dt)
    {
        return Carbon::parse($dt)->format('d/m/Y');
    }

    public function getIcaVrTotalCampanhaAttribute($vr)
    {
        return "R$ " . number_format($vr, '2', ',', '.');
    }

    public function getIcaNuDpvAttribute($value){
        $dpv = str_pad((int) $value, 6, '0', STR_PAD_LEFT);
        $dpvArr = str_split($dpv);
        $sum = [];
        for($i = count($dpvArr); $i > 0; $i--){
            $sum[] = $dpv[$i-1] + $i;
        }
        $sum = array_sum($sum);
        $dv = 11 - ($sum % 11);
        if($dv == 10 || $dv == 11)
            $dv = 0;
        return $dpv.'-'.$dv;
    }

    public function setIcaNuDpvAttribute($value){
        $dpv = str_pad((int) $value, 6, '0', STR_PAD_LEFT);
        return $dpv;
    }
}