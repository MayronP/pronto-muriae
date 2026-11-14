<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;

class Fila extends BaseController
{
    private $AtendimentoModel;

    public function __construct()
    {
        $this->AtendimentoModel = new AtendimentoModel();
    }

    /**
     * Exibe a fila médica ordenada por prioridade e horário de chegada.
     *
     * @return string
     */
    public function index()
    {
        $status = (string) $this->request->getGet("status");
        $busca  = trim((string) $this->request->getGet("busca"));

        if (!in_array($status, ["aguardando_medico", "em_atendimento"])) {
            $status = "";
        }

        $titulo          = ($status == "em_atendimento") ? "Atendimentos em andamento" : "Fila de atendimento";
        $subtitulo       = "Pacientes triados aguardando ou em atendimento médico.";
        $paginaAtiva     = ($status == "em_atendimento") ? "atendimento" : "fila";
        $horaAtualizacao = date("H:i");
        $fila            = $this->AtendimentoModel->fila($status, $busca);
        $ehMedico        = session()->get("usuarioPerfilId") == PERFIL_MEDICO;
        $ehEnfermagem    = session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM;

        return view("fila/index", compact("titulo", "subtitulo", "paginaAtiva", "horaAtualizacao", "status", "busca", "fila", "ehMedico", "ehEnfermagem"));
    }
}
