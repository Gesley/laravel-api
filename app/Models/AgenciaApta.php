<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AgenciaApta extends Model
{
    protected $table = "unidade_operacional";
    protected $primaryKey = "loc_nu_sequencial";

    public function agenciaCampanha(){
        return $this->hasMany(\App\Models\AgendaCampanha::class, 'mcmcu_unidade_operacional', 'mcmcu_unidade_distribuicao');
    }

    public function getAapSgUopAttribute($sg){
        if($sg == 'AC')
            return 'Agência';
        return 'CTC';
}

}