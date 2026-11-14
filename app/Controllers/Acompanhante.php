<?php

namespace App\Controllers;

use App\Models\AcompanhanteModel;
use App\Models\AtendimentoModel;

class Acompanhante extends BaseController
{
    private $AtendimentoModel;
    private $AcompanhanteModel;

    public function __construct()
    {
        $this->AtendimentoModel  = new AtendimentoModel();
        $this->AcompanhanteModel = new AcompanhanteModel();
    }

    /**
     * Lista os acompanhantes do atendimento (entrada e saída)
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

        return $this->exibePagina($atendimento, [], []);
    }

    /**
     * Registra o acompanhante.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || $atendimento["status"] == "encerrado") {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento"    => $idAtendimento,
            "id_usuario"        => session()->get("usuarioId"),
            "nome"              => trim((string) $this->request->getPost("nome")),
            "parentesco"        => trim((string) $this->request->getPost("parentesco")),
            "documento"         => trim((string) $this->request->getPost("documento")),
            "telefone"          => trim((string) $this->request->getPost("telefone")),
            "data_hora_entrada" => date("Y-m-d H:i:s"),
        ];

        if (!$this->AcompanhanteModel->validate($dados)) {
            return $this->exibePagina($atendimento, $dados, $this->AcompanhanteModel->errors());
        }

        // saída do acompanhante e a entrada do novo
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        $this->AcompanhanteModel->registraSaida($idAtendimento);
        $this->AcompanhanteModel->insert($dados);

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível registrar o acompanhante. Tente novamente.");

            return redirect()->to("acompanhantes/index/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Acompanhante registrado", "atendimento", $idAtendimento, $dados["nome"] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Acompanhante " . $dados["nome"] . " registrado.");

        return redirect()->to("acompanhantes/index/" . $idAtendimento);
    }

    /**
     * Registra a saída do acompanhante.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function saida()
    {
        $id           = (int) $this->request->getPost("id_acompanhante");
        $acompanhante = $this->AcompanhanteModel->getById($id);

        if (empty($acompanhante)) {
            session()->setFlashdata("msgError", "Acompanhante não encontrado.");

            return redirect()->to("fila");
        }

        if (!empty($acompanhante["data_hora_saida"])) {
            session()->setFlashdata("msgError", "A saída deste acompanhante já foi registrada.");
        } elseif (!$this->AcompanhanteModel->update($id, ["data_hora_saida" => date("Y-m-d H:i:s")])) {
            session()->setFlashdata("msgError", "Não foi possível registrar a saída. Tente novamente.");
        } else {
            $this->gravaLog("Saída de acompanhante", "atendimento", $acompanhante["id_atendimento"], $acompanhante["nome"]);
            session()->setFlashdata("msgSucesso", "Saída de " . $acompanhante["nome"] . " registrada.");
        }

        return redirect()->to("acompanhantes/index/" . $acompanhante["id_atendimento"]);
    }

    /**
     * Monta a tela de acompanhantes.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibePagina($atendimento, $dados, $erros)
    {
        $titulo        = "Acompanhante";
        $subtitulo     = "Entrada e saída dos acompanhantes.";
        $paginaAtiva   = ($atendimento["status"] == "em_observacao") ? "observacao" : "fila";
        $acompanhantes = $this->AcompanhanteModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $podeRegistrar = in_array(session()->get("usuarioPerfilId"), [PERFIL_ADMINISTRADOR, PERFIL_ENFERMAGEM]) && $atendimento["status"] != "encerrado";

        return view("acompanhantes/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "acompanhantes", "podeRegistrar", "dados", "erros"));
    }
}
