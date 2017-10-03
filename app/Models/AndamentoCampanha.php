<?php
/**
 * Created by PhpStorm.
 * User: luisq
 * Date: 27/07/2017
 * Time: 10:53
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AndamentoCampanha extends Model
{
    protected $table = 'andamento_campanha';
    protected $primaryKey = 'ica_nu';
    public $timestamps = false;
}