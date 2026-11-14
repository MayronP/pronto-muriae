<?php

namespace App\Models;

class MedicamentoModel extends BaseModel
{
    protected $table = "medicamento";
    protected $primaryKey = "id_medicamento";
    protected $allowedFields = [
        "nome",
        "apresentacao",
        "ativo"
    ];

    protected $validationRules = [
        "nome" => [
            "label" => "Medicamento",
            "rules" => "required|min_length[2]|max_length[180]"
        ],
        "apresentacao" => [
            "label" => "Apresentação",
            "rules" => "permit_empty|max_length[150]"
        ]
    ];

    /**
     * Nomes já usados nas prescrições, para sugerir no campo Medicamento.
     *
     * @return array
     */
    public function listaNomes(): array
    {
        $medicamentos = $this->select("nome")
            ->distinct()
            ->where("ativo", 1)
            ->orderBy("nome")
            ->findAll();

        return array_column($medicamentos, "nome");
    }

    /**
     * Retorna o medicamento pelo nome e apresentação (ou array vazio).
     *
     * @param string $nome
     * @param string $apresentacao
     * @return array
     */
    public function getMedicamento(string $nome, string $apresentacao): array
    {
        return $this->where("nome", $nome)
            ->where("apresentacao", $apresentacao)
            ->first() ?? [];
    }
}
