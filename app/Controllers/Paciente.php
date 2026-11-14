<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\PacienteModel;

class Paciente extends BaseController
{
    private $PacienteModel;
    private $AtendimentoModel;

    public function __construct()
    {
        $this->PacienteModel    = new PacienteModel();
        $this->AtendimentoModel = new AtendimentoModel();
    }

    /**
     * Lista e pesquisa os pacientes (nome, nome social, CPF ou CNS).
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Pacientes";
        $subtitulo   = "Consulte os pacientes cadastrados e inicie uma nova admissão.";
        $paginaAtiva = "pacientes";
        $busca       = trim((string) $this->request->getGet("busca"));
        $pacientes   = $this->PacienteModel->pesquisar($busca);
        $podeAdmitir = in_array(session()->get("usuarioPerfilId"), [PERFIL_ADMINISTRADOR, PERFIL_ENFERMAGEM]);

        return view("pacientes/index", compact("titulo", "subtitulo", "paginaAtiva", "busca", "pacientes", "podeAdmitir"));
    }

    /**
     * Exibe o formulário de inclusão/alteração de paciente.
     *
     * @param string $action
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($action = "insert", $id = 0)
    {
        $dados = ($id > 0) ? $this->PacienteModel->getById($id) : [];
        $erros = [];

        if (!in_array($action, ["insert", "update"]) || ($action == "update" && empty($dados))) {
            session()->setFlashdata("msgError", "Paciente não encontrado.");

            return redirect()->to("pacientes");
        }

        return $this->exibeForm($action, $dados, $erros);
    }

    /**
     * Grava (insere ou atualiza) um paciente.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $action = $this->request->getPost("action");
        $id     = (int) $this->request->getPost("id_paciente");
        $atual  = ($id > 0) ? $this->PacienteModel->getById($id) : [];

        if ($id > 0 && empty($atual)) {
            session()->setFlashdata("msgError", "Paciente não encontrado.");

            return redirect()->to("pacientes");
        }

        // Só deixa de ser não identificado ao confirmar a identidade
        $confirmouIdentidade = !empty($atual["nao_identificado"]) && $this->request->getPost("identidade_confirmada") == "1";

        $dados = [
            "id_paciente"      => $id > 0 ? $id : null,
            "nome_completo"    => trim((string) $this->request->getPost("nome_completo")),
            "nome_social"      => trim((string) $this->request->getPost("nome_social")),
            "cpf"              => formataCpf($this->request->getPost("cpf")),
            "cns"              => preg_replace("/\D/", "", (string) $this->request->getPost("cns")),
            "data_nascimento"  => (string) $this->request->getPost("data_nascimento"),
            "nome_mae"         => trim((string) $this->request->getPost("nome_mae")),
            "sexo"             => $this->request->getPost("sexo"),
            "telefone"         => trim((string) $this->request->getPost("telefone")),
            "endereco"         => trim((string) $this->request->getPost("endereco")),
            "numero"           => trim((string) $this->request->getPost("numero")),
            "bairro"           => trim((string) $this->request->getPost("bairro")),
            "cidade"           => trim((string) $this->request->getPost("cidade")),
            "estado"           => strtoupper(trim((string) $this->request->getPost("estado"))),
            "nome_contato"     => trim((string) $this->request->getPost("nome_contato")),
            "telefone_contato" => trim((string) $this->request->getPost("telefone_contato")),
            "alergias"         => trim((string) $this->request->getPost("alergias")),
            "nao_identificado" => (!empty($atual["nao_identificado"]) && !$confirmouIdentidade) ? 1 : 0,
            "caracteristicas"  => $this->request->getPost("caracteristicas") !== null ? trim((string) $this->request->getPost("caracteristicas")) : ($atual["caracteristicas"] ?? null),
        ];

        foreach (["nome_social", "cpf", "cns", "data_nascimento", "nome_mae", "telefone", "alergias", "caracteristicas"] as $campo) {
            if ($dados[$campo] === "") {
                $dados[$campo] = null;
            }
        }

        $erros = [];

        if (!empty($dados["data_nascimento"]) && $dados["data_nascimento"] > date("Y-m-d")) {
            $erros["data_nascimento"] = "A data de nascimento não pode ser posterior à data atual.";
        }

        if ($confirmouIdentidade && mb_stripos($dados["nome_completo"], "Não identificado") === 0) {
            $erros["nome_completo"] = "Informe o nome real do paciente para confirmar a identidade.";
        }

        if (empty($erros) && !$this->PacienteModel->save($dados)) {
            $erros = $this->PacienteModel->errors();
        }

        if (!empty($erros)) {
            $dados["identidade_confirmada"] = $confirmouIdentidade ? 1 : 0;
            $dados["nao_identificado"]      = $atual["nao_identificado"] ?? 0;

            return $this->exibeForm($action, $dados, $erros);
        }

        if ($id > 0) {
            $this->gravaLog($confirmouIdentidade ? "Paciente identificado" : "Paciente alterado", "paciente", $id, $dados["nome_completo"]);
            session()->setFlashdata("msgSucesso", $confirmouIdentidade ? "Identidade do paciente confirmada." : "Paciente alterado com sucesso.");

            return redirect()->to("pacientes");
        }

        $id = $this->PacienteModel->getInsertID();

        $this->gravaLog("Paciente cadastrado", "paciente", $id, $dados["nome_completo"]);
        session()->setFlashdata("msgSucesso", "Paciente cadastrado com sucesso. Registre a admissão para iniciar o atendimento.");

        return redirect()->to("admissao/form/" . $id);
    }

    /**
     * Exibe o cadastro rápido de paciente não identificado.
     *
     * @return string
     */
    public function naoIdentificado()
    {
        return $this->exibeNaoIdentificado([], []);
    }

    /**
     * Cadastra o paciente não identificado e segue para a admissão.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function storeNaoIdentificado()
    {
        $dados = [
            "sexo"            => $this->request->getPost("sexo"),
            "caracteristicas" => trim((string) $this->request->getPost("caracteristicas")),
        ];

        $id = $this->PacienteModel->cadastraNaoIdentificado($dados);

        if ($id == 0) {
            return $this->exibeNaoIdentificado($dados, $this->PacienteModel->errors());
        }

        $paciente = $this->PacienteModel->getById($id);

        $this->gravaLog("Paciente não identificado cadastrado", "paciente", $id, $paciente["nome_completo"]);
        session()->setFlashdata("msgSucesso", "Paciente cadastrado como " . $paciente["nome_completo"] . ". Registre a admissão; os dados podem ser completados depois.");

        return redirect()->to("admissao/form/" . $id);
    }

    /**
     * Exibe o histórico de atendimentos do paciente.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function historico($id)
    {
        $paciente = $this->PacienteModel->getById($id);

        if (empty($paciente)) {
            session()->setFlashdata("msgError", "Paciente não encontrado.");

            return redirect()->to("pacientes");
        }

        $titulo       = "Histórico do paciente";
        $subtitulo    = "Atendimentos anteriores do paciente.";
        $paginaAtiva  = "pacientes";
        $atendimentos = $this->AtendimentoModel->historicoPaciente($id);
        $podeAdmitir  = in_array(session()->get("usuarioPerfilId"), [PERFIL_ADMINISTRADOR, PERFIL_ENFERMAGEM]);

        $temAtendimentoAberto = !empty($this->AtendimentoModel->getAberto($id));

        return view("pacientes/historico", compact("titulo", "subtitulo", "paginaAtiva", "paciente", "atendimentos", "podeAdmitir", "temAtendimentoAberto"));
    }

    /**
     * Monta o formulário de cadastro.
     *
     * @param string $action
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($action, $dados, $erros)
    {
        $titulo      = "Cadastro de paciente" . subTitulo($action);
        $subtitulo   = "Dados de identificação do paciente.";
        $paginaAtiva = "pacientes";

        return view("pacientes/cadastro", compact("titulo", "subtitulo", "paginaAtiva", "action", "dados", "erros"));
    }

    /**
     * Monta o cadastro rápido de paciente não identificado.
     *
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeNaoIdentificado($dados, $erros)
    {
        $titulo      = "Paciente não identificado";
        $subtitulo   = "Cadastro rápido de paciente sem identificação.";
        $paginaAtiva = "pacientes";

        return view("pacientes/naoIdentificado", compact("titulo", "subtitulo", "paginaAtiva", "dados", "erros"));
    }
}
