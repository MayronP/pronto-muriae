<?php

namespace App\Models;

class ExameModel extends BaseModel
{
    protected $table = "exame";
    protected $primaryKey = "id_exame";
    protected $allowedFields = [
        "id_atendimento",
        "id_solicitante",
        "id_usuario_resultado",
        "descricao",
        "data_hora_solicitacao",
        "status",
        "resultado",
        "data_hora_resultado"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_solicitante" => [
            "label" => "Responsável",
            "rules" => "required|is_natural_no_zero"
        ],
        "descricao" => [
            "label" => "Exame",
            "rules" => "required|min_length[2]|max_length[180]"
        ],
        "data_hora_solicitacao" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "status" => [
            "label" => "Situação",
            "rules" => "required|in_list[solicitado,concluido]"
        ],
        "resultado" => [
            "label" => "Resultado",
            "rules" => "permit_empty|min_length[2]|max_length[16000]"
        ]
    ];

    /**
     * Exames do atendimento com o nome do solicitante.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("exame.*, solicitante.nome AS solicitante, responsavel.nome AS responsavel_resultado")
            ->join("usuario AS solicitante", "solicitante.id_usuario = exame.id_solicitante")
            ->join("usuario AS responsavel", "responsavel.id_usuario = exame.id_usuario_resultado", "left")
            ->where("exame.id_atendimento", $idAtendimento)
            ->orderBy("exame.data_hora_solicitacao")
            ->findAll();
    }
}
