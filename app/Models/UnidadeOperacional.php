<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UnidadeOperacional extends Model
{
    protected $table = "unidade_operacional";
    protected $primaryKey = "mcmcu_unidade_operacional";

    public function agenciaCampanha(){
        return $this->hasMany(\App\Models\AgendaCampanha::class, 'mcmcu_unidade_distribuicao',
            'mcmcu_unidade_operacional');
    }

    public function getAapSgUopAttribute($sg){
        if($sg == 'AC')
            return 'Agência';
        return 'CTC';
    }

}