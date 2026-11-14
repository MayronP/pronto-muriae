<?php

namespace App\Controllers;

use App\Models\AcompanhanteModel;
use App\Models\AtendimentoModel;
use App\Models\PacienteModel;

class Admissao extends BaseController
{
    private $PacienteModel;
    private $AtendimentoModel;
    private $AcompanhanteModel;

    public function __construct()
    {
        $this->PacienteModel     = new PacienteModel();
        $this->AtendimentoModel  = new AtendimentoModel();
        $this->AcompanhanteModel = new AcompanhanteModel();
    }

    /**
     * Exibe o formulário de admissão.
     *
     * @param int $idPaciente
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($idPaciente)
    {
        $paciente = $this->PacienteModel->getById($idPaciente);

        if (empty($paciente)) {
            session()->setFlashdata("msgError", "Paciente não encontrado.");

            return redirect()->to("pacientes");
        }

        $aberto = $this->AtendimentoModel->getAberto($idPaciente);

        if (!empty($aberto)) {
            session()->setFlashdata("msgError", "O paciente já possui o atendimento " . $aberto["numero"] . " em aberto.");

            return redirect()->to("pacientes/historico/" . $idPaciente);
        }

        $dados = [
            "data_chegada" => date("Y-m-d"),
            "hora_chegada" => date("H:i"),
        ];
        $erros = [];

        return $this->exibeForm($paciente, $dados, $erros);
    }

    /**
     * Registra a admissão
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idPaciente = (int) $this->request->getPost("id_paciente");
        $paciente   = $this->PacienteModel->getById($idPaciente);

        if (empty($paciente)) {
            session()->setFlashdata("msgError", "Paciente não encontrado.");

            return redirect()->to("pacientes");
        }

        if (!empty($this->AtendimentoModel->getAberto($idPaciente))) {
            session()->setFlashdata("msgError", "O paciente já possui um atendimento em aberto.");

            return redirect()->to("pacientes/historico/" . $idPaciente);
        }

        $regras = [
            "data_chegada" => [
                "label" => "Data",
                "rules" => "required|valid_date[Y-m-d]"
            ],
            "hora_chegada" => [
                "label" => "Horário de chegada",
                "rules" => "required|regex_match[/^\d{2}:\d{2}$/]"
            ],
            "acompanhante_nome" => [
                "label" => "Nome do acompanhante",
                "rules" => "permit_empty|required_with[acompanhante_parentesco,acompanhante_telefone]|min_length[3]|max_length[150]"
            ],
            "acompanhante_parentesco" => [
                "label" => "Parentesco",
                "rules" => "permit_empty|max_length[50]"
            ],
            "acompanhante_telefone" => [
                "label" => "Telefone do acompanhante",
                "rules" => "permit_empty|min_length[8]|max_length[20]"
            ]
        ];

        $dados = $this->request->getPost(["data_chegada", "hora_chegada", "observacao_inicial", "acompanhante_nome", "acompanhante_parentesco", "acompanhante_telefone"]);

        if (!$this->validate($regras)) {
            return $this->exibeForm($paciente, $dados, $this->validator->getErrors());
        }

        $dataHoraChegada = $dados["data_chegada"] . " " . $dados["hora_chegada"] . ":00";

        if (strtotime($dataHoraChegada) > time()) {
            return $this->exibeForm($paciente, $dados, ["hora_chegada" => "A chegada não pode ser posterior ao horário atual."]);
        }

        $atendimento = [
            "id_paciente"         => $idPaciente,
            "id_usuario_admissao" => session()->get("usuarioId"),
            "numero"              => $this->AtendimentoModel->gerarNumero(),
            "data_hora_chegada"   => $dataHoraChegada,
            "status"              => "aguardando_triagem",
            "observacao_inicial"  => trim((string) $dados["observacao_inicial"]),
        ];

        $db = \Config\Database::connect();

        $db->transBegin();

        if (!$this->AtendimentoModel->save($atendimento)) {
            $db->transRollback();

            return $this->exibeForm($paciente, $dados, $this->AtendimentoModel->errors());
        }

        $id = $this->AtendimentoModel->getInsertID();

        if (trim((string) $dados["acompanhante_nome"]) != "") {
            $gravou = $this->AcompanhanteModel->insert([
                "id_atendimento"    => $id,
                "id_usuario"        => session()->get("usuarioId"),
                "nome"              => trim($dados["acompanhante_nome"]),
                "parentesco"        => trim((string) $dados["acompanhante_parentesco"]),
                "telefone"          => trim((string) $dados["acompanhante_telefone"]),
                "data_hora_entrada" => $dataHoraChegada,
            ]);

            if (!$gravou) {
                $db->transRollback();

                return $this->exibeForm($paciente, $dados, ["acompanhante_nome" => implode(" ", $this->AcompanhanteModel->errors())]);
            }
        }

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz o que foi feito na base de dados
            session()->setFlashdata("msgError", "Não foi possível registrar a admissão. Tente novamente.");

            return redirect()->to("admissao/form/" . $idPaciente);
        }

        $db->transCommit();             // Confirma a gravação dos dados na base de dados

        $this->gravaLog("Admissão registrada", "atendimento", $id, "Atendimento " . $atendimento["numero"] . " - " . $paciente["nome_completo"]);
        session()->setFlashdata("msgSucesso", "Admissão registrada. Atendimento " . $atendimento["numero"] . " aguardando triagem.");

        if (session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM) {
            return redirect()->to("triagem/form/" . $id);
        }

        return redirect()->to("pacientes/historico/" . $idPaciente);
    }

    /**
     * Monta a tela de admissão.
     *
     * @param array $paciente
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($paciente, $dados, $erros)
    {
        $titulo            = "Admissão do paciente";
        $subtitulo         = "Abertura de um novo atendimento.";
        $paginaAtiva       = "pacientes";
        $numeroAtendimento = $this->AtendimentoModel->gerarNumero();

        return view("pacientes/admissao", compact("titulo", "subtitulo", "paginaAtiva", "paciente", "numeroAtendimento", "dados", "erros"));
    }
}
