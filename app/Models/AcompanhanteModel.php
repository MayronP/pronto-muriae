<?php

namespace App\Models;

class AcompanhanteModel extends BaseModel
{
    protected $table = "acompanhante";
    protected $primaryKey = "id_acompanhante";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "nome",
        "parentesco",
        "documento",
        "telefone",
        "data_hora_entrada",
        "data_hora_saida"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_usuario" => [
            "label" => "Responsável",
            "rules" => "required|is_natural_no_zero"
        ],
        "nome" => [
            "label" => "Nome do acompanhante",
            "rules" => "required|min_length[3]|max_length[150]"
        ],
        "parentesco" => [
            "label" => "Parentesco",
            "rules" => "permit_empty|max_length[50]"
        ],
        "documento" => [
            "label" => "Documento",
            "rules" => "permit_empty|max_length[30]"
        ],
        "telefone" => [
            "label" => "Telefone",
            "rules" => "permit_empty|min_length[8]|max_length[20]"
        ],
        "data_hora_entrada" => [
            "label" => "Entrada",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    /**
     * Acompanhantes do atendimento (o atual primeiro).
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("acompanhante.*, usuario.nome AS usuario")
            ->join("usuario", "usuario.id_usuario = acompanhante.id_usuario")
            ->where("acompanhante.id_atendimento", $idAtendimento)
            ->orderBy("acompanhante.data_hora_saida IS NULL", "DESC", false)
            ->orderBy("acompanhante.data_hora_entrada", "DESC")
            ->findAll();
    }

    /**
     * Registra a saída do acompanhante atual do atendimento.
     *
     * @param int $idAtendimento
     * @return bool
     */
    public function registraSaida(int $idAtendimento): bool
    {
        return $this->where("id_atendimento", $idAtendimento)
            ->where("data_hora_saida", null)
            ->set(["data_hora_saida" => date("Y-m-d H:i:s")])
            ->update();
    }
}
