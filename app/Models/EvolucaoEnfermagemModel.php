<?php

namespace App\Models;

class EvolucaoEnfermagemModel extends BaseModel
{
    protected $table = "evolucao_enfermagem";
    protected $primaryKey = "id_evolucao_enfermagem";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "data_hora",
        "avaliacao",
        "diagnostico",
        "planejamento",
        "implementacao",
        "evolucao"
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
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "avaliacao" => [
            "label" => "Avaliação de enfermagem",
            "rules" => "permit_empty|required_without[implementacao]|min_length[3]|max_length[16000]"
        ],
        "implementacao" => [
            "label" => "Cuidados realizados",
            "rules" => "permit_empty|required_without[avaliacao]|min_length[3]|max_length[16000]"
        ]
    ];

    protected $validationMessages = [
        "avaliacao" => [
            "required_without" => "Registre a avaliação de enfermagem ou os cuidados realizados."
        ],
        "implementacao" => [
            "required_without" => "Registre a avaliação de enfermagem ou os cuidados realizados."
        ]
    ];

    /**
     * Registros de enfermagem do atendimento (mais recente primeiro).
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("evolucao_enfermagem.*, usuario.nome AS usuario, usuario.registro_profissional")
            ->join("usuario", "usuario.id_usuario = evolucao_enfermagem.id_usuario")
            ->where("evolucao_enfermagem.id_atendimento", $idAtendimento)
            ->orderBy("evolucao_enfermagem.data_hora", "DESC")
            ->orderBy("evolucao_enfermagem.id_evolucao_enfermagem", "DESC")
            ->findAll();
    }
}
