<?php

namespace App\Models;

class ReclassificacaoModel extends BaseModel
{
    protected $table = "reclassificacao";
    protected $primaryKey = "id_reclassificacao";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "id_classificacao_anterior",
        "id_classificacao_nova",
        "motivo",
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
        "id_classificacao_anterior" => [
            "label" => "Classificação anterior",
            "rules" => "required|is_not_unique[classificacao_risco.id_classificacao]"
        ],
        "id_classificacao_nova" => [
            "label" => "Nova classificação",
            "rules" => "required|is_not_unique[classificacao_risco.id_classificacao]|differs[id_classificacao_anterior]"
        ],
        "motivo" => [
            "label" => "Motivo",
            "rules" => "required|min_length[5]|max_length[16000]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    protected $validationMessages = [
        "id_classificacao_nova" => [
            "differs" => "Escolha uma classificação diferente da atual."
        ]
    ];

    /**
     * Reclassificações do atendimento, com as cores e o profissional responsável.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("reclassificacao.*, usuario.nome AS usuario, perfil.nome AS perfil")
            ->select("anterior.nome AS classificacao_anterior, anterior.cor AS cor_anterior, nova.nome AS classificacao_nova, nova.cor AS cor_nova")
            ->join("usuario", "usuario.id_usuario = reclassificacao.id_usuario")
            ->join("perfil", "perfil.id_perfil = usuario.id_perfil")
            ->join("classificacao_risco AS anterior", "anterior.id_classificacao = reclassificacao.id_classificacao_anterior")
            ->join("classificacao_risco AS nova", "nova.id_classificacao = reclassificacao.id_classificacao_nova")
            ->where("reclassificacao.id_atendimento", $idAtendimento)
            ->orderBy("reclassificacao.data_hora")
            ->orderBy("reclassificacao.id_reclassificacao")
            ->findAll();
    }
}
