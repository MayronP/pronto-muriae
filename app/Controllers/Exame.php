<?php

namespace App\Controllers;

use App\Models\AnexoModel;
use App\Models\AtendimentoModel;
use App\Models\ExameModel;

class Exame extends BaseController
{
    private $ExameModel;
    private $AtendimentoModel;
    private $AnexoModel;

    public function __construct()
    {
        $this->ExameModel       = new ExameModel();
        $this->AtendimentoModel = new AtendimentoModel();
        $this->AnexoModel       = new AnexoModel();
    }

    /**
     * Lista os exames do atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Os exames só podem ser registrados em atendimentos triados e não encerrados.");

            return redirect()->to(empty($atendimento) ? "fila" : "atendimentos/detalhes/" . $idAtendimento);
        }

        $titulo           = "Exames";
        $subtitulo        = "Solicitações e resultados do atendimento.";
        $paginaAtiva      = "atendimento";
        $exames           = $this->ExameModel->listaPorAtendimento($idAtendimento);
        $exameSelecionado = (int) $this->request->getGet("exame");
        $anexosExame      = [];

        foreach ($this->AnexoModel->listaPorAtendimento($idAtendimento) as $anexo) {
            if (!empty($anexo["id_exame"])) {
                $anexosExame[$anexo["id_exame"]][] = $anexo;
            }
        }

        return view("exames/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "exames", "anexosExame", "exameSelecionado"));
    }

    /**
     * Registra a solicitação de um exame no atendimento.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function solicitar()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getById($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento"        => $idAtendimento,
            "id_solicitante"        => session()->get("usuarioId"),
            "descricao"             => trim((string) $this->request->getPost("descricao")),
            "data_hora_solicitacao" => date("Y-m-d H:i:s"),
            "status"                => "solicitado",
        ];

        if (!$this->ExameModel->save($dados)) {
            session()->setFlashdata("msgError", implode(" ", $this->ExameModel->errors()));

            return redirect()->to("exames/index/" . $idAtendimento)->withInput();
        }

        $this->gravaLog("Exame solicitado", "exame", $this->ExameModel->getInsertID(), $dados["descricao"] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Exame solicitado com sucesso.");

        return redirect()->to("exames/index/" . $idAtendimento);
    }

    /**
     * Registra o resultado de um exame, com anexo opcional (PDF ou imagem).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function resultado()
    {
        $idExame = (int) $this->request->getPost("id_exame");
        $exame   = $this->ExameModel->getById($idExame);

        if (empty($exame)) {
            session()->setFlashdata("msgError", "Selecione um exame solicitado.");

            return redirect()->to("exames/index/" . (int) $this->request->getPost("id_atendimento"));
        }

        $idAtendimento = $exame["id_atendimento"];
        $atendimento   = $this->AtendimentoModel->getById($idAtendimento);

        if (!in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento já encerrado.");

            return redirect()->to("fila");
        }

        if ($exame["status"] != "solicitado") {
            session()->setFlashdata("msgError", "O resultado deste exame já foi registrado.");

            return redirect()->to("exames/index/" . $idAtendimento);
        }

        $regras = [
            "resultado" => [
                "label" => "Resultado",
                "rules" => "required|min_length[2]|max_length[16000]"
            ]
        ];

        $arquivo = $this->request->getFile("anexo");

        if ($arquivo != null && $arquivo->getError() != UPLOAD_ERR_NO_FILE) {
            $regras["anexo"] = [
                "label" => "Anexo",
                "rules" => "uploaded[anexo]|max_size[anexo,2048]|ext_in[anexo,pdf,jpg,jpeg,png]|mime_in[anexo,application/pdf,image/jpeg,image/png]"
            ];
        }

        if (!$this->validate($regras)) {
            session()->setFlashdata("msgError", implode(" ", $this->validator->getErrors()));

            return redirect()->to("exames/index/" . $idAtendimento . "?exame=" . $idExame)->withInput();
        }

        // Resultado e anexo do exame
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        $gravou = $this->ExameModel->update($idExame, [
            "status"               => "concluido",
            "resultado"            => trim($this->request->getPost("resultado")),
            "id_usuario_resultado" => session()->get("usuarioId"),
            "data_hora_resultado"  => date("Y-m-d H:i:s"),
        ]);

        if (!$gravou) {
            $db->transRollback();
            session()->setFlashdata("msgError", implode(" ", $this->ExameModel->errors()));

            return redirect()->to("exames/index/" . $idAtendimento . "?exame=" . $idExame)->withInput();
        }

        if (isset($regras["anexo"]) && !$this->AnexoModel->gravaArquivo($arquivo, $idAtendimento, $idExame, session()->get("usuarioId"))) {
            $db->transRollback();
            session()->setFlashdata("msgError", "Não foi possível gravar o anexo. Tente novamente.");

            return redirect()->to("exames/index/" . $idAtendimento . "?exame=" . $idExame)->withInput();
        }

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível gravar o resultado. Tente novamente.");

            return redirect()->to("exames/index/" . $idAtendimento . "?exame=" . $idExame);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Resultado de exame registrado", "exame", $idExame, $exame["descricao"] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Resultado registrado com sucesso.");

        return redirect()->to("exames/index/" . $idAtendimento);
    }
}
