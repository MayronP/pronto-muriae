<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\ChamadaModel;
use App\Models\ClassificacaoRiscoModel;

class Painel extends BaseController
{
    private $ChamadaModel;
    private $AtendimentoModel;
    private $ClassificacaoRiscoModel;

    public function __construct()
    {
        $this->ChamadaModel            = new ChamadaModel();
        $this->AtendimentoModel        = new AtendimentoModel();
        $this->ClassificacaoRiscoModel = new ClassificacaoRiscoModel();
    }

    /**
     * Exibe o painel de chamada da sala de espera.
     *
     * @return string
     */
    public function index()
    {
        $titulo         = "Painel de chamada";
        $chamadas       = $this->ChamadaModel->ultimasDoDia(7);
        $classificacoes = $this->ClassificacaoRiscoModel->listaAtivas();

        return view("painel/index", compact("titulo", "chamadas", "classificacoes"));
    }

    /**
     * Chama o paciente no painel e volta para a página anterior.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function chamar()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getById($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_triagem", "aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return $this->voltaPaginaAnterior("fila");
        }

        $chamada = [
            "id_atendimento" => $idAtendimento,
            "id_usuario"     => session()->get("usuarioId"),
            "local"          => getLocalAtendimento(),
            "data_hora"      => date("Y-m-d H:i:s"),
        ];

        if (!$this->ChamadaModel->save($chamada)) {
            session()->setFlashdata("msgError", implode(" ", $this->ChamadaModel->errors()));

            return $this->voltaPaginaAnterior("fila");
        }

        session()->setFlashdata("msgSucesso", "Atendimento " . $atendimento["numero"] . " chamado no painel: " . $chamada["local"] . ".");

        return $this->voltaPaginaAnterior("fila");
    }

    /**
     * Define o local das chamadas e volta para a triagem ou para a fila.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function local()
    {
        $local   = (string) $this->request->getPost("local");
        $destino = (session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM) ? "triagem" : "fila";

        if (!in_array($local, getLocaisAtendimento(), true)) {
            session()->setFlashdata("msgError", "Local de atendimento inválido.");

            return redirect()->to($destino);
        }

        session()->set("localAtendimento", $local);
        session()->setFlashdata("msgSucesso", "Suas chamadas serão para: " . $local . ".");

        return redirect()->to($destino);
    }
}
