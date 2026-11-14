<?php

namespace App\Controllers;

use App\Models\AcompanhanteModel;
use App\Models\AtendimentoModel;

class Evasao extends BaseController
{
    private $AtendimentoModel;
    private $AcompanhanteModel;

    public function __construct()
    {
        $this->AtendimentoModel  = new AtendimentoModel();
        $this->AcompanhanteModel = new AcompanhanteModel();
    }

    /**
     * Exibe o registro de saída sem atendimento.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_triagem", "aguardando_medico"])) {
            session()->setFlashdata("msgError", "Só é possível registrar a saída de quem ainda aguarda a triagem ou o médico.");

            return redirect()->to("fila");
        }

        return $this->exibeForm($atendimento, [], []);
    }

    /**
     * Encerra o atendimento com o desfecho "saiu sem atendimento".
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_triagem", "aguardando_medico"])) {
            session()->setFlashdata("msgError", "Só é possível registrar a saída de quem ainda aguarda a triagem ou o médico.");

            return redirect()->to("fila");
        }

        $regras = [
            "resumo_final" => [
                "label" => "Como a saída foi constatada",
                "rules" => "required|min_length[5]|max_length[16000]"
            ]
        ];

        $dados = $this->request->getPost(["resumo_final"]);

        if (!$this->validate($regras)) {
            return $this->exibeForm($atendimento, $dados, $this->validator->getErrors());
        }

        // Encerramento e saída do acompanhante
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        $gravou = $this->AtendimentoModel->update($idAtendimento, [
            "status"                 => "encerrado",
            "desfecho"               => "evasao",
            "resumo_final"           => trim($dados["resumo_final"]),
            "data_hora_encerramento" => date("Y-m-d H:i:s"),
        ]);

        $this->AcompanhanteModel->registraSaida($idAtendimento);

        if (!$gravou || $db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível registrar a saída. Tente novamente.");

            return redirect()->to("evasao/form/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Saída sem atendimento registrada", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Saída sem atendimento registrada no atendimento " . $atendimento["numero"] . ".");

        return redirect()->to($atendimento["status"] == "aguardando_triagem" && session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM ? "triagem" : "fila");
    }

    /**
     * Monta a tela de saída sem atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo      = "Saída sem atendimento";
        $subtitulo   = "Paciente que saiu antes de ser atendido pelo médico.";
        $paginaAtiva = ($atendimento["status"] == "aguardando_triagem") ? "triagem" : "fila";

        return view("evasao/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "dados", "erros"));
    }
}
