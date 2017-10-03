<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 17/05/2017
 * Time: 16:20
 */

namespace App\Enums;


abstract class Servicos
{
    /**
     *
     * campanha não endereçada com distribuição interna = Mala direta perfil.
     * campanha não endereçada com distribuição externa = Mala direta perfil.
     * Correios Listas Distribuição  = Correios Listas Distribuição
     * Correios Listas Dist. Urgente  = Correios Listas Dist. Urgente
     */

    const MALA_DIRETA_PERFIL_EXT = 'CNEDE';
    const MALA_DIRETA_PERFIL_INT = 'CNEDI';
    const CORREIOS_LISTAS_DIST = 'CEDE';
    const CORREIOS_LISTAS_DIST_URGENTE = 'CEDEU';
    const COD_MALA_DIRETA_PERFIL_INTERNO = 2283;
    const COD_MALA_DIRETA_PERFIL_EXTERNO = 2259;
    const COD_CORREIOS_LISTAS_DIST = 2275;
    const COD_CORREIOS_LISTAS_DIST_URGENTE = 2267;



    const CODIGOS_SERVICOS = [
       2020 => 'CORREIOS LISTA DADOS',
       2259 => 'MALA DIRETA PERFIL EXTERNO',
       2267 => 'CORREIOS LISTA DISTR URGENTE',
       2275 => 'CORREIOS LISTA DISTRIBUICAO',
       2280 => 'MALA DIRETA PERFIL INTERNO',
    ];

    const CODIG_SERVICO_EXTERNA_INTERNA = [
        2259 => 'E',
        2267 => 'I',
        2275 => 'I',
        2280 => 'I',
    ];

    const CODIGOS_SERVICOS_DESCRICAO = [
        ['codigo' => 2020, 'descricaoServico' => 'CORREIOS LISTA DADOS'],
        ['codigo' => 2259, 'descricaoServico' => 'MALA DIRETA PERFIL EXTERNO'],
        ['codigo' => 2267, 'descricaoServico' => 'CORREIOS LISTA DISTR URGENTE'],
        ['codigo' => 2275, 'descricaoServico' => 'CORREIOS LISTA DISTRIBUICAO'],
        ['codigo' => 2280, 'descricaoServico' => 'MALA DIRETA PERFIL INTERNO'],
    ];
}