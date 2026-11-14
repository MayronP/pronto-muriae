<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\ProtocoloAtendimentoModel;
use App\Models\ProtocoloEtapaModel;
use App\Models\ProtocoloModel;
use App\Models\ProtocoloRegistroModel;

class Protocolo extends BaseController
{
    private $AtendimentoModel;
    private $ProtocoloModel;
    private $ProtocoloEtapaModel;
    private $ProtocoloAtendimentoModel;
    private $ProtocoloRegistroModel;

    public function __construct()
    {
        $this->AtendimentoModel          = new AtendimentoModel();
        $this->ProtocoloModel            = new ProtocoloModel();
        $this->ProtocoloEtapaModel       = new ProtocoloEtapaModel();
        $this->ProtocoloAtendimentoModel = new ProtocoloAtendimentoModel();
        $this->ProtocoloRegistroModel    = new ProtocoloRegistroModel();
    }

    /**
     * Exibe os protocolos gerenciados do atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        if ($atendimento["status"] == "encerrado") {
            return redirect()->to("atendimentos/detalhes/" . $idAtendimento);
        }

        return $this->exibePagina($atendimento, [], []);
    }

    /**
     * Abre um protocolo gerenciado para o paciente.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function abrir()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || $atendimento["status"] == "encerrado") {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $dados     = $this->request->getPost(["id_protocolo", "inicio_sintomas"]);
        $protocolo = $this->ProtocoloModel->getById((int) $dados["id_protocolo"]);

        if (empty($protocolo) || $protocolo["ativo"] != 1) {
            return $this->exibePagina($atendimento, $dados, ["id_protocolo" => "Selecione um protocolo."]);
        }

        $inicioSintomas = null;

        if (trim((string) $dados["inicio_sintomas"]) != "") {
            $inicioSintomas = str_replace("T", " ", $dados["inicio_sintomas"]) . ":00";

            if (strtotime($inicioSintomas) === false || strtotime($inicioSintomas) > time()) {
                return $this->exibePagina($atendimento, $dados, ["inicio_sintomas" => "Informe um horário de início dos sintomas até o momento atual."]);
            }
        } elseif (!empty($protocolo["janela_minutos"])) {
            return $this->exibePagina($atendimento, $dados, ["inicio_sintomas" => "Informe o horário de início dos sintomas."]);
        }

        $registro = [
            "id_atendimento"     => $idAtendimento,
            "id_protocolo"       => $protocolo["id_protocolo"],
            "id_usuario"         => session()->get("usuarioId"),
            "data_hora_abertura" => date("Y-m-d H:i:s"),
            "inicio_sintomas"    => $inicioSintomas,
            "situacao"           => "aberto",
        ];

        foreach ($this->ProtocoloAtendimentoModel->listaPorAtendimento($idAtendimento) as $aberto) {
            if ($aberto["id_protocolo"] == $protocolo["id_protocolo"]) {
                return $this->exibePagina($atendimento, $dados, ["id_protocolo" => "O protocolo " . $protocolo["nome"] . " já foi aberto para este atendimento."]);
            }
        }

        if (!$this->ProtocoloAtendimentoModel->save($registro)) {
            return $this->exibePagina($atendimento, $dados, $this->ProtocoloAtendimentoModel->errors());
        }

        $this->gravaLog("Protocolo aberto", "atendimento", $idAtendimento, "Protocolo " . $protocolo["nome"] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Protocolo " . $protocolo["nome"] . " aberto. Registre o horário de cada etapa.");

        return redirect()->to("protocolos/index/" . $idAtendimento);
    }

    /**
     * Registra o horário da etapa e informa se ficou dentro da meta.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function registrar()
    {
        $id        = (int) $this->request->getPost("id_protocolo_atendimento");
        $idEtapa   = (int) $this->request->getPost("id_etapa");
        $dataHora  = str_replace("T", " ", (string) $this->request->getPost("data_hora")) . ":00";
        $protocolo = $this->ProtocoloAtendimentoModel->getCompleto($id);
        $etapa     = $this->ProtocoloEtapaModel->getById($idEtapa);

        if (empty($protocolo) || empty($etapa) || $etapa["id_protocolo"] != $protocolo["id_protocolo"]) {
            session()->setFlashdata("msgError", "Etapa do protocolo não encontrada.");

            return redirect()->to("fila");
        }

        $voltar = "protocolos/index/" . $protocolo["id_atendimento"];

        if ($protocolo["situacao"] != "aberto" || $protocolo["status"] == "encerrado") {
            session()->setFlashdata("msgError", "O protocolo já foi encerrado.");

            return redirect()->to($voltar);
        }

        if (strtotime($dataHora) === false || strtotime($dataHora) > time() + 60 || strtotime($dataHora) < strtotime($protocolo["data_hora_chegada"])) {
            session()->setFlashdata("msgError", "O horário da etapa deve ser entre a chegada do paciente e o momento atual.");

            return redirect()->to($voltar);
        }

        if ($this->ProtocoloRegistroModel->existe($id, $idEtapa)) {
            session()->setFlashdata("msgError", "Esta etapa já foi registrada.");

            return redirect()->to($voltar);
        }

        $gravou = $this->ProtocoloRegistroModel->insert([
            "id_protocolo_atendimento" => $id,
            "id_etapa"                 => $idEtapa,
            "id_usuario"               => session()->get("usuarioId"),
            "data_hora"                => $dataHora,
        ]);

        if (!$gravou) {
            session()->setFlashdata("msgError", implode(" ", $this->ProtocoloRegistroModel->errors()));

            return redirect()->to($voltar);
        }

        $minutos  = minutosEntre(marcoProtocolo($protocolo), $dataHora);
        $situacao = ($minutos <= $etapa["meta_minutos"]) ? "dentro da meta" : "acima da meta";

        $this->gravaLog("Etapa de protocolo registrada", "atendimento", $protocolo["id_atendimento"], $protocolo["protocolo"] . ": " . $etapa["descricao"] . " em " . formataMinutos($minutos) . " (" . $situacao . ")");
        session()->setFlashdata("msgSucesso", $etapa["descricao"] . ": " . formataMinutos($minutos) . ", " . $situacao . " de " . $etapa["meta_minutos"] . " min.");

        return redirect()->to($voltar);
    }

    /**
     * Encerra o protocolo como concluído ou descartado.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function encerrar()
    {
        $id         = (int) $this->request->getPost("id_protocolo_atendimento");
        $situacao   = (string) $this->request->getPost("situacao");
        $observacao = trim((string) $this->request->getPost("observacao"));
        $protocolo  = $this->ProtocoloAtendimentoModel->getCompleto($id);

        if (empty($protocolo)) {
            session()->setFlashdata("msgError", "Protocolo não encontrado.");

            return redirect()->to("fila");
        }

        if ($protocolo["status"] == "encerrado") {
            session()->setFlashdata("msgError", "Atendimento já encerrado.");

            return redirect()->to("fila");
        }

        $voltar = "protocolos/index/" . $protocolo["id_atendimento"];

        if (!in_array($situacao, ["concluido", "descartado"])) {
            session()->setFlashdata("msgError", "Informe se o protocolo foi concluído ou descartado.");

            return redirect()->to($voltar);
        }

        if ($situacao == "descartado" && mb_strlen($observacao) < 3) {
            session()->setFlashdata("msgError", "Informe por que o protocolo foi descartado.");

            return redirect()->to($voltar);
        }

        if (!$this->ProtocoloAtendimentoModel->encerra($id, $situacao, $observacao !== "" ? mb_substr($observacao, 0, 255) : null)) {
            session()->setFlashdata("msgError", "O protocolo já foi encerrado.");

            return redirect()->to($voltar);
        }

        $this->gravaLog("Protocolo " . ($situacao == "concluido" ? "concluído" : "descartado"), "atendimento", $protocolo["id_atendimento"], $protocolo["protocolo"] . ($observacao !== "" ? " - " . $observacao : ""));
        session()->setFlashdata("msgSucesso", "Protocolo " . $protocolo["protocolo"] . " " . ($situacao == "concluido" ? "concluído" : "descartado") . ".");

        return redirect()->to($voltar);
    }

    /**
     * Monta a tela de protocolos do atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibePagina($atendimento, $dados, $erros)
    {
        $titulo      = "Protocolos gerenciados";
        $subtitulo   = "Horário de cada etapa comparado com a meta do protocolo.";
        $paginaAtiva = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $protocolos  = $this->ProtocoloAtendimentoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $abertos     = array_column($protocolos, "id_protocolo");
        $disponiveis = [];
        $etapas      = [];
        $registros   = [];

        foreach ($this->ProtocoloModel->listaAtivos() as $protocolo) {
            if (!in_array($protocolo["id_protocolo"], $abertos)) {
                $disponiveis[] = $protocolo;
            }
        }

        foreach ($this->ProtocoloEtapaModel->listaComProtocolo() as $etapa) {
            $etapas[$etapa["id_protocolo"]][] = $etapa;
        }

        foreach ($this->ProtocoloRegistroModel->listaPorAtendimento($atendimento["id_atendimento"]) as $registro) {
            $registros[$registro["id_protocolo_atendimento"]][$registro["id_etapa"]] = $registro;
        }

        $podeRegistrar = true;

        return view("protocolos/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "protocolos", "disponiveis", "etapas", "registros", "podeRegistrar", "dados", "erros"));
    }
}
