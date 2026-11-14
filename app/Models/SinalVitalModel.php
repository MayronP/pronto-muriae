<?php

namespace App\Models;

class SinalVitalModel extends BaseModel
{
    protected $table = "sinal_vital";
    protected $primaryKey = "id_sinal_vital";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "data_hora",
        "pressao_arterial",
        "temperatura",
        "frequencia_cardiaca",
        "frequencia_respiratoria",
        "saturacao",
        "oxigenio_suplementar",
        "nivel_consciencia",
        "dor",
        "glicemia",
        "observacoes"
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
        "pressao_arterial" => [
            "label" => "Pressão arterial",
            "rules" => "required|regex_match[/^\d{2,3}\/\d{2,3}$/]"
        ],
        "temperatura" => [
            "label" => "Temperatura",
            "rules" => "required|decimal|greater_than_equal_to[30]|less_than_equal_to[45]"
        ],
        "frequencia_cardiaca" => [
            "label" => "Frequência cardíaca",
            "rules" => "required|is_natural_no_zero|less_than_equal_to[300]"
        ],
        "frequencia_respiratoria" => [
            "label" => "Frequência respiratória",
            "rules" => "required|is_natural_no_zero|less_than_equal_to[100]"
        ],
        "saturacao" => [
            "label" => "Saturação",
            "rules" => "required|is_natural|less_than_equal_to[100]"
        ],
        "oxigenio_suplementar" => [
            "label" => "Oxigênio suplementar",
            "rules" => "required|in_list[0,1]"
        ],
        "nivel_consciencia" => [
            "label" => "Nível de consciência",
            "rules" => "required|in_list[A,C,V,P,U]"
        ],
        "dor" => [
            "label" => "Dor",
            "rules" => "permit_empty|is_natural|less_than_equal_to[10]"
        ],
        "glicemia" => [
            "label" => "Glicemia capilar",
            "rules" => "permit_empty|is_natural_no_zero|less_than_equal_to[2000]"
        ],
        "observacoes" => [
            "label" => "Observações",
            "rules" => "permit_empty|max_length[255]"
        ]
    ];

    protected $validationMessages = [
        "pressao_arterial" => [
            "regex_match" => "Informe a pressão arterial no formato 120/80."
        ]
    ];

    /**
     * Aferições do atendimento (mais recente primeiro), com quem aferiu.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("sinal_vital.*, usuario.nome AS usuario")
            ->join("usuario", "usuario.id_usuario = sinal_vital.id_usuario")
            ->where("sinal_vital.id_atendimento", $idAtendimento)
            ->orderBy("sinal_vital.data_hora", "DESC")
            ->orderBy("sinal_vital.id_sinal_vital", "DESC")
            ->findAll();
    }
}
