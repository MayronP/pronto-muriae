<?php

namespace App\Controllers;

use App\Models\LogAuditoriaModel;

class Auditoria extends BaseController
{
    private $LogAuditoriaModel;

    public function __construct()
    {
        $this->LogAuditoriaModel = new LogAuditoriaModel();
    }

    /**
     * Lista as ações registradas na auditoria, com filtros.
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Auditoria";
        $subtitulo   = "Acompanhe as ações importantes registradas no sistema.";
        $paginaAtiva = "admin";

        $filtros = [
            "usuario"      => trim((string) $this->request->getGet("usuario")),
            "acao"         => trim((string) $this->request->getGet("acao")),
            "data_inicial" => $this->request->getGet("data_inicial") ?? date("Y-m-d", strtotime("-7 days")),
            "data_final"   => $this->request->getGet("data_final") ?? date("Y-m-d"),
        ];

        $registros = $this->LogAuditoriaModel->pesquisar($filtros);

        return view("admin/auditoria", compact("titulo", "subtitulo", "paginaAtiva", "filtros", "registros"));
    }
}
