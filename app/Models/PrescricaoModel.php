<?php

namespace App\Models;

class PrescricaoModel extends BaseModel
{
    protected $table = "prescricao";
    protected $primaryKey = "id_prescricao";
    protected $allowedFields = [
        "id_atendimento",
        "id_medico",
        "data_hora",
        "observacoes",
        "status"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_medico" => [
            "label" => "Prescritor",
            "rules" => "required|is_natural_no_zero"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "status" => [
            "label" => "Situação",
            "rules" => "required|max_length[30]"
        ]
    ];

    /**
     * Retorna a prescrição do médico no atendimento (ou array vazio).
     *
     * @param int $idAtendimento
     * @param int $idMedico
     * @return array
     */
    public function getPrescricao(int $idAtendimento, int $idMedico): array
    {
        return $this->where("id_atendimento", $idAtendimento)
            ->where("id_medico", $idMedico)
            ->first() ?? [];
    }
}
