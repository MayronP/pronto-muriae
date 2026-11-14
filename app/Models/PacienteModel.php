<?php

namespace App\Models;

class PacienteModel extends BaseModel
{
    protected $table = "paciente";
    protected $primaryKey = "id_paciente";
    protected $allowedFields = [
        "nome_completo",
        "nome_social",
        "cpf",
        "cns",
        "data_nascimento",
        "nome_mae",
        "sexo",
        "telefone",
        "endereco",
        "numero",
        "bairro",
        "cidade",
        "estado",
        "nome_contato",
        "telefone_contato",
        "alergias",
        "nao_identificado",
        "caracteristicas"
    ];

    protected $validationRules = [
        "id_paciente" => [
            "label" => "Id",
            "rules" => "permit_empty|is_natural_no_zero"
        ],
        "nome_completo" => [
            "label" => "Nome completo",
            "rules" => "required|min_length[3]|max_length[150]"
        ],
        "nome_social" => [
            "label" => "Nome social",
            "rules" => "permit_empty|max_length[150]"
        ],
        "cpf" => [
            "label" => "CPF",
            "rules" => "permit_empty|regex_match[/^\d{3}\.\d{3}\.\d{3}-\d{2}$/]|cpf_valido|is_unique[paciente.cpf,id_paciente,{id_paciente}]"
        ],
        "cns" => [
            "label" => "Cartão Nacional de Saúde",
            "rules" => "permit_empty|regex_match[/^\d{15}$/]|is_unique[paciente.cns,id_paciente,{id_paciente}]"
        ],
        "data_nascimento" => [
            "label" => "Data de nascimento",
            "rules" => "permit_empty|required_without[nao_identificado]|valid_date[Y-m-d]"
        ],
        "nome_mae" => [
            "label" => "Nome da mãe",
            "rules" => "permit_empty|max_length[150]"
        ],
        "sexo" => [
            "label" => "Sexo",
            "rules" => "permit_empty|in_list[F,M,O,N]"
        ],
        "telefone" => [
            "label" => "Telefone",
            "rules" => "permit_empty|min_length[8]|max_length[20]"
        ],
        "endereco" => [
            "label" => "Endereço",
            "rules" => "permit_empty|max_length[180]"
        ],
        "numero" => [
            "label" => "Número",
            "rules" => "permit_empty|max_length[20]"
        ],
        "bairro" => [
            "label" => "Bairro",
            "rules" => "permit_empty|max_length[100]"
        ],
        "cidade" => [
            "label" => "Cidade",
            "rules" => "permit_empty|max_length[100]"
        ],
        "estado" => [
            "label" => "Estado",
            "rules" => "permit_empty|alpha|exact_length[2]"
        ],
        "nome_contato" => [
            "label" => "Nome do contato",
            "rules" => "permit_empty|max_length[150]"
        ],
        "telefone_contato" => [
            "label" => "Telefone do contato",
            "rules" => "permit_empty|min_length[8]|max_length[20]"
        ],
        "alergias" => [
            "label" => "Alergias",
            "rules" => "permit_empty|max_length[500]"
        ],
        "nao_identificado" => [
            "label" => "Paciente não identificado",
            "rules" => "permit_empty|in_list[0,1]"
        ],
        "caracteristicas" => [
            "label" => "Características",
            "rules" => "permit_empty|required_with[nao_identificado]|max_length[255]"
        ]
    ];

    protected $validationMessages = [
        "cpf" => [
            "regex_match" => "Informe um CPF com 11 dígitos.",
            "is_unique"   => "Já existe um paciente cadastrado com este CPF."
        ],
        "cns" => [
            "regex_match" => "O Cartão Nacional de Saúde tem 15 dígitos.",
            "is_unique"   => "Já existe um paciente cadastrado com este Cartão Nacional de Saúde."
        ],
        "data_nascimento" => [
            "required_without" => "Informe a data de nascimento."
        ],
        "caracteristicas" => [
            "required_with" => "Descreva as características que ajudam a reconhecer o paciente não identificado."
        ]
    ];

    /**
     * Pesquisa pacientes por nome, nome social, CPF ou CNS.
     *
     * @param string $busca
     * @return array
     */
    public function pesquisar(string $busca = ""): array
    {
        $this->select("paciente.*, MAX(atendimento.data_hora_chegada) AS ultimo_atendimento, MAX(IF(atendimento.status <> 'encerrado', atendimento.status, NULL)) AS situacao", false)
            ->join("atendimento", "atendimento.id_paciente = paciente.id_paciente", "left");

        if ($busca != "") {
            $this->groupStart()
                ->like("paciente.nome_completo", $busca)
                ->orLike("paciente.nome_social", $busca)
                ->orLike("paciente.cpf", formataCpf($busca))
                ->orLike("paciente.cns", $busca)
            ->groupEnd();
        }

        return $this->groupBy("paciente.id_paciente")
            ->orderBy("paciente.nao_identificado", "DESC")
            ->orderBy("paciente.nome_completo")
            ->findAll(100);
    }

    /**
     * Cadastra um paciente não identificado (nome provisório com o id).
     *
     * @param array $dados
     * @return int
     */
    public function cadastraNaoIdentificado(array $dados): int
    {
        $dados["nome_completo"]    = "Não identificado";
        $dados["nao_identificado"] = 1;

        $db = \Config\Database::connect();

        $db->transBegin();      // Inicia controle de transação

        if (!$this->save($dados)) {
            $db->transRollback();

            return 0;
        }

        $id = (int) $this->getInsertID();

        $this->update($id, [
            "nome_completo" => "Não identificado " . str_pad($id, 4, "0", STR_PAD_LEFT) . " (" . date("d/m/Y") . ")",
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();       // Desfaz

            return 0;
        }

        $db->transCommit();             // Confirma

        return $id;
    }
}
