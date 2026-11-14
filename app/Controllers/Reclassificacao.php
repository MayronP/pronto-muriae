<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\ClassificacaoRiscoModel;
use App\Models\ReclassificacaoModel;
use App\Models\TriagemModel;

class Reclassificacao extends BaseController
{
    private $AtendimentoModel;
    private $ClassificacaoRiscoModel;
    private $ReclassificacaoModel;
    private $TriagemModel;

    public function __construct()
    {
        $this->AtendimentoModel        = new AtendimentoModel();
        $this->ClassificacaoRiscoModel = new ClassificacaoRiscoModel();
        $this->ReclassificacaoModel    = new ReclassificacaoModel();
        $this->TriagemModel            = new TriagemModel();
    }

    /**
     * Exibe o formulário de reclassificação de risco.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (!$this->podeReclassificar($atendimento)) {
            session()->setFlashdata("msgError", "Este atendimento não pode ser reclassificado agora.");

            return redirect()->to("fila");
        }

        return $this->exibeForm($atendimento, ["id_classificacao_nova" => $atendimento["id_classificacao"]], []);
    }

    /**
     * Grava a reclassificação e troca a cor vigente da triagem.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (!$this->podeReclassificar($atendimento)) {
            session()->setFlashdata("msgError", "Este atendimento não pode ser reclassificado agora.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento"            => $idAtendimento,
            "id_usuario"                => session()->get("usuarioId"),
            "id_classificacao_anterior" => $atendimento["id_classificacao"],
            "id_classificacao_nova"     => $this->request->getPost("id_classificacao_nova"),
            "motivo"                    => trim((string) $this->request->getPost("motivo")),
            "data_hora"                 => date("Y-m-d H:i:s"),
        ];

        // Histórico da reclassificação e cor vigente da triagem
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        if (!$this->ReclassificacaoModel->save($dados)) {
            $db->transRollback();

            return $this->exibeForm($atendimento, $dados, $this->ReclassificacaoModel->errors());
        }

        // A espera pela meta recomeça com a nova cor
        $this->TriagemModel->reclassifica($idAtendimento, (int) $dados["id_classificacao_nova"], $dados["data_hora"]);

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível gravar a reclassificação. Tente novamente.");

            return redirect()->to("reclassificacao/form/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $nova = $this->ClassificacaoRiscoModel->getById((int) $dados["id_classificacao_nova"]);

        $this->gravaLog("Risco reclassificado", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"] . " - " . $atendimento["classificacao"] . " para " . $nova["nome"]);
        session()->setFlashdata("msgSucesso", "Atendimento " . $atendimento["numero"] . " reclassificado de " . $atendimento["classificacao"] . " para " . $nova["nome"] . ".");

        if (session()->get("usuarioPerfilId") == PERFIL_MEDICO && $atendimento["status"] != "aguardando_medico") {
            return redirect()->to("atendimentos/form/" . $idAtendimento);
        }

        return redirect()->to($atendimento["status"] == "em_observacao" ? "observacao" : "fila");
    }

    /**
     * Confere a etapa do atendimento e o perfil de quem reclassifica.
     *
     * @param array $atendimento
     * @return bool
     */
    private function podeReclassificar($atendimento)
    {
        if (empty($atendimento) || empty($atendimento["id_triagem"])) {
            return false;
        }

        if (session()->get("usuarioPerfilId") == PERFIL_MEDICO) {
            return in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"]);
        }

        return in_array($atendimento["status"], ["aguardando_medico", "em_observacao"]);
    }

    /**
     * Monta a tela de reclassificação.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo           = "Reclassificação de risco";
        $subtitulo        = "Reavalie quem espera além do tempo-alvo ou mudou de quadro.";
        $paginaAtiva      = ($atendimento["status"] == "em_observacao") ? "observacao" : "fila";
        $classificacoes   = $this->ClassificacaoRiscoModel->listaAtivas();
        $reclassificacoes = $this->ReclassificacaoModel->listaPorAtendimento($atendimento["id_atendimento"]);

        return view("reclassificacao/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "classificacoes", "reclassificacoes", "dados", "erros"));
    }
}
