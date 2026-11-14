<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Login::index');
$routes->get("login", "Login::index");
$routes->post("login", "Login::autenticar");
$routes->post("logout", "Login::logout", ["filter" => ["autenticacao", "fluxoescrita"]]);

// Painel público da sala de espera, com o nome abreviado do paciente
$routes->get("painel", "Painel::index");

// Perfis no filtro: 1 = Administrador, 2 = Enfermagem, 3 = Médico
$routes->get("dashboard", "Dashboard::index", ["filter" => ["autenticacao", "fluxoescrita"]]);
$routes->get("fila", "Fila::index", ["filter" => ["autenticacao", "fluxoescrita"]]);
$routes->get("cid10/buscar", "Cid10::buscar", ["filter" => ["autenticacao", "fluxoescrita"]]);

$routes->group("painel", ["filter" => ["autenticacao:2,3", "fluxoescrita"]], static function($routes) {
    $routes->post("chamar", "Painel::chamar");
    $routes->post("local", "Painel::local");
});

$routes->group("pacientes", static function($routes) {
    $routes->get("/", "Paciente::index", ["filter" => ["autenticacao", "fluxoescrita"]]);
    $routes->get("form/(:alpha)/(:num)", "Paciente::form/$1/$2", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->match(["POST", "PUT"], "store", "Paciente::store", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->get("naoIdentificado", "Paciente::naoIdentificado", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->post("naoIdentificado", "Paciente::storeNaoIdentificado", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->get("historico/(:num)", "Paciente::historico/$1", ["filter" => ["autenticacao", "fluxoescrita"]]);
});

$routes->group("admissao", ["filter" => ["autenticacao:1,2", "fluxoescrita"]], static function($routes) {
    $routes->get("form/(:num)", "Admissao::form/$1");
    $routes->post("store", "Admissao::store");
});

$routes->group("triagem", ["filter" => ["autenticacao:2", "fluxoescrita"]], static function($routes) {
    $routes->get("/", "Triagem::index");
    $routes->get("form/(:num)", "Triagem::form/$1");
    $routes->post("store", "Triagem::store");
});

$routes->group("atendimentos", static function($routes) {
    $routes->post("iniciar", "Atendimento::iniciar", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->get("form/(:num)", "Atendimento::form/$1", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->post("store", "Atendimento::store", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->get("encerramento/(:num)", "Atendimento::encerramento/$1", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->post("encerrar", "Atendimento::encerrar", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->get("detalhes/(:num)", "Atendimento::detalhes/$1", ["filter" => ["autenticacao", "fluxoescrita"]]);
});

$routes->group("reclassificacao", ["filter" => ["autenticacao:2,3", "fluxoescrita"]], static function($routes) {
    $routes->get("form/(:num)", "Reclassificacao::form/$1");
    $routes->post("store", "Reclassificacao::store");
});

$routes->group("observacao", static function($routes) {
    $routes->get("/", "Observacao::index", ["filter" => ["autenticacao", "fluxoescrita"]]);
    $routes->get("form/(:num)", "Observacao::form/$1", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->post("store", "Observacao::store", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
});

$routes->group("evasao", ["filter" => ["autenticacao:2,3", "fluxoescrita"]], static function($routes) {
    $routes->get("form/(:num)", "Evasao::form/$1");
    $routes->post("store", "Evasao::store");
});

$routes->group("plantao", ["filter" => ["autenticacao:2,3", "fluxoescrita"]], static function($routes) {
    $routes->get("/", "PassagemPlantao::index");
    $routes->get("form/(:num)", "PassagemPlantao::form/$1");
    $routes->post("store", "PassagemPlantao::store");
    $routes->post("confirmar", "PassagemPlantao::confirmar");
});

$routes->group("exames", ["filter" => ["autenticacao:3", "fluxoescrita"]], static function($routes) {
    $routes->get("index/(:num)", "Exame::index/$1");
    $routes->post("solicitar", "Exame::solicitar");
    $routes->post("resultado", "Exame::resultado");
});

$routes->group("medicamentos", static function($routes) {
    $routes->get("index/(:num)", "Medicamento::index/$1", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("prescrever", "Medicamento::prescrever", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
    $routes->post("checar", "Medicamento::checar", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("sos", "Medicamento::sos", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("situacao", "Medicamento::situacao", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
});

$routes->group("enfermagem", static function($routes) {
    $routes->get("index/(:num)", "Enfermagem::index/$1", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("sinais", "Enfermagem::sinais", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("evolucao", "Enfermagem::evolucao", ["filter" => ["autenticacao:2", "fluxoescrita"]]);
});

$routes->group("documentos", ["filter" => ["autenticacao", "fluxoescrita"]], static function($routes) {
    $routes->get("index/(:num)", "Documento::index/$1");
    $routes->post("store", "Documento::store");
    $routes->get("imprimir/(:num)", "Documento::imprimir/$1");
    $routes->get("sumario/(:num)", "Documento::sumario/$1");
});

$routes->group("protocolos", ["filter" => ["autenticacao:2,3", "fluxoescrita"]], static function($routes) {
    $routes->get("index/(:num)", "Protocolo::index/$1");
    $routes->post("abrir", "Protocolo::abrir");
    $routes->post("registrar", "Protocolo::registrar");
    $routes->post("encerrar", "Protocolo::encerrar");
});

$routes->group("acompanhantes", static function($routes) {
    $routes->get("index/(:num)", "Acompanhante::index/$1", ["filter" => ["autenticacao", "fluxoescrita"]]);
    $routes->post("store", "Acompanhante::store", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->post("saida", "Acompanhante::saida", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
});

$routes->group("notificacoes", static function($routes) {
    $routes->get("/", "Notificacao::index", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
    $routes->get("form/(:num)", "Notificacao::form/$1", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("store", "Notificacao::store", ["filter" => ["autenticacao:2,3", "fluxoescrita"]]);
    $routes->post("notificar", "Notificacao::notificar", ["filter" => ["autenticacao:1,2", "fluxoescrita"]]);
});

$routes->group("anexos", static function($routes) {
    $routes->get("download/(:num)", "Anexo::download/$1", ["filter" => ["autenticacao", "fluxoescrita"]]);
    $routes->post("store", "Anexo::store", ["filter" => ["autenticacao:3", "fluxoescrita"]]);
});

$routes->group("admin", ["filter" => ["autenticacao:1", "fluxoescrita"]], static function($routes) {
    $routes->get("usuarios", "Usuario::index");
    $routes->get("usuarios/form/(:alpha)/(:num)", "Usuario::form/$1/$2");
    $routes->match(["POST", "PUT"], "usuarios/store", "Usuario::store");
    $routes->post("usuarios/situacao", "Usuario::situacao");
    $routes->get("relatorios", "Relatorio::index");
    $routes->get("auditoria", "Auditoria::index");
    $routes->get("classificacoes", "ClassificacaoRisco::index");
    $routes->get("classificacoes/form/(:alpha)/(:num)", "ClassificacaoRisco::form/$1/$2");
    $routes->post("classificacoes/store", "ClassificacaoRisco::store");
    $routes->get("protocolos", "ProtocoloEtapa::index");
    $routes->get("protocolos/form/(:alpha)/(:num)", "ProtocoloEtapa::form/$1/$2");
    $routes->post("protocolos/store", "ProtocoloEtapa::store");
});
