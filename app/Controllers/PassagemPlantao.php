<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\PassagemPlantaoModel;

class PassagemPlantao extends BaseController
{
    private $AtendimentoModel;
    private $PassagemPlantaoModel;

    public function __construct()
    {
        $this->AtendimentoModel     = new AtendimentoModel();
        $this->PassagemPlantaoModel = new PassagemPlantaoModel();
    }

    /**
     * Lista os pacientes triados e a última passagem do perfil do usuário.
     *
     * @return string
     */
    public function index()
    {
        $titulo       = "Passagem de plantão";
        $subtitulo    = "Passagem de cada paciente no formato SBAR.";
        $paginaAtiva  = "plantao";
        $atendimentos = $this->AtendimentoModel->listaParaPlantao((int) session()->get("usuarioPerfilId"));

        // Só outro profissional do mesmo perfil confirma
        foreach ($atendimentos as $indice => $atendimento) {
            $atendimentos[$indice]["pode_confirmar"] = !empty($atendimento["id_passagem"]) && empty($atendimento["data_hora_recebimento"])
                && $atendimento["id_autor_passagem"] != session()->get("usuarioId")
                && $atendimento["id_perfil_autor_passagem"] == session()->get("usuarioPerfilId");
        }

        return view("plantao/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimentos"));
    }

    /**
     * Exibe o formulário SBAR do paciente.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "A passagem de plantão é feita para pacientes triados que continuam no pronto atendimento.");

            return redirect()->to("plantao");
        }

        return $this->exibeForm($atendimento, [], []);
    }

    /**
     * Grava a passagem de plantão.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "A passagem de plantão é feita para pacientes triados que continuam no pronto atendimento.");

            return redirect()->to("plantao");
        }

        $dados = [
            "id_atendimento" => $idAtendimento,
            "id_usuario"     => session()->get("usuarioId"),
            "situacao"       => trim((string) $this->request->getPost("situacao")),
            "historico"      => trim((string) $this->request->getPost("historico")),
            "avaliacao"      => trim((string) $this->request->getPost("avaliacao")),
            "recomendacao"   => trim((string) $this->request->getPost("recomendacao")),
            "data_hora"      => date("Y-m-d H:i:s"),
        ];

        if (!$this->PassagemPlantaoModel->save($dados)) {
            return $this->exibeForm($atendimento, $dados, $this->PassagemPlantaoModel->errors());
        }

        $this->gravaLog("Passagem de plantão registrada", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Passagem de plantão do atendimento " . $atendimento["numero"] . " registrada. Aguarda a confirmação de quem assume.");

        return redirect()->to("plantao");
    }

    /**
     * Confirma o recebimento da passagem de plantão.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function confirmar()
    {
        $id       = (int) $this->request->getPost("id_passagem");
        $passagem = $this->PassagemPlantaoModel->getComAutor($id);

        if (empty($passagem) || !empty($passagem["id_usuario_recebimento"])) {
            session()->setFlashdata("msgError", "Passagem não encontrada ou já recebida.");
        } elseif ($passagem["id_usuario"] == session()->get("usuarioId")) {
            session()->setFlashdata("msgError", "Quem registrou a passagem não pode confirmar o próprio recebimento.");
        } elseif ($passagem["id_perfil_autor"] != session()->get("usuarioPerfilId")) {
            session()->setFlashdata("msgError", "O recebimento deve ser confirmado por um profissional do mesmo perfil de quem passou o plantão.");
        } elseif ($this->PassagemPlantaoModel->confirmaRecebimento($id, session()->get("usuarioId"))) {
            $this->gravaLog("Passagem de plantão recebida", "atendimento", $passagem["id_atendimento"], "Atendimento " . $passagem["numero"]);
            session()->setFlashdata("msgSucesso", "Recebimento da passagem do atendimento " . $passagem["numero"] . " confirmado.");
        } else {
            session()->setFlashdata("msgError", "Esta passagem já foi recebida.");
        }

        return redirect()->to("plantao");
    }

    /**
     * Monta a tela da passagem de plantão do paciente.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo      = "Passagem de plantão";
        $subtitulo   = "Registro no formato SBAR para quem assume o paciente.";
        $paginaAtiva = "plantao";
        $passagens   = $this->PassagemPlantaoModel->listaPorAtendimento($atendimento["id_atendimento"]);

        return view("plantao/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "passagens", "dados", "erros"));
    }
}
