<?php

namespace App\Controllers;

use App\Models\AnexoModel;
use App\Models\AtendimentoModel;

class Anexo extends BaseController
{
    private $AnexoModel;
    private $AtendimentoModel;

    public function __construct()
    {
        $this->AnexoModel       = new AnexoModel();
        $this->AtendimentoModel = new AtendimentoModel();
    }

    /**
     * Anexa um documento (PDF ou imagem) ao atendimento.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getById($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $regras = [
            "anexo" => [
                "label" => "Arquivo",
                "rules" => "uploaded[anexo]|max_size[anexo,2048]|ext_in[anexo,pdf,jpg,jpeg,png]|mime_in[anexo,application/pdf,image/jpeg,image/png]"
            ]
        ];

        if (!$this->validate($regras)) {
            session()->setFlashdata("msgError", implode(" ", $this->validator->getErrors()));

            return redirect()->to("atendimentos/form/" . $idAtendimento);
        }

        $arquivo = $this->request->getFile("anexo");
        $idAnexo = $this->AnexoModel->gravaArquivo($arquivo, $idAtendimento, null, session()->get("usuarioId"));

        if ($idAnexo == 0) {
            session()->setFlashdata("msgError", "Não foi possível anexar o arquivo. Tente novamente.");

            return redirect()->to("atendimentos/form/" . $idAtendimento);
        }

        $this->gravaLog("Anexo incluído", "anexo", $idAnexo, $arquivo->getClientName() . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Arquivo anexado ao atendimento.");

        return redirect()->to("atendimentos/form/" . $idAtendimento);
    }

    /**
     * Faz o download de um anexo guardado em writable/uploads.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\DownloadResponse|\CodeIgniter\HTTP\RedirectResponse
     */
    public function download($id)
    {
        $anexo   = $this->AnexoModel->getById($id);
        $caminho = empty($anexo) ? "" : WRITEPATH . "uploads/" . $anexo["caminho_arquivo"];

        if (empty($anexo) || !is_file($caminho)) {
            session()->setFlashdata("msgError", "Anexo não encontrado.");

            return redirect()->to("dashboard");
        }

        return $this->response->download($caminho, null)->setFileName($anexo["nome_original"]);
    }
}
