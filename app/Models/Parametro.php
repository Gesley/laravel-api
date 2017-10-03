<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 22/05/2017
 * Time: 13:42
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametro';
    protected $primaryKey = 'par_sg';
    public $incrementing = false;
    public $timestamps = false;



}