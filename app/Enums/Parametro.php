<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 12/06/17
 * Time: 12:59
 */

namespace App\Enums;


abstract class Parametro
{
    const
        QTD_MAXIMA_PECAS = 'QMP',
        QTD_CAMPANHAS_SIMULT = 'QCS',
        PESO_MAX_PECA = 'PMA';

    public static function get($parametro){
        return \App\Models\Parametro::find($parametro)['par_tx'];
    }

}