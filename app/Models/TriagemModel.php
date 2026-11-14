<?php

namespace App\Models;

class TriagemModel extends BaseModel
{
    protected $table = "triagem";
    protected $primaryKey = "id_triagem";
    protected $allowedFields = [
        "id_atendimento",
        "id_enfermeiro",
        "id_classificacao",
        "queixa_principal",
        "observacoes_iniciais",
        "pressao_arterial",
        "temperatura",
        "frequencia_cardiaca",
        "frequencia_respiratoria",
        "saturacao",
        "oxigenio_suplementar",
        "nivel_consciencia",
        "gestante",
        "dor",
        "glicemia",
        "peso",
        "data_hora",
        "data_hora_reclassificacao"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero|is_unique[triagem.id_atendimento]"
        ],
        "id_enfermeiro" => [
            "label" => "Responsável pela triagem",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_classificacao" => [
            "label" => "Classificação de risco",
            "rules" => "required|is_not_unique[classificacao_risco.id_classificacao]"
        ],
        "queixa_principal" => [
            "label" => "Queixa principal",
            "rules" => "required|min_length[3]|max_length[16000]"
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
        "gestante" => [
            "label" => "Gestante",
            "rules" => "required|in_list[0,1]"
        ],
        "dor" => [
            "label" => "Dor",
            "rules" => "permit_empty|is_natural|less_than_equal_to[10]"
        ],
        "glicemia" => [
            "label" => "Glicemia capilar",
            "rules" => "permit_empty|is_natural_no_zero|less_than_equal_to[2000]"
        ],
        "peso" => [
            "label" => "Peso",
            "rules" => "permit_empty|decimal|greater_than[0]|less_than_equal_to[400]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    protected $validationMessages = [
        "id_atendimento" => [
            "is_unique" => "Este atendimento já possui triagem registrada."
        ],
        "pressao_arterial" => [
            "regex_match" => "Informe a pressão arterial no formato 120/80."
        ]
    ];

    /**
     * Troca a classificação vigente e registra o horário da reclassificação.
     *
     * @param int $idAtendimento
     * @param int $idClassificacao
     * @param string $dataHora
     * @return bool
     */
    public function reclassifica(int $idAtendimento, int $idClassificacao, string $dataHora): bool
    {
        return $this->where("id_atendimento", $idAtendimento)
            ->set([
                "id_classificacao"          => $idClassificacao,
                "data_hora_reclassificacao" => $dataHora,
            ])
            ->update();
    }
}
