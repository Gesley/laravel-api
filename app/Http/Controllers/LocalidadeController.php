<?php

namespace App\Http\Controllers;

use App\Enums\Localidade;
use App\Http\Controllers\Controller;
use App\Models\UnidadeOperacional as AgenciaApta;
use App\Models\LocalidadeApta;
use App\Models\LogLocalidade;
use App\Services\Correios\CorreiosServices;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocalidadeController extends Controller
{
    use CorreiosServices;

    public function uf()
    {
        $ufs = LocalidadeApta::select('ufe_sg')
            ->where('lap_in_ativo', '=', 'S')
            ->distinct()
            ->orderBy('ufe_sg', 'asc')
            ->get();

        return response()->json($ufs->map(function ($localidade) {
            return [
                'sigla' => $localidade['ufe_sg'],
                'descricao' => Localidade::ESTADOS[$localidade['ufe_sg']]
            ];
        }));
    }

    public function cidade(Request $request, $uf)
    {
        $localidade = LocalidadeApta::select('loc_nu_sequencial')
            ->where('ufe_sg', '=', strtoupper($uf))
            ->where('lap_in_ativo', '=', 'S')
            ->orderBy('loc_nu_sequencial', 'asc')
            ->get();

        return response()->json(
            $localidade->map(function ($local) {
                    //return $local->agencias()->where('aap_in_ativo', '=', 'S')->get();
                return $local->localidade;
            })
        );
    }

    /*
     * Consulta logradouros
     */
    public function bairro(Request $request, $uf, $localidade)
    {
        if(!$request->input('todos')):
            $localidade = $this->trataStrings($localidade);
        endif;
        
        $bairros = Cache::remember("$uf.$localidade{$request->input('todos', null)}", Carbon::now()->addDays(3), function() use($uf, $localidade, $request){
            $bairros = $this->logradouros()->get($uf, urldecode($localidade))->toArray();
            $bairros = $bairros['Localidades']['Localidade'];
            if (!is_array($bairros))
                $bairros = [];

            $agenciasService = $this->agencias();
            return collect($bairros)->filter(function ($bairro) use ($agenciasService, $uf, $localidade, $request) {
                if($request->input('todos', 'false') == 'true')
                    return true;
                $agencias = $agenciasService->listarAgenciasPorMunicipio($uf, self::trataStrings($localidade), self::trataStrings($bairro))->toArray();
                if (isset($agencias['listaAgencia'])) {
                    if (isset($agencias['listaAgencia']['codigo']))
                        $agencias['listaAgencia'] = [$agencias['listaAgencia']];
                    $agencias = collect($agencias['listaAgencia'])->filter(function ($agencia) {

//                        dd($agencia['codigo']);

                        $agencia = AgenciaApta::where('mcmcu_unidade_operacional', 'like', $agencia['codigo'])
//                            ->where('aap_in_ativo', '=', 'S')->first()
                    ;
                        return !empty($agencia);
                    })->toArray();
                }
                return !empty($agencias);
            })->toArray();
        });

        return response()->json(array_values($bairros));

//        $bairros = LogLocalidade::where('loc_nu_sequencial_sub', '=', $localidade)->get();
//        return response()->json($bairros);
    }

    public function trataStrings($string, $slug = false)
    {
        if(!is_Array($string)){
            $string = urldecode($string);

            $string = trim(strtolower($string));
            return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
        }
    }
}