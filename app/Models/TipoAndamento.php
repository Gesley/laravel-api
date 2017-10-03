<?php
/**
 * Created by PhpStorm.
 * User: luisq
 * Date: 27/07/2017
 * Time: 10:53
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TipoAndamento extends Model
{
    protected $table = 'tipo_andamento';
    protected $primaryKey = 'tan_nu';
    public $timestamps = false;
}