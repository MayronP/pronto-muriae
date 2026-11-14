<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\NotificacaoModel;

class Notificacao extends BaseController
{
    private $AtendimentoModel;
    private $NotificacaoModel;

    public function __construct()
    {
        $this->AtendimentoModel = new AtendimentoModel();
        $this->NotificacaoModel = new NotificacaoModel();
    }

    /**
     * Lista as notificações compulsórias registradas (pendentes primeiro).
     *
     * @return string
     */
    public function index()
    {
        $situacao = (string) ($this->request->getGet("situacao") ?? "pendente");

        if (!in_array($situacao, ["pendente", "notificada", "todas"])) {
            $situacao = "pendente";
        }

        $titulo        = "Notificações compulsórias";
        $subtitulo     = "Suspeitas registradas no atendimento e o envio ao Sinan.";
        $paginaAtiva   = "notificacoes";
        $notificacoes  = $this->NotificacaoModel->listaPorSituacao($situacao);
        $podeNotificar = in_array(session()->get("usuarioPerfilId"), [PERFIL_ADMINISTRADOR, PERFIL_ENFERMAGEM]);

        return view("notificacoes/index", compact("titulo", "subtitulo", "paginaAtiva", "situacao", "notificacoes", "podeNotificar"));
    }

    /**
     * Exibe as notificações do atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        return $this->exibeForm($atendimento, ["tipo" => "imediata"], []);
    }

    /**
     * Registra a suspeita de doença ou agravo de notificação compulsória.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento" => $idAtendimento,
            "id_usuario"     => session()->get("usuarioId"),
            "agravo"         => trim((string) $this->request->getPost("agravo")),
            "tipo"           => $this->request->getPost("tipo"),
            "data_hora"      => date("Y-m-d H:i:s"),
            "situacao"       => "pendente",
        ];

        if (!$this->NotificacaoModel->save($dados)) {
            return $this->exibeForm($atendimento, $dados, $this->NotificacaoModel->errors());
        }

        $this->gravaLog("Notificação compulsória registrada", "atendimento", $idAtendimento, $dados["agravo"] . " (" . $dados["tipo"] . ") - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Suspeita de " . $dados["agravo"] . " registrada. A notificação fica pendente até o envio ao Sinan.");

        return redirect()->to("notificacoes/form/" . $idAtendimento);
    }

    /**
     * Registra o envio da notificação ao Sinan.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function notificar()
    {
        $id          = (int) $this->request->getPost("id_notificacao");
        $numeroSinan = trim((string) $this->request->getPost("numero_sinan"));
        $notificacao = $this->NotificacaoModel->getById($id);

        if (empty($notificacao)) {
            session()->setFlashdata("msgError", "Notificação não encontrada.");
        } elseif (mb_strlen($numeroSinan) > 30) {
            session()->setFlashdata("msgError", "O número do Sinan deve ter até 30 caracteres.");
        } elseif (!$this->NotificacaoModel->notifica($id, session()->get("usuarioId"), $numeroSinan)) {
            session()->setFlashdata("msgError", "Esta notificação já foi enviada.");
        } else {
            $this->gravaLog("Envio ao Sinan registrado", "atendimento", $notificacao["id_atendimento"], $notificacao["agravo"] . ($numeroSinan !== "" ? " - nº " . $numeroSinan : ""));
            session()->setFlashdata("msgSucesso", "Notificação de " . $notificacao["agravo"] . " registrada como enviada ao Sinan.");
        }

        if (!empty($notificacao) && $this->request->getPost("origem") == "atendimento") {
            return redirect()->to("notificacoes/form/" . $notificacao["id_atendimento"]);
        }

        return redirect()->to("notificacoes");
    }

    /**
     * Monta a tela de notificações do atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo        = "Notificação compulsória";
        $subtitulo     = "Suspeitas de notificação compulsória do atendimento.";
        $paginaAtiva   = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $notificacoes  = $this->NotificacaoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $podeNotificar = in_array(session()->get("usuarioPerfilId"), [PERFIL_ADMINISTRADOR, PERFIL_ENFERMAGEM]);

        return view("notificacoes/form", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "notificacoes", "podeNotificar", "dados", "erros"));
    }
}
