<?php
namespace App\Services\Correios;

use App\Services\Correios\classes\Cas;
use App\Services\Correios\classes\Cep;
use App\Services\Correios\classes\Funcionario;
use App\Services\Correios\classes\IdAutorizadorUsuario;
use App\Services\Correios\classes\Idcorreiosws;
use App\Services\Correios\classes\ClienteContrato;
use App\Services\Correios\classes\Localidade;
use App\Services\Correios\classes\Logradouros;
use App\Services\Correios\classes\Agencias;
use App\Services\Correios\classes\Produto;
use App\Services\Correios\classes\Campanha;
use App\Services\Correios\classes\UnidadeNegocio as Unidade;
use App\Services\Correios\classes\UnidadeNegocio;

trait CorreiosServices
{
    public function cep(){
        return new Cep();
    }

    public function idAutorizadorUsuario()
    {
        return new IdAutorizadorUsuario();
    }
    public function cpf(){
    	return new Idcorreiosws();
    }

    public function contrato(){
    	return new ClienteContrato();
    }

    public function localidade(){
		return new Localidade();
    }

    public function logradouros(){
    	return new Logradouros();
    }

    public function cas(){
        return new Cas();
    }

    public function agencias(){
        return new Agencias();
    }

    public function produtos(){
        return new Produto();
    }

    public function campanha(){
        return new Campanha();
    }

    public function unidade(){
        return new Unidade();
    }

    public function funcionario(){
        return new Funcionario();
    }


}