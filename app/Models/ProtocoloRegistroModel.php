<?php

namespace App\Models;

class ProtocoloRegistroModel extends BaseModel
{
    protected $table = "protocolo_registro";
    protected $primaryKey = "id_registro";
    protected $allowedFields = [
        "id_protocolo_atendimento",
        "id_etapa",
        "id_usuario",
        "data_hora"
    ];

    protected $validationRules = [
        "id_protocolo_atendimento" => [
            "label" => "Protocolo",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_etapa" => [
            "label" => "Etapa",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_usuario" => [
            "label" => "Responsável",
            "rules" => "required|is_natural_no_zero"
        ],
        "data_hora" => [
            "label" => "Horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    /**
     * Etapas registradas nos protocolos do atendimento, com quem registrou.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("protocolo_registro.*, usuario.nome AS usuario")
            ->join("protocolo_atendimento", "protocolo_atendimento.id_protocolo_atendimento = protocolo_registro.id_protocolo_atendimento")
            ->join("usuario", "usuario.id_usuario = protocolo_registro.id_usuario")
            ->where("protocolo_atendimento.id_atendimento", $idAtendimento)
            ->findAll();
    }

    /**
     * Informa se a etapa já foi registrada no protocolo do paciente.
     *
     * @param int $idProtocoloAtendimento
     * @param int $idEtapa
     * @return bool
     */
    public function existe(int $idProtocoloAtendimento, int $idEtapa): bool
    {
        return $this->where("id_protocolo_atendimento", $idProtocoloAtendimento)
            ->where("id_etapa", $idEtapa)
            ->countAllResults() > 0;
    }
}
