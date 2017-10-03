<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentoCampanha extends Model
{
    protected $table = 'segmento_campanha';
    protected $primaryKey = 'sca_nu';
    public $timestamps = false;
}