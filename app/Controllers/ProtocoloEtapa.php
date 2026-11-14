<?php

namespace App\Controllers;

use App\Models\ProtocoloEtapaModel;

class ProtocoloEtapa extends BaseController
{
    private $ProtocoloEtapaModel;

    public function __construct()
    {
        $this->ProtocoloEtapaModel = new ProtocoloEtapaModel();
    }

    /**
     * Lista os protocolos gerenciados com a meta de cada etapa e a fonte.
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Protocolos gerenciados";
        $subtitulo   = "Metas de tempo de cada etapa.";
        $paginaAtiva = "admin";
        $protocolos  = [];

        // Etapas agrupadas pelo nome do protocolo
        foreach ($this->ProtocoloEtapaModel->listaComProtocolo() as $etapa) {
            $protocolos[$etapa["protocolo"]][] = $etapa;
        }

        return view("admin/listaProtocoloEtapa", compact("titulo", "subtitulo", "paginaAtiva", "protocolos"));
    }

    /**
     * Exibe o formulário de alteração da meta de uma etapa.
     *
     * @param string $action
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($action = "update", $id = 0)
    {
        $dados = $this->ProtocoloEtapaModel->getComProtocolo($id);

        if ($action != "update" || empty($dados)) {
            session()->setFlashdata("msgError", "Etapa não encontrada.");

            return redirect()->to("admin/protocolos");
        }

        return $this->exibeForm($action, $dados, []);
    }

    /**
     * Grava a meta da etapa (em minutos).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $id    = (int) $this->request->getPost("id_etapa");
        $etapa = $this->ProtocoloEtapaModel->getComProtocolo($id);

        if (empty($etapa)) {
            session()->setFlashdata("msgError", "Etapa não encontrada.");

            return redirect()->to("admin/protocolos");
        }

        $dados = [
            "id_etapa"     => $id,
            "meta_minutos" => trim((string) $this->request->getPost("meta_minutos")),
        ];

        if (!$this->ProtocoloEtapaModel->save($dados)) {
            return $this->exibeForm("update", array_merge($etapa, $dados), $this->ProtocoloEtapaModel->errors());
        }

        $this->gravaLog("Meta de protocolo alterada", "protocolo_etapa", $id, $etapa["protocolo"] . " - " . $etapa["descricao"] . ": " . $dados["meta_minutos"] . " min");
        session()->setFlashdata("msgSucesso", "Meta da etapa \"" . $etapa["descricao"] . "\" alterada para " . $dados["meta_minutos"] . " minutos.");

        return redirect()->to("admin/protocolos");
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
        $titulo      = "Protocolos gerenciados" . subTitulo($action);
        $subtitulo   = "Meta da etapa do protocolo " . $dados["protocolo"] . ".";
        $paginaAtiva = "admin";

        return view("admin/formProtocoloEtapa", compact("titulo", "subtitulo", "paginaAtiva", "action", "dados", "erros"));
    }
}
