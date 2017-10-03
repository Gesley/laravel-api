<?php

namespace App\Http\Controllers;
use App\Enums\Servicos;
use App\Services\Correios\classes\Localidade;
use App\models\LocalidadeApta as ModelLocalidade;
use App\Services\Correios\CorreiosServices;
use App\Models\ServicoLocalidade;
use Illuminate\Http\Request;

class ServicoLocalidadeController  extends Controller
{
    use CorreiosServices;

    /**
     * @param Request $request POST
     * @param string $sloAtendimento tipo de atendimento residencial ou comecrial
     * @param string $tServico tipo de serviço (m - mala direta perfil ou c - correios lista )
     */
    public function save(Request $request, $sloAtendimento, $tServico)
    {
        $localidade = collect($request->input('dados'));
        $localidade->each(function ($local) use($sloAtendimento, $tServico) {

            $sLocalidaeInterna = new ServicoLocalidade();
            $sLocalidaeExterna = new ServicoLocalidade();

            // Verifica se já tem no banco e insere de novo
            $exists = collect($sLocalidaeInterna::where('loc_nu_sequencial', '=', $local['loc_nu_sequencial'])->get());
            if(count($exists)){
                $exists->each(function($e){
                   $e->delete();
                });
            }

            // Cria dois registros para mala direta interna e externa /ou correios lista dist
            if($tServico == 'M'){
                $sLocalidaeInterna->loc_nu_sequencial = $local['loc_nu_sequencial'];
                $sLocalidaeInterna->slo_in_atendimento = $sloAtendimento;
                $sLocalidaeInterna->jsitm_codigo_servico = Servicos::COD_MALA_DIRETA_PERFIL_INTERNO;
                $sLocalidaeInterna->save();

                $sLocalidaeExterna->loc_nu_sequencial = $local['loc_nu_sequencial'];
                $sLocalidaeExterna->slo_in_atendimento = $sloAtendimento;
                $sLocalidaeExterna->jsitm_codigo_servico = Servicos::COD_MALA_DIRETA_PERFIL_EXTERNO;
                $sLocalidaeExterna->save();
            }else{
                $sLocalidaeInterna->loc_nu_sequencial = $local['loc_nu_sequencial'];
                $sLocalidaeInterna->slo_in_atendimento = $sloAtendimento;
                $sLocalidaeInterna->jsitm_codigo_servico = Servicos::COD_CORREIOS_LISTAS_DIST;
                $sLocalidaeInterna->save();

                $sLocalidaeExterna->loc_nu_sequencial = $local['loc_nu_sequencial'];
                $sLocalidaeExterna->slo_in_atendimento = $sloAtendimento;
                $sLocalidaeExterna->jsitm_codigo_servico = Servicos::COD_CORREIOS_LISTAS_DIST_URGENTE;
                $sLocalidaeExterna->save();
            }
        });
    }


    public function verificaServico($uf, $tipo)
    {
        // se for mala direta, not in correios lista distribuicao
        $wherein = ($tipo == 'M')? [Servicos::COD_CORREIOS_LISTAS_DIST, Servicos::COD_CORREIOS_LISTAS_DIST_URGENTE] : [Servicos::COD_MALA_DIRETA_PERFIL_INTERNO, Servicos::COD_MALA_DIRETA_PERFIL_EXTERNO];
        $servicoLocalidade = ServicoLocalidade::select('loc_nu_sequencial')->whereNotIn('jsitm_codigo_servico', $wherein)->get();

        $localidade = ModelLocalidade::select('loc_nu_sequencial')
            ->where('ufe_sg', $uf)
            ->whereNotIn('loc_nu_sequencial', $servicoLocalidade)
            ->get();

        return response()->json(
            $localidade->map(function ($local) {
                return $local->localidade;
            })
        );
    }

    public function verificaSelecionados($uf, $tipo)
    {
        $wherein = ($tipo == 'M')? [Servicos::COD_CORREIOS_LISTAS_DIST, Servicos::COD_CORREIOS_LISTAS_DIST_URGENTE] :
            [Servicos::COD_MALA_DIRETA_PERFIL_INTERNO, Servicos::COD_MALA_DIRETA_PERFIL_EXTERNO];

        $selecionados = ServicoLocalidade::leftJoin('localidade_apta', function($join) {
            $join->on('servico_localidade.loc_nu_sequencial', '=', 'localidade_apta.loc_nu_sequencial');
        })->where('localidade_apta.ufe_sg', '=', $uf)
            ->wherenotin('jsitm_codigo_servico', $wherein)
            ->get();

        $dados = collect($selecionados->unique('loc_nu_sequencial'));

        return response()->json(
            $dados->map(function ($local) {
                return $local->localidade;
            })
        );


    }

}