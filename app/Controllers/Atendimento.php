<?php

namespace App\Controllers;

use App\Models\AcompanhanteModel;
use App\Models\AdministracaoModel;
use App\Models\AnexoModel;
use App\Models\AtendimentoModel;
use App\Models\Cid10Model;
use App\Models\DocumentoModel;
use App\Models\EvolucaoEnfermagemModel;
use App\Models\EvolucaoMedicaModel;
use App\Models\ExameModel;
use App\Models\NotificacaoModel;
use App\Models\PassagemPlantaoModel;
use App\Models\PrescricaoMedicamentoModel;
use App\Models\ProtocoloAtendimentoModel;
use App\Models\ProtocoloEtapaModel;
use App\Models\ProtocoloRegistroModel;
use App\Models\ReclassificacaoModel;
use App\Models\SinalVitalModel;

class Atendimento extends BaseController
{
    private $AtendimentoModel;
    private $EvolucaoMedicaModel;
    private $ExameModel;
    private $PrescricaoMedicamentoModel;
    private $AdministracaoModel;
    private $AnexoModel;
    private $ReclassificacaoModel;
    private $PassagemPlantaoModel;
    private $SinalVitalModel;
    private $EvolucaoEnfermagemModel;
    private $DocumentoModel;
    private $ProtocoloAtendimentoModel;
    private $ProtocoloEtapaModel;
    private $ProtocoloRegistroModel;
    private $AcompanhanteModel;
    private $NotificacaoModel;
    private $Cid10Model;

    public function __construct()
    {
        $this->AtendimentoModel           = new AtendimentoModel();
        $this->EvolucaoMedicaModel        = new EvolucaoMedicaModel();
        $this->ExameModel                 = new ExameModel();
        $this->PrescricaoMedicamentoModel = new PrescricaoMedicamentoModel();
        $this->AdministracaoModel         = new AdministracaoModel();
        $this->AnexoModel                 = new AnexoModel();
        $this->ReclassificacaoModel       = new ReclassificacaoModel();
        $this->PassagemPlantaoModel       = new PassagemPlantaoModel();
        $this->SinalVitalModel            = new SinalVitalModel();
        $this->EvolucaoEnfermagemModel    = new EvolucaoEnfermagemModel();
        $this->DocumentoModel             = new DocumentoModel();
        $this->ProtocoloAtendimentoModel  = new ProtocoloAtendimentoModel();
        $this->ProtocoloEtapaModel        = new ProtocoloEtapaModel();
        $this->ProtocoloRegistroModel     = new ProtocoloRegistroModel();
        $this->AcompanhanteModel          = new AcompanhanteModel();
        $this->NotificacaoModel           = new NotificacaoModel();
        $this->Cid10Model                 = new Cid10Model();
    }

    /**
     * Inicia o atendimento médico do paciente da fila.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function iniciar()
    {
        $id          = (int) $this->request->getPost("id_atendimento");
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou fora da fila médica.");

            return redirect()->to("fila");
        }

        if ($atendimento["status"] == "aguardando_medico" && $this->AtendimentoModel->iniciaAtendimento($atendimento, session()->get("usuarioId"))) {
            $this->gravaLog("Atendimento médico iniciado", "atendimento", $id, "Atendimento " . $atendimento["numero"]);
        }

        return redirect()->to("atendimentos/form/" . $id);
    }

    /**
     * Exibe a tela de atendimento médico.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        if ($atendimento["status"] == "encerrado") {
            return redirect()->to("atendimentos/detalhes/" . $id);
        }

        if ($atendimento["status"] == "aguardando_triagem") {
            session()->setFlashdata("msgError", "O atendimento " . $atendimento["numero"] . " ainda não passou pela triagem.");

            return redirect()->to("fila");
        }

        return $this->exibeForm($atendimento, [], []);
    }

    /**
     * Grava uma evolução médica (o histórico de evoluções é preservado).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $dados = [
            "id_atendimento"       => $idAtendimento,
            "id_medico"            => session()->get("usuarioId"),
            "data_hora"            => date("Y-m-d H:i:s"),
            "evolucao"             => trim((string) $this->request->getPost("evolucao")),
            "observacoes"          => trim((string) $this->request->getPost("observacoes")),
            "hipotese_diagnostica" => trim((string) $this->request->getPost("hipotese_diagnostica")),
            "conclusao_clinica"    => trim((string) $this->request->getPost("conclusao_clinica")),
        ];

        // Evolução e início do atendimento
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        if (!$this->EvolucaoMedicaModel->save($dados)) {
            $db->transRollback();

            return $this->exibeForm($atendimento, $dados, $this->EvolucaoMedicaModel->errors());
        }

        if ($atendimento["status"] == "aguardando_medico") {
            $this->AtendimentoModel->iniciaAtendimento($atendimento, session()->get("usuarioId"));
        }

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível gravar a evolução. Tente novamente.");

            return redirect()->to("atendimentos/form/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Evolução médica registrada", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Evolução registrada com sucesso.");

        return redirect()->to("atendimentos/form/" . $idAtendimento);
    }

    /**
     * Exibe o formulário de encerramento (desfecho) do atendimento.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function encerramento($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        if (!in_array($atendimento["status"], ["em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Somente atendimentos em andamento ou em observação podem ser encerrados.");

            return redirect()->to("atendimentos/form/" . $id);
        }

        if (empty($this->EvolucaoMedicaModel->listaPorAtendimento($id))) {
            session()->setFlashdata("msgError", "Registre ao menos uma evolução médica antes de encerrar o atendimento.");

            return redirect()->to("atendimentos/form/" . $id);
        }

        $dados = [
            "data_obito" => date("Y-m-d"),
            "hora_obito" => date("H:i"),
        ];

        return $this->exibeEncerramento($atendimento, $dados, []);
    }

    /**
     * Registra o desfecho e encerra o atendimento (exige ao menos uma evolução).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function encerrar()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Somente atendimentos em andamento ou em observação podem ser encerrados.");

            return redirect()->to("fila");
        }

        if (empty($this->EvolucaoMedicaModel->listaPorAtendimento($idAtendimento))) {
            session()->setFlashdata("msgError", "Registre ao menos uma evolução médica antes de encerrar o atendimento.");

            return redirect()->to("atendimentos/form/" . $idAtendimento);
        }

        $regras = [
            "desfecho" => [
                "label" => "Desfecho",
                "rules" => "required|in_list[" . implode(",", array_keys(getDesfechos())) . "]"
            ],
            "cid_principal" => [
                "label" => "CID-10",
                "rules" => "required"
            ],
            "resumo_final" => [
                "label" => "Resumo do atendimento",
                "rules" => "required|min_length[3]|max_length[16000]"
            ]
        ];

        if ($this->request->getPost("desfecho") == "obito") {
            $regras["data_obito"] = [
                "label" => "Data do óbito",
                "rules" => "required|valid_date[Y-m-d]"
            ];
            $regras["hora_obito"] = [
                "label" => "Horário do óbito",
                "rules" => "required|regex_match[/^\d{2}:\d{2}$/]"
            ];
            $regras["declaracao_obito"] = [
                "label" => "Número da declaração de óbito",
                "rules" => "permit_empty|max_length[20]"
            ];
        }

        if ($this->request->getPost("desfecho") == "alta_pedido") {
            $regras["termo_alta_pedido"] = [
                "label"  => "Termo de responsabilidade",
                "rules"  => "required|in_list[1]",
                "errors" => [
                    "required" => "Confirme que o paciente ou o responsável assinou o termo de responsabilidade."
                ]
            ];
        }

        $dados = $this->request->getPost(["desfecho", "cid_principal", "resumo_final", "observacoes_finais", "data_obito", "hora_obito", "declaracao_obito", "termo_alta_pedido"]);

        if (!$this->validate($regras)) {
            return $this->exibeEncerramento($atendimento, $dados, $this->validator->getErrors());
        }

        $cid = $this->Cid10Model->getByCodigo($dados["cid_principal"]);

        if (empty($cid)) {
            return $this->exibeEncerramento($atendimento, $dados, ["cid_principal" => "Código não encontrado na CID-10."]);
        }

        $encerramento = [
            "status"                 => "encerrado",
            "id_medico"              => session()->get("usuarioId"),
            "desfecho"               => $dados["desfecho"],
            "cid_principal"          => $cid["codigo"],
            "resumo_final"           => trim($dados["resumo_final"]),
            "observacoes_finais"     => trim((string) $dados["observacoes_finais"]),
            "data_hora_encerramento" => date("Y-m-d H:i:s"),
        ];

        if ($dados["desfecho"] == "obito") {
            $encerramento["data_hora_obito"]  = $dados["data_obito"] . " " . $dados["hora_obito"] . ":00";
            $encerramento["declaracao_obito"] = trim((string) $dados["declaracao_obito"]) !== "" ? trim($dados["declaracao_obito"]) : null;

            if (strtotime($encerramento["data_hora_obito"]) > time() || strtotime($encerramento["data_hora_obito"]) < strtotime($atendimento["data_hora_chegada"])) {
                return $this->exibeEncerramento($atendimento, $dados, ["hora_obito" => "O óbito deve ser entre a chegada do paciente e o horário atual."]);
            }
        }

        if ($dados["desfecho"] == "alta_pedido") {
            $encerramento["termo_alta_pedido"] = 1;
        }

        // Encerramento e saída do acompanhante
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        $gravou = $this->AtendimentoModel->update($idAtendimento, $encerramento);

        $this->AcompanhanteModel->registraSaida($idAtendimento);

        if (!$gravou || $db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível encerrar o atendimento. Tente novamente.");

            return redirect()->to("atendimentos/encerramento/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Atendimento encerrado", "atendimento", $idAtendimento, "Atendimento " . $atendimento["numero"] . " - desfecho: " . getDescricaoStatus($dados["desfecho"]));
        session()->setFlashdata("msgSucesso", "Atendimento " . $atendimento["numero"] . " encerrado com desfecho " . getDescricaoStatus($dados["desfecho"]) . ".");

        return redirect()->to("fila");
    }

    /**
     * Exibe o prontuário do atendimento (somente leitura).
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function detalhes($id)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($id);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("pacientes");
        }

        $this->gravaLog("Prontuário consultado", "atendimento", $id, "Atendimento " . $atendimento["numero"]);

        $titulo              = "Prontuário do atendimento";
        $subtitulo           = "Registros do atendimento.";
        $paginaAtiva         = "pacientes";
        $evolucoes           = $this->EvolucaoMedicaModel->listaPorAtendimento($id);
        $exames              = $this->ExameModel->listaPorAtendimento($id);
        $medicamentos        = $this->PrescricaoMedicamentoModel->listaPorAtendimento($id);
        $anexos              = $this->AnexoModel->listaPorAtendimento($id);
        $reclassificacoes    = $this->ReclassificacaoModel->listaPorAtendimento($id);
        $passagens           = $this->PassagemPlantaoModel->listaPorAtendimento($id);
        $sinais              = $this->SinalVitalModel->listaPorAtendimento($id);
        $evolucoesEnfermagem = $this->EvolucaoEnfermagemModel->listaPorAtendimento($id);
        $documentos          = $this->DocumentoModel->listaPorAtendimento($id);
        $protocolos          = $this->ProtocoloAtendimentoModel->listaPorAtendimento($id);
        $acompanhantes       = $this->AcompanhanteModel->listaPorAtendimento($id);
        $notificacoes        = $this->NotificacaoModel->listaPorAtendimento($id);
        $podeRegistrar       = false;
        $doses               = [];
        $etapas              = [];
        $registros           = [];

        foreach ($this->AdministracaoModel->listaPorAtendimento($id) as $dose) {
            $doses[$dose["id_item_prescricao"]][] = $dose;
        }

        foreach ($this->ProtocoloEtapaModel->listaComProtocolo() as $etapa) {
            $etapas[$etapa["id_protocolo"]][] = $etapa;
        }

        foreach ($this->ProtocoloRegistroModel->listaPorAtendimento($id) as $registro) {
            $registros[$registro["id_protocolo_atendimento"]][$registro["id_etapa"]] = $registro;
        }

        return view("atendimento/detalhes", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "evolucoes", "exames", "medicamentos", "doses", "anexos", "reclassificacoes", "passagens", "sinais", "evolucoesEnfermagem", "documentos", "protocolos", "etapas", "registros", "podeRegistrar", "acompanhantes", "notificacoes"));
    }

    /**
     * Monta a tela de atendimento médico.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($atendimento, $dados, $erros)
    {
        $titulo           = "Atendimento médico";
        $subtitulo        = "Avaliação médica do paciente.";
        $paginaAtiva      = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $evolucoes        = $this->EvolucaoMedicaModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $anexos           = $this->AnexoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $reclassificacoes = $this->ReclassificacaoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $passagens        = $this->PassagemPlantaoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $sinais           = $this->SinalVitalModel->listaPorAtendimento($atendimento["id_atendimento"]);

        return view("atendimento/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "evolucoes", "anexos", "reclassificacoes", "passagens", "sinais", "dados", "erros"));
    }

    /**
     * Monta a tela de encerramento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeEncerramento($atendimento, $dados, $erros)
    {
        $titulo      = "Encerramento do atendimento";
        $subtitulo   = "Registre o desfecho do atendimento.";
        $paginaAtiva = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $cid         = $this->Cid10Model->getByCodigo($dados["cid_principal"] ?? "");

        return view("encerramento/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "cid", "dados", "erros"));
    }
}
