<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\ClassificacaoRiscoModel;
use App\Models\PacienteModel;
use App\Models\TriagemModel;

class Triagem extends BaseController
{
    private $TriagemModel;
    private $AtendimentoModel;
    private $ClassificacaoRiscoModel;
    private $PacienteModel;

    public function __construct()
    {
        $this->TriagemModel            = new TriagemModel();
        $this->AtendimentoModel        = new AtendimentoModel();
        $this->ClassificacaoRiscoModel = new ClassificacaoRiscoModel();
        $this->PacienteModel           = new PacienteModel();
    }

    /**
     * Lista os atendimentos que aguardam triagem, por ordem de chegada.
     *
     * @return string
     */
    public function index()
    {
        $titulo       = "Triagem";
        $subtitulo    = "Pacientes que aguardam a triagem, por ordem de chegada.";
        $paginaAtiva  = "triagem";
        $atendimentos = $this->AtendimentoModel->aguardandoTriagem();

        return view("triagem/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimentos"));
    }

    /**
     * Exibe o formulário de triagem e classificação de risco do atendimento.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento) || $atendimento["status"] != "aguardando_triagem") {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já triado.");

            return redirect()->to("triagem");
        }

        $dados = [
            "alergias"             => $atendimento["alergias"],
            "nivel_consciencia"    => "A",
            "oxigenio_suplementar" => "0",
            "gestante"             => "0",
        ];

        return $this->exibeForm($atendimento, $dados, []);
    }

    /**
     * Grava a triagem e encaminha o paciente para a fila médica.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || $atendimento["status"] != "aguardando_triagem") {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já triado.");

            return redirect()->to("triagem");
        }

        $dados = [
            "id_atendimento"          => $idAtendimento,
            "id_enfermeiro"           => session()->get("usuarioId"),
            "id_classificacao"        => $this->request->getPost("id_classificacao"),
            "queixa_principal"        => trim((string) $this->request->getPost("queixa_principal")),
            "observacoes_iniciais"    => trim((string) $this->request->getPost("observacoes_iniciais")),
            "pressao_arterial"        => trim((string) $this->request->getPost("pressao_arterial")),
            "temperatura"             => str_replace(",", ".", (string) $this->request->getPost("temperatura")),
            "frequencia_cardiaca"     => $this->request->getPost("frequencia_cardiaca"),
            "frequencia_respiratoria" => $this->request->getPost("frequencia_respiratoria"),
            "saturacao"               => $this->request->getPost("saturacao"),
            "oxigenio_suplementar"    => $this->request->getPost("oxigenio_suplementar"),
            "nivel_consciencia"       => $this->request->getPost("nivel_consciencia"),
            "gestante"                => $this->request->getPost("gestante"),
            "dor"                     => $this->request->getPost("dor"),
            "glicemia"                => $this->request->getPost("glicemia"),
            "peso"                    => str_replace(",", ".", (string) $this->request->getPost("peso")),
            "data_hora"               => date("Y-m-d H:i:s"),
        ];

        foreach (["dor", "glicemia", "peso"] as $campo) {
            if ($dados[$campo] === "" || $dados[$campo] === null) {
                $dados[$campo] = null;
            }
        }

        $alergias = trim((string) $this->request->getPost("alergias"));

        if (mb_strlen($alergias) > 500) {
            $dados["alergias"] = $alergias;

            return $this->exibeForm($atendimento, $dados, ["alergias" => "Descreva as alergias em até 500 caracteres."]);
        }

        // Triagem, status do atendimento e alergias do paciente
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        if (!$this->TriagemModel->save($dados)) {
            $db->transRollback();

            $dados["alergias"] = $alergias;

            return $this->exibeForm($atendimento, $dados, $this->TriagemModel->errors());
        }

        $this->AtendimentoModel->update($idAtendimento, ["status" => "aguardando_medico"]);
        $this->PacienteModel->update($atendimento["id_paciente"], ["alergias" => $alergias !== "" ? $alergias : null]);

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível gravar a triagem. Tente novamente.");

            return redirect()->to("triagem/form/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $classificacao = $this->ClassificacaoRiscoModel->getById((int) $dados["id_classificacao"]);
        $news2         = calculaNews2($dados, $atendimento["data_nascimento"]);
        $mensagem      = "Triagem do atendimento " . $atendimento["numero"] . " registrada. Paciente encaminhado para a fila médica." . mensagemNews2($news2);

        $this->gravaLog("Triagem registrada", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"] . " - classificação " . $classificacao["nome"]);
        session()->setFlashdata("msgSucesso", $mensagem);

        return redirect()->to("triagem");
    }

    /**
     * Monta a tela de triagem.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo         = "Triagem";
        $subtitulo      = "Registre a avaliação inicial e a classificação de risco do atendimento.";
        $paginaAtiva    = "triagem";
        $classificacoes = $this->ClassificacaoRiscoModel->listaAtivas();

        return view("triagem/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "classificacoes", "dados", "erros"));
    }
}
