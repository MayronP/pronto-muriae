<?php

namespace App\Models;

class ClassificacaoRiscoModel extends BaseModel
{
    protected $table = "classificacao_risco";
    protected $primaryKey = "id_classificacao";
    protected $allowedFields = [
        "nome",
        "cor",
        "ordem_prioridade",
        "tempo_alvo_minutos",
        "ativo"
    ];

    protected $validationRules = [
        "nome" => [
            "label" => "Nome",
            "rules" => "required|max_length[50]"
        ],
        "cor" => [
            "label" => "Cor",
            "rules" => "required|max_length[20]"
        ],
        "ordem_prioridade" => [
            "label" => "Ordem de prioridade",
            "rules" => "required|is_natural_no_zero"
        ],
        "tempo_alvo_minutos" => [
            "label" => "Tempo-alvo",
            "rules" => "required|is_natural|less_than_equal_to[240]"
        ]
    ];

    /**
     * Classificações ativas, da mais urgente para a menos urgente.
     *
     * @return array
     */
    public function listaAtivas(): array
    {
        return $this->where("ativo", 1)
            ->orderBy("ordem_prioridade")
            ->findAll();
    }
}
