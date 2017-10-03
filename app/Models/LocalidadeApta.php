<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:37
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LocalidadeApta extends Model
{
    protected $table = 'localidade_apta';
    protected $primaryKey = 'loc_nu_sequencial';

    public function agencias(){
        return $this->hasMany(\App\Models\AgenciaApta::class, 'loc_nu_sequencial');
    }

    public function localidade(){
        return $this->hasOne(\App\Models\LogLocalidade::class, 'loc_nu_sequencial');
    }
}