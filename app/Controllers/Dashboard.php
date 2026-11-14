<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;

class Dashboard extends BaseController
{
    private $AtendimentoModel;

    public function __construct()
    {
        $this->AtendimentoModel = new AtendimentoModel();
    }

    /**
     * Exibe a situação atual do pronto atendimento.
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Dashboard";
        $subtitulo   = "Situação atual do pronto atendimento.";
        $paginaAtiva = "dashboard";

        $indicadores = [
            [
                "rotulo"  => "Aguardando triagem",
                "valor"   => $this->AtendimentoModel->totalPorStatus(["aguardando_triagem"]),
                "detalhe" => "Admitidos sem classificação"
            ],
            [
                "rotulo"  => "Aguardando médico",
                "valor"   => $this->AtendimentoModel->totalPorStatus(["aguardando_medico"]),
                "detalhe" => "Classificados na fila"
            ],
            [
                "rotulo"  => "Atendidos hoje",
                "valor"   => $this->AtendimentoModel->totalAtendidosHoje(),
                "detalhe" => "Chegaram hoje e passaram pelo médico"
            ],
            [
                "rotulo"  => "Em atendimento",
                "valor"   => $this->AtendimentoModel->totalPorStatus(["em_atendimento"]),
                "detalhe" => "Com médico"
            ],
            [
                "rotulo"  => "Em observação",
                "valor"   => $this->AtendimentoModel->totalPorStatus(["em_observacao"]),
                "detalhe" => "No pronto atendimento"
            ],
            [
                "rotulo"  => "Prioritários",
                "valor"   => $this->AtendimentoModel->totalPrioritarios(),
                "detalhe" => "Vermelho ou laranja em aberto"
            ]
        ];

        $fila = $this->AtendimentoModel->fila("", "", 8);

        return view("dashboard/index", compact("titulo", "subtitulo", "paginaAtiva", "indicadores", "fila"));
    }
}
