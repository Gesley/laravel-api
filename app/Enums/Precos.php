<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 14/06/17
 * Time: 14:32
 */

namespace App\Enums;


abstract class Precos
{
    const FAIXAS = [
        1 => "Até 20",
        2 => "Mais de 20 até 50",
        3 => "Mais de 50 até 100",
        4 => "Mais de 100 até 150",
        5 => "Mais de 150 até 200",
        6 => "Mais de 200 até 250",
        7 => "Mais de 250 até 300",
        8 => "Mais de 300 até 350",
        9 => "Mais de 350 até 400",
        10 => "Mais de 400 até 450",
        11 => "Mais de 450 até 500",
    ];
    const LOCAL = [
        1 => 0.22,
        2 => 0.27,
        3 => 0.33,
        4 => 0.41,
        5 => 0.51,
        6 => 0.63,
        7 => 0.79,
        8 => 0.97,
        9 => 1.21,
        10 => 1.50,
        11 => 1.86,
    ];

    const ESTADUAL = [
        1 => 0.23,
        2 => 0.29,
        3 => 0.36,
        4 => 0.45,
        5 => 0.55,
        6 => 0.68,
        7 => 0.85,
        8 => 1.05,
        9 => 1.31,
        10 => 1.62,
        11 => 2.01,
    ];

    const NACIONAL = [
        1 => 0.25,
        2 => 0.31,
        3 => 0.39,
        4 => 0.48,
        5 => 0.59,
        6 => 0.74,
        7 => 0.91,
        8 => 1.13,
        9 => 1.40,
        10 => 1.74,
        11 => 2.16,
    ];

    public static function getFaixa($peso){
        if($peso <= 20)
            return 1;
        if($peso <= 50)
            return 2;
        if($peso <= 100)
            return 3;
        if($peso <= 150)
            return 4;
        if($peso <= 200)
            return 5;
        if($peso <= 250)
            return 6;
        if($peso <= 300)
            return 7;
        if($peso <= 350)
            return 8;
        if($peso <= 400)
            return 9;
        if($peso <= 450)
            return 10;
        if($peso <= 500)
            return 11;
    }
}















