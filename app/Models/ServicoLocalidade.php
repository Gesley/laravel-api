<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:37
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ServicoLocalidade extends Model
{
    protected $table = 'servico_localidade';
    protected $primaryKey = 'loc_nu_sequencial';
    public $timestamps = false;


    public function localidade(){
        return $this->hasOne(\App\Models\LogLocalidade::class, 'loc_nu_sequencial');
    }
}