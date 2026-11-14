<?php

namespace App\Models;

class EvolucaoMedicaModel extends BaseModel
{
    protected $table = "evolucao_medica";
    protected $primaryKey = "id_evolucao";
    protected $allowedFields = [
        "id_atendimento",
        "id_medico",
        "data_hora",
        "evolucao",
        "observacoes",
        "hipotese_diagnostica",
        "conclusao_clinica"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_medico" => [
            "label" => "Médico",
            "rules" => "required|is_natural_no_zero"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "evolucao" => [
            "label" => "Evolução",
            "rules" => "required|min_length[3]|max_length[16000]"
        ]
    ];

    /**
     * Evoluções do atendimento com o nome do médico (mais recentes primeiro).
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("evolucao_medica.*, usuario.nome AS medico")
            ->join("usuario", "usuario.id_usuario = evolucao_medica.id_medico")
            ->where("evolucao_medica.id_atendimento", $idAtendimento)
            ->orderBy("evolucao_medica.data_hora", "DESC")
            ->findAll();
    }
}
