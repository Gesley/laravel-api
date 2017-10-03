<?php

$app->get('/', function () use ($app) {
    return view('index');
});

$app->group(['prefix' => 'api'], function (\Laravel\Lumen\Application $app) {

    $app->group(['prefix' => 'v1'], function (\Laravel\Lumen\Application $app) {

        $app->group(['prefix' => 'agencia'], function (\Laravel\Lumen\Application $app) {
            $app->get('/listar-agencias/{codigo}', 'AgenciaController@get');
                $app->post('/postagem', 'AgenciaController@agenciasPostagem');
            $app->post('/listar-agencias-municipio', 'AgenciaController@getAgenciaMunicipio');
            $app->post('/por-localidade', 'AgenciaController@listAgencias');
            $app->post('/por-bairro', 'AgenciaController@listAgenciasPorBairro');
            $app->get('/codigo/{codigo}', 'AgenciaController@get');
            $app->post('/teste', 'AgenciaController@get');
            $app->get('/listar-agencias-aptas/{loc_nu_sequencial}', 'AgenciaController@listaAgenciasAptas');

        });

        $app->group(['prefix' => 'campanha'], function (\Laravel\Lumen\Application $app) {
            $app->post('/listar', 'CampanhaController@listar');
            $app->post('/listar-cargas', 'CampanhaController@listarCargas');
            $app->post('/atualizar-campanha/{id}', 'CampanhaController@atualizar');
            $app->post('/calculaprazo', 'CampanhaController@calculaPrazo');
            $app->post('/salvar', 'CampanhaController@salvar');
            $app->get('/getcepporlocal', 'CampanhaController@show');
            $app->post('/calcula-preco', 'CampanhaController@calculaPreco');
            $app->get('/conclusao/{id}', 'CampanhaController@conclusao');
            $app->get('/detalhar-campanha/{id}', 'CampanhaController@detalharCampanha');
            $app->get('/valida-cupom/{id}', 'CampanhaController@validaCupom');
            $app->post('/calcular-dados', 'CampanhaController@calcularDados');
            $app->post('/valida-postagem', 'CampanhaController@validaPostagem');
            $app->post('/detalhamento-campanha/{id}', 'CampanhaController@detalharCampanha');
            //ATENCAO - DELETAR FUNCAO DE TESTE
            $app->get('/teste/{info}', 'CampanhaController@teste');


        });

        $app->group(['prefix' => 'cep'], function (\Laravel\Lumen\Application $app) {
            $app->get('/search/{cep: [0-9]+}', 'CepController@show');
        });

        $app->group(['prefix' => 'usuario'], function (\Laravel\Lumen\Application $app) {
            $app->get('/cartoes-postagem/{cartao}/{dr}', 'UserController@getCartoesPostagem');
            $app->get('/consulta-cartao/{cartao}', 'UserController@consultaCartaoPostagem');
            $app->post('/lista-servicos-cartao', 'UserController@consultaServicosCartao');
            $app->post('/validate-token', 'UserController@validateToken');
            $app->get('/{cpfcnpj}', 'UserController@getUser');
            $app->get('/matricula/{matricula}', 'UserController@consultaFuncionarioPorMatricula');
        });

        $app->group(['prefix' => 'atendente'], function (\Laravel\Lumen\Application $app) {
            $app->get('/matricula/{matricula}', 'UserController@consultaFuncionarioPorMatricula');
        });

        $app->group(['prefix' => 'contrato'], function (\Laravel\Lumen\Application $app) {
            $app->get('/cartoes/{contrato}', 'ContratoController@show');
            $app->get('/consultacontrato/{contrato}', 'ContratoController@consultaContrato');
            $app->get('/cnpj/{cnpj}', 'ContratoController@cnpj');
            $app->get('/cartao-postagem/{cartao}', 'ContratoController@consultaContratoPorCartaoPostagem');
            $app->get('/validade-contrato/{contrato}', 'ContratoController@consultaValidadeContrato');
        });

        $app->group(['prefix' => 'localidade'], function (\Laravel\Lumen\Application $app) {
            $app->get('/uf/', 'LocalidadeController@uf');
            $app->get('/uf/{uf}', 'LocalidadeController@cidade');
            $app->get('/consulta-bairros/uf/{uf}[/bairro/{bairro}]', 'LocalidadeController@consultaBairros');
            $app->get('/bairro/{uf}/{cidade}', 'LocalidadeController@bairro');
            $app->get('/bairro/uf/{uf}/localidade/{cidade}', 'LocalidadeController@bairro');
            $app->post('/habilitar-servico/{tipo}/{servico}', 'ServicoLocalidadeController@save');
            $app->get('/verifica-servico/{uf}/{tipo}', 'ServicoLocalidadeController@verificaServico');
            $app->get('/verifica-selecionados/{uf}/{tipo}', 'ServicoLocalidadeController@verificaSelecionados');
        });

        $app->group(['prefix' => 'segmento'], function (\Laravel\Lumen\Application $app) {
            $app->post('/listar', 'SegmentoController@get');
        });

        $app->group(['prefix' => 'util'], function (\Laravel\Lumen\Application $app) {
            $app->get('/dias-campanha', 'UtilController@getQtdDiasCampanha');
            $app->get('/faixas-de-peso', 'UtilController@getFaixasPeso');
            $app->get('/chancelas', 'UtilController@chancelas');
            $app->get('/orientacoes', 'UtilController@orientacoes');
            $app->get('/segmentos', 'UtilController@segmentos');
            $app->post('/valida-cupom', 'UtilController@validaCupom');
            $app->get('/procedure', 'UtilController@procedure');
        });

        $app->group(['prefix' => 'parametro'], function (\Laravel\Lumen\Application $app){
            $app->get('listar', 'ParametroController@listar');
        });


        $app->group(['prefix' => 'parametro'], function(\Laravel\Lumen\Application $app){
            $app->get('listar', 'ParametroController@listar');
            $app->post('/salvar', 'ParametroController@salvar');
        });

        $app->group(['prefix' => 'capacidade'], function(\Laravel\Lumen\Application $app){
            $app->get('listar-ctcs', 'CapacidadeController@listarCtcs');
        });

    });
}
);
