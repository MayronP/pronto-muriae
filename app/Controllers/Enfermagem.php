<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\EvolucaoEnfermagemModel;
use App\Models\SinalVitalModel;

class Enfermagem extends BaseController
{
    private $AtendimentoModel;
    private $SinalVitalModel;
    private $EvolucaoEnfermagemModel;

    public function __construct()
    {
        $this->AtendimentoModel        = new AtendimentoModel();
        $this->SinalVitalModel         = new SinalVitalModel();
        $this->EvolucaoEnfermagemModel = new EvolucaoEnfermagemModel();
    }

    /**
     * Exibe os sinais vitais e os registros de enfermagem do atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        if ($atendimento["status"] == "encerrado") {
            return redirect()->to("atendimentos/detalhes/" . $idAtendimento);
        }

        if ($atendimento["status"] == "aguardando_triagem") {
            session()->setFlashdata("msgError", "Os primeiros sinais vitais são registrados na triagem.");

            return redirect()->to(session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM ? "triagem/form/" . $idAtendimento : "fila");
        }

        $dados = [
            "oxigenio_suplementar" => $atendimento["oxigenio_suplementar"] ?? "0",
            "nivel_consciencia"    => "A",
        ];

        return $this->exibePagina($atendimento, $dados, []);
    }

    /**
     * Registra uma nova aferição de sinais vitais e informa o NEWS2.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function sinais()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou fora do pronto atendimento.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento"          => $idAtendimento,
            "id_usuario"              => session()->get("usuarioId"),
            "data_hora"               => date("Y-m-d H:i:s"),
            "pressao_arterial"        => trim((string) $this->request->getPost("pressao_arterial")),
            "temperatura"             => str_replace(",", ".", (string) $this->request->getPost("temperatura")),
            "frequencia_cardiaca"     => $this->request->getPost("frequencia_cardiaca"),
            "frequencia_respiratoria" => $this->request->getPost("frequencia_respiratoria"),
            "saturacao"               => $this->request->getPost("saturacao"),
            "oxigenio_suplementar"    => $this->request->getPost("oxigenio_suplementar"),
            "nivel_consciencia"       => $this->request->getPost("nivel_consciencia"),
            "dor"                     => $this->request->getPost("dor"),
            "glicemia"                => $this->request->getPost("glicemia"),
            "observacoes"             => trim((string) $this->request->getPost("observacoes")),
        ];

        foreach (["dor", "glicemia", "observacoes"] as $campo) {
            if ($dados[$campo] === "") {
                $dados[$campo] = null;
            }
        }

        if (!$this->SinalVitalModel->save($dados)) {
            return $this->exibePagina($atendimento, $dados, $this->SinalVitalModel->errors());
        }

        $news2    = calculaNews2(array_merge($dados, ["gestante" => $atendimento["gestante"]]), $atendimento["data_nascimento"]);
        $mensagem = "Sinais vitais registrados." . mensagemNews2($news2);

        $this->gravaLog("Sinais vitais registrados", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", $mensagem);

        return redirect()->to("enfermagem/index/" . $idAtendimento);
    }

    /**
     * Registra a evolução de enfermagem nas etapas do Processo de Enfermagem.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function evolucao()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou fora do pronto atendimento.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento" => $idAtendimento,
            "id_usuario"     => session()->get("usuarioId"),
            "data_hora"      => date("Y-m-d H:i:s"),
            "avaliacao"      => trim((string) $this->request->getPost("avaliacao")),
            "implementacao"  => trim((string) $this->request->getPost("implementacao")),
        ];

        if (!$this->EvolucaoEnfermagemModel->save($dados)) {
            return $this->exibePagina($atendimento, $dados, $this->EvolucaoEnfermagemModel->errors());
        }

        $this->gravaLog("Evolução de enfermagem registrada", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Registro de enfermagem salvo.");

        return redirect()->to("enfermagem/index/" . $idAtendimento);
    }

    /**
     * Monta a tela de enfermagem do atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibePagina($atendimento, $dados, $erros)
    {
        $titulo       = "Enfermagem";
        $subtitulo    = "Sinais vitais seriados e registros do Processo de Enfermagem.";
        $paginaAtiva  = ($atendimento["status"] == "em_observacao") ? "observacao" : "fila";
        $sinais       = $this->SinalVitalModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $evolucoes    = $this->EvolucaoEnfermagemModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $ehEnfermagem = session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM;

        return view("enfermagem/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "sinais", "evolucoes", "ehEnfermagem", "dados", "erros"));
    }
}
