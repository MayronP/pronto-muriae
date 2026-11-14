<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\ClassificacaoRiscoModel;
use App\Models\ProtocoloEtapaModel;
use App\Models\UsuarioModel;

class Relatorio extends BaseController
{
    private $AtendimentoModel;
    private $UsuarioModel;
    private $ClassificacaoRiscoModel;
    private $ProtocoloEtapaModel;

    public function __construct()
    {
        $this->AtendimentoModel        = new AtendimentoModel();
        $this->UsuarioModel            = new UsuarioModel();
        $this->ClassificacaoRiscoModel = new ClassificacaoRiscoModel();
        $this->ProtocoloEtapaModel     = new ProtocoloEtapaModel();
    }

    /**
     * Consulta os atendimentos por período, profissional e status.
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Relatórios";
        $subtitulo   = "Indicadores de volume e de tempo do período.";
        $paginaAtiva = "admin";

        $filtros = [
            "data_inicial" => $this->request->getGet("data_inicial") ?? date("Y-m-01"),
            "data_final"   => $this->request->getGet("data_final") ?? date("Y-m-d"),
            "id_medico"    => (int) $this->request->getGet("id_medico"),
            "status"       => (string) $this->request->getGet("status"),
        ];

        $profissionais   = $this->UsuarioModel->listaPorPerfil(PERFIL_MEDICO);
        $resumoRelatorio = $this->AtendimentoModel->resumoRelatorio($filtros);
        $resultados      = $this->AtendimentoModel->relatorio($filtros);
        $tempos          = $this->AtendimentoModel->indicadoresTempo($filtros);
        $temposPorCor    = $this->AtendimentoModel->temposPorClassificacao($filtros);
        $classificacoes  = $this->ClassificacaoRiscoModel->listaAtivas();
        $protocolos      = $this->ProtocoloEtapaModel->indicadores($filtros);

        return view("admin/relatorios", compact("titulo", "subtitulo", "paginaAtiva", "filtros", "profissionais", "resumoRelatorio", "resultados", "tempos", "temposPorCor", "classificacoes", "protocolos"));
    }
}
