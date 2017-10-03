<?php
/**
 * Created by PhpStorm.
 * User: luisq
 * Date: 15/07/2017
 * Time: 12:13
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LogLocalidade extends Model
{
    protected $table = 'log_localidade';
    protected $primaryKey = 'loc_nu_sequencial';

    public function localidade_apta(){
        return $this->hasMany(\App\Models\LocalidadeApta::class);
    }
}