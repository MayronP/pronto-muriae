<?php

namespace App\Controllers;

use App\Models\ClassificacaoRiscoModel;

class ClassificacaoRisco extends BaseController
{
    private $ClassificacaoRiscoModel;

    public function __construct()
    {
        $this->ClassificacaoRiscoModel = new ClassificacaoRiscoModel();
    }

    /**
     * Lista as classificações de risco com o tempo-alvo de cada cor.
     *
     * @return string
     */
    public function index()
    {
        $titulo         = "Classificação de risco";
        $subtitulo      = "Tempo máximo de espera pelo médico em cada cor.";
        $paginaAtiva    = "admin";
        $classificacoes = $this->ClassificacaoRiscoModel->lista("ordem_prioridade");

        return view("admin/listaClassificacao", compact("titulo", "subtitulo", "paginaAtiva", "classificacoes"));
    }

    /**
     * Exibe o formulário de alteração do tempo-alvo (as cores são fixas).
     *
     * @param string $action
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($action = "update", $id = 0)
    {
        $dados = $this->ClassificacaoRiscoModel->getById($id);

        if ($action != "update" || empty($dados)) {
            session()->setFlashdata("msgError", "Classificação não encontrada.");

            return redirect()->to("admin/classificacoes");
        }

        return $this->exibeForm($action, $dados, []);
    }

    /**
     * Grava o tempo-alvo.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $id            = (int) $this->request->getPost("id_classificacao");
        $classificacao = $this->ClassificacaoRiscoModel->getById($id);

        if (empty($classificacao)) {
            session()->setFlashdata("msgError", "Classificação não encontrada.");

            return redirect()->to("admin/classificacoes");
        }

        $dados = [
            "id_classificacao"   => $id,
            "tempo_alvo_minutos" => trim((string) $this->request->getPost("tempo_alvo_minutos")),
        ];

        $erros = [];

        // Cor mais urgente não pode esperar mais que a menos urgente
        foreach ($this->ClassificacaoRiscoModel->lista("ordem_prioridade") as $outra) {
            if ($outra["ordem_prioridade"] < $classificacao["ordem_prioridade"] && (int) $dados["tempo_alvo_minutos"] < $outra["tempo_alvo_minutos"]) {
                $erros["tempo_alvo_minutos"] = "O tempo-alvo não pode ser menor que o da cor " . $outra["nome"] . " (" . $outra["tempo_alvo_minutos"] . " min), que é mais urgente.";
            }

            if ($outra["ordem_prioridade"] > $classificacao["ordem_prioridade"] && (int) $dados["tempo_alvo_minutos"] > $outra["tempo_alvo_minutos"]) {
                $erros["tempo_alvo_minutos"] = "O tempo-alvo não pode ser maior que o da cor " . $outra["nome"] . " (" . $outra["tempo_alvo_minutos"] . " min), que é menos urgente.";
            }
        }

        if (empty($erros) && !$this->ClassificacaoRiscoModel->save($dados)) {
            $erros = $this->ClassificacaoRiscoModel->errors();
        }

        if (!empty($erros)) {
            return $this->exibeForm("update", array_merge($classificacao, $dados), $erros);
        }

        $this->gravaLog("Tempo-alvo alterado", "classificacao_risco", $id, $classificacao["nome"] . ": " . $dados["tempo_alvo_minutos"] . " min");
        session()->setFlashdata("msgSucesso", "Tempo-alvo da cor " . $classificacao["nome"] . " alterado para " . $dados["tempo_alvo_minutos"] . " minutos.");

        return redirect()->to("admin/classificacoes");
    }

    /**
     * Monta a tela de alteração.
     *
     * @param string $action
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($action, $dados, $erros)
    {
        $titulo      = "Classificação de risco" . subTitulo($action);
        $subtitulo   = "Tempo máximo de espera pelo médico para a cor " . $dados["nome"] . ".";
        $paginaAtiva = "admin";

        return view("admin/formClassificacao", compact("titulo", "subtitulo", "paginaAtiva", "action", "dados", "erros"));
    }
}
