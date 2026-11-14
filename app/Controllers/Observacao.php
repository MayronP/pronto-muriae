<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;

class Observacao extends BaseController
{
    private $AtendimentoModel;

    public function __construct()
    {
        $this->AtendimentoModel = new AtendimentoModel();
    }

    /**
     * Lista os pacientes em observação.
     *
     * @return string
     */
    public function index()
    {
        $titulo       = "Observação";
        $subtitulo    = "Pacientes em observação, por ordem de chegada.";
        $paginaAtiva  = "observacao";
        $atendimentos = $this->AtendimentoModel->emObservacao();
        $ehMedico     = session()->get("usuarioPerfilId") == PERFIL_MEDICO;
        $ehEnfermagem = session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM;

        return view("observacao/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimentos", "ehMedico", "ehEnfermagem"));
    }

    /**
     * Exibe o formulário para encaminhar o paciente à observação.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento) || $atendimento["status"] != "em_atendimento") {
            session()->setFlashdata("msgError", "Somente atendimentos em andamento podem ser encaminhados para observação.");

            return redirect()->to("fila");
        }

        return $this->exibeForm($atendimento, [], []);
    }

    /**
     * Coloca o paciente em observação.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || $atendimento["status"] != "em_atendimento") {
            session()->setFlashdata("msgError", "Somente atendimentos em andamento podem ser encaminhados para observação.");

            return redirect()->to("fila");
        }

        $regras = [
            "leito_observacao" => [
                "label" => "Leito ou sala",
                "rules" => "required|max_length[40]"
            ]
        ];

        $dados = $this->request->getPost(["leito_observacao"]);

        if (!$this->validate($regras)) {
            return $this->exibeForm($atendimento, $dados, $this->validator->getErrors());
        }

        $gravou = $this->AtendimentoModel->update($idAtendimento, [
            "status"                      => "em_observacao",
            "leito_observacao"            => trim($dados["leito_observacao"]),
            "data_hora_inicio_observacao" => date("Y-m-d H:i:s"),
        ]);

        if (!$gravou) {
            session()->setFlashdata("msgError", "Não foi possível registrar a observação. Tente novamente.");

            return redirect()->to("observacao/form/" . $idAtendimento);
        }

        $this->gravaLog("Paciente em observação", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"] . " - " . trim($dados["leito_observacao"]));
        session()->setFlashdata("msgSucesso", "Atendimento " . $atendimento["numero"] . " em observação (" . trim($dados["leito_observacao"]) . ").");

        return redirect()->to("observacao");
    }

    /**
     * Monta a tela de encaminhamento para observação.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo      = "Encaminhar para observação";
        $subtitulo   = "Leito e início da observação.";
        $paginaAtiva = "observacao";

        return view("observacao/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "dados", "erros"));
    }
}
