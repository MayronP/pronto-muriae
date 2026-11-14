<?php

namespace App\Controllers;

use App\Models\AdministracaoModel;
use App\Models\AtendimentoModel;
use App\Models\MedicamentoModel;
use App\Models\PrescricaoMedicamentoModel;
use App\Models\PrescricaoModel;

class Medicamento extends BaseController
{
    private $AtendimentoModel;
    private $PrescricaoModel;
    private $MedicamentoModel;
    private $PrescricaoMedicamentoModel;
    private $AdministracaoModel;

    public function __construct()
    {
        $this->AtendimentoModel           = new AtendimentoModel();
        $this->PrescricaoModel            = new PrescricaoModel();
        $this->MedicamentoModel           = new MedicamentoModel();
        $this->PrescricaoMedicamentoModel = new PrescricaoMedicamentoModel();
        $this->AdministracaoModel         = new AdministracaoModel();
    }

    /**
     * Lista os medicamentos do atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Os medicamentos só podem ser registrados em atendimentos triados e não encerrados.");

            return redirect()->to("fila");
        }

        $dados = [
            "frequencia"    => "unica",
            "primeira_dose" => date("Y-m-d\TH:i"),
            "situacao"      => "prescrito",
        ];

        return $this->exibePagina($atendimento, $dados, []);
    }

    /**
     * Prescreve o medicamento e programa as doses.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function prescrever()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || !in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento não encontrado ou já encerrado.");

            return redirect()->to("fila");
        }

        $regras = [
            "medicamento" => [
                "label" => "Medicamento",
                "rules" => "required|min_length[2]|max_length[180]"
            ],
            "apresentacao" => [
                "label" => "Apresentação",
                "rules" => "permit_empty|max_length[150]"
            ],
            "dose" => [
                "label" => "Dose",
                "rules" => "required|max_length[80]"
            ],
            "via" => [
                "label" => "Via",
                "rules" => "required|in_list[Oral,Intravenosa,Intramuscular,Subcutânea]"
            ],
            "frequencia" => [
                "label" => "Frequência",
                "rules" => "required|in_list[" . implode(",", array_keys(getFrequencias())) . "]"
            ],
            "primeira_dose" => [
                "label" => "Primeira dose",
                "rules" => "required|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/]"
            ],
            "situacao" => [
                "label" => "Situação",
                "rules" => "required|in_list[prescrito,administrado]"
            ]
        ];

        if ($this->request->getPost("frequencia") == "sn") {
            $regras["observacoes"] = [
                "label"  => "Condição de uso",
                "rules"  => "required|min_length[5]",
                "errors" => [
                    "required" => "No uso \"se necessário\", informe a condição de uso e a dose máxima diária."
                ]
            ];
        }

        $dados = $this->request->getPost(["medicamento", "apresentacao", "dose", "via", "frequencia", "primeira_dose", "observacoes", "situacao", "confirma_alerta"]);

        if (!$this->validate($regras)) {
            return $this->exibePagina($atendimento, $dados, $this->validator->getErrors());
        }

        $inicio = str_replace("T", " ", $dados["primeira_dose"]) . ":00";

        if (strtotime($inicio) < time() - 3600 || strtotime($inicio) > time() + PERMANENCIA_MAXIMA_HORAS * 3600) {
            return $this->exibePagina($atendimento, $dados, ["primeira_dose" => "A primeira dose deve ser entre uma hora atrás e as próximas " . PERMANENCIA_MAXIMA_HORAS . " horas."]);
        }

        if ($dados["situacao"] == "administrado" && strtotime($inicio) > time()) {
            return $this->exibePagina($atendimento, $dados, ["primeira_dose" => "Para registrar a 1ª dose como já administrada, informe o horário em que ela foi dada."]);
        }

        // Alergia ou duplicidade só grava com a confirmação do médico
        $alertas   = [];
        $alergia   = alergiaDoMedicamento($dados["medicamento"], $atendimento["alergias"]);
        $duplicado = $this->PrescricaoMedicamentoModel->duplicidade($idAtendimento, trim($dados["medicamento"]));

        if ($alergia != "") {
            $alertas[] = "Alergia registrada do paciente: " . $atendimento["alergias"] . ".";
        }

        if ($duplicado != "") {
            $alertas[] = $duplicado . " já está prescrito e ativo neste atendimento (duplicidade).";
        }

        if (!empty($alertas) && $dados["confirma_alerta"] != "1") {
            return $this->exibePagina($atendimento, $dados, ["confirma_alerta" => "Revise o alerta e confirme para prescrever mesmo assim."], $alertas);
        }

        $seNecessario = $dados["frequencia"] == "sn";
        $intervalo    = is_numeric($dados["frequencia"]) ? (int) $dados["frequencia"] : null;

        // Prescrição, item e doses aprazadas
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        // Uma prescrição por médico no atendimento
        $prescricao = $this->PrescricaoModel->getPrescricao($idAtendimento, session()->get("usuarioId"));

        if (!empty($prescricao)) {
            $idPrescricao = $prescricao["id_prescricao"];
        } else {
            $idPrescricao = $this->PrescricaoModel->insert([
                "id_atendimento" => $idAtendimento,
                "id_medico"      => session()->get("usuarioId"),
                "data_hora"      => date("Y-m-d H:i:s"),
                "status"         => "ativa",
            ]);
        }

        // Medicamento novo entra no catálogo
        $medicamento = $this->MedicamentoModel->getMedicamento(trim($dados["medicamento"]), trim((string) $dados["apresentacao"]));

        if (!empty($medicamento)) {
            $idMedicamento = $medicamento["id_medicamento"];
        } else {
            $idMedicamento = $this->MedicamentoModel->insert([
                "nome"         => trim($dados["medicamento"]),
                "apresentacao" => trim((string) $dados["apresentacao"]),
            ]);
        }

        $item = [
            "id_prescricao"    => $idPrescricao,
            "id_medicamento"   => $idMedicamento,
            "dose"             => trim($dados["dose"]),
            "via"              => $dados["via"],
            "frequencia"       => getFrequencias()[$dados["frequencia"]],
            "intervalo_horas"  => $intervalo,
            "se_necessario"    => $seNecessario ? 1 : 0,
            "data_hora_inicio" => $inicio,
            "observacoes"      => trim((string) $dados["observacoes"]),
            "situacao"         => "prescrito",
        ];

        if (!$this->PrescricaoMedicamentoModel->save($item)) {
            $db->transRollback();

            return $this->exibePagina($atendimento, $dados, $this->PrescricaoMedicamentoModel->errors());
        }

        $idItem = $this->PrescricaoMedicamentoModel->getInsertID();
        $doses  = $this->AdministracaoModel->aprazar($idItem, $inicio, $intervalo, $seNecessario);

        if ($dados["situacao"] == "administrado") {
            if ($seNecessario) {
                $this->AdministracaoModel->registraAgora($idItem, session()->get("usuarioId"));
            } else {
                $this->AdministracaoModel->checaPrimeira($idItem, session()->get("usuarioId"));
            }
        }

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível gravar a prescrição. Tente novamente.");

            return redirect()->to("medicamentos/index/" . $idAtendimento);
        }

        $db->transCommit();             // Confirma

        $detalhes = trim($dados["medicamento"]) . " - atendimento " . $atendimento["numero"] . (!empty($alertas) ? " - alerta revisado: " . implode(" ", $alertas) : "");

        $this->gravaLog("Medicamento prescrito", "prescricao_medicamento", $idItem, $detalhes);
        session()->setFlashdata("msgSucesso", "Medicamento prescrito" . ($doses > 0 ? " com " . $doses . " dose(s) aprazada(s)." : "."));

        return redirect()->to("medicamentos/index/" . $idAtendimento);
    }

    /**
     * Checa a dose aprazada como administrada ou não administrada.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function checar()
    {
        $id       = (int) $this->request->getPost("id_administracao");
        $situacao = (string) $this->request->getPost("situacao");
        $motivo   = trim((string) $this->request->getPost("motivo"));
        $dose     = $this->AdministracaoModel->getComAtendimento($id);

        if (empty($dose)) {
            session()->setFlashdata("msgError", "Dose não encontrada.");

            return redirect()->to("fila");
        }

        $atendimento = $this->AtendimentoModel->getById($dose["id_atendimento"]);

        if (!in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento já encerrado.");

            return redirect()->to("fila");
        }

        if (!in_array($situacao, ["administrado", "nao_administrado"]) || $dose["situacao_item"] != "prescrito") {
            session()->setFlashdata("msgError", "Somente doses de medicamentos prescritos podem ser checadas.");

            return redirect()->to("medicamentos/index/" . $dose["id_atendimento"]);
        }

        if ($situacao == "nao_administrado" && !in_array($motivo, getMotivosNaoAdministracao())) {
            session()->setFlashdata("msgError", "Informe o motivo da dose não administrada.");

            return redirect()->to("medicamentos/index/" . $dose["id_atendimento"]);
        }

        if (!$this->AdministracaoModel->checar($id, $situacao, session()->get("usuarioId"), $situacao == "nao_administrado" ? $motivo : null)) {
            session()->setFlashdata("msgError", "Esta dose já foi checada.");

            return redirect()->to("medicamentos/index/" . $dose["id_atendimento"]);
        }

        $acao = ($situacao == "administrado") ? "Dose administrada" : "Dose não administrada";

        $this->gravaLog($acao, "administracao", $id, $dose["medicamento"] . " das " . formatHora($dose["data_hora_prevista"]) . " - atendimento " . $atendimento["numero"] . ($motivo != "" ? " - " . $motivo : ""));
        session()->setFlashdata("msgSucesso", $acao . ": " . $dose["medicamento"] . " das " . formatHora($dose["data_hora_prevista"]) . ".");

        return redirect()->to("medicamentos/index/" . $dose["id_atendimento"]);
    }

    /**
     * Registra a administração de um medicamento "se necessário".
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function sos()
    {
        $id   = (int) $this->request->getPost("id_item_prescricao");
        $item = $this->PrescricaoMedicamentoModel->getComAtendimento($id);

        if (empty($item)) {
            session()->setFlashdata("msgError", "Medicamento não encontrado.");

            return redirect()->to("fila");
        }

        $atendimento = $this->AtendimentoModel->getById($item["id_atendimento"]);

        if (!in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento já encerrado.");

            return redirect()->to("fila");
        }

        if ($item["se_necessario"] != 1 || $item["situacao"] != "prescrito") {
            session()->setFlashdata("msgError", "Somente medicamentos \"se necessário\" ativos são registrados desta forma.");

            return redirect()->to("medicamentos/index/" . $item["id_atendimento"]);
        }

        $this->AdministracaoModel->registraAgora($id, session()->get("usuarioId"));

        $this->gravaLog("Dose administrada", "prescricao_medicamento", $id, $item["medicamento"] . " (se necessário) - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Administração de " . $item["medicamento"] . " registrada.");

        return redirect()->to("medicamentos/index/" . $item["id_atendimento"]);
    }

    /**
     * Suspende o item prescrito e as doses pendentes.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function situacao()
    {
        $id       = (int) $this->request->getPost("id_item_prescricao");
        $situacao = $this->request->getPost("situacao");
        $item     = $this->PrescricaoMedicamentoModel->getComAtendimento($id);

        if (empty($item)) {
            session()->setFlashdata("msgError", "Medicamento não encontrado.");

            return redirect()->to("fila");
        }

        if ($item["situacao"] != "prescrito" || $situacao != "suspenso") {
            session()->setFlashdata("msgError", "Somente medicamentos prescritos podem ser suspensos.");

            return redirect()->to("medicamentos/index/" . $item["id_atendimento"]);
        }

        $atendimento = $this->AtendimentoModel->getById($item["id_atendimento"]);

        if (!in_array($atendimento["status"], ["aguardando_medico", "em_atendimento", "em_observacao"])) {
            session()->setFlashdata("msgError", "Atendimento já encerrado.");

            return redirect()->to("fila");
        }

        // Item e doses pendentes
        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        $this->PrescricaoMedicamentoModel->update($id, ["situacao" => "suspenso"]);
        $this->AdministracaoModel->suspendePendentes($id);

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz
            session()->setFlashdata("msgError", "Não foi possível suspender a prescrição. Tente novamente.");

            return redirect()->to("medicamentos/index/" . $item["id_atendimento"]);
        }

        $db->transCommit();             // Confirma

        $this->gravaLog("Prescrição suspensa", "prescricao_medicamento", $id, $item["medicamento"] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", "Prescrição de " . $item["medicamento"] . " suspensa.");

        return redirect()->to("medicamentos/index/" . $item["id_atendimento"]);
    }

    /**
     * Monta a tela de medicamentos do atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @param array $alertas
     * @return string
     */
    private function exibePagina($atendimento, $dados, $erros, $alertas = [])
    {
        $titulo         = "Medicamentos";
        $subtitulo      = "Prescrição com aprazamento e checagem de cada dose.";
        $paginaAtiva    = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $medicamentos   = $this->PrescricaoMedicamentoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $catalogo       = $this->MedicamentoModel->listaNomes();
        $podePrescrever = session()->get("usuarioPerfilId") == PERFIL_MEDICO;
        $itens          = array_column($medicamentos, null, "id_item_prescricao");
        $doses          = [];

        // Doses na ordem do horário previsto
        foreach ($this->AdministracaoModel->listaPorAtendimento($atendimento["id_atendimento"]) as $dose) {
            $item = $itens[$dose["id_item_prescricao"]];

            $dose["medicamento"] = $item["medicamento"];
            $dose["dose"]        = $item["dose"];
            $dose["via"]         = $item["via"];
            $dose["pode_checar"] = $dose["situacao"] == "pendente" && $item["situacao"] == "prescrito";

            $doses[] = $dose;
        }

        // Doses feitas e pendentes de cada item
        foreach ($medicamentos as $indice => $item) {
            $medicamentos[$indice]["doses_total"]     = 0;
            $medicamentos[$indice]["doses_feitas"]    = 0;
            $medicamentos[$indice]["doses_pendentes"] = 0;

            foreach ($doses as $dose) {
                if ($dose["id_item_prescricao"] == $item["id_item_prescricao"]) {
                    $medicamentos[$indice]["doses_total"]++;

                    if ($dose["situacao"] == "administrado") {
                        $medicamentos[$indice]["doses_feitas"]++;
                    } elseif ($dose["situacao"] == "pendente") {
                        $medicamentos[$indice]["doses_pendentes"]++;
                    }
                }
            }
        }

        return view("medicamentos/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "medicamentos", "doses", "catalogo", "podePrescrever", "alertas", "dados", "erros"));
    }
}
