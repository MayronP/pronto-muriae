<?php

namespace App\Models;

class ChamadaModel extends BaseModel
{
    protected $table = "chamada";
    protected $primaryKey = "id_chamada";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "local",
        "data_hora"
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
        "local" => [
            "label" => "Local de atendimento",
            "rules" => "required|max_length[60]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    /**
     * Últimas chamadas do dia para o painel (mais recente primeiro).
     *
     * @param int $limite
     * @return array
     */
    public function ultimasDoDia(int $limite = 7): array
    {
        return $this->select("chamada.*, atendimento.numero, paciente.nome_completo, paciente.nome_social, paciente.nao_identificado")
            ->join("atendimento", "atendimento.id_atendimento = chamada.id_atendimento")
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->where("chamada.data_hora >=", date("Y-m-d 00:00:00"))
            ->orderBy("chamada.data_hora", "DESC")
            ->orderBy("chamada.id_chamada", "DESC")
            ->findAll($limite);
    }
}
