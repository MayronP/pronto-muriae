<?php

namespace App\Models;

class PassagemPlantaoModel extends BaseModel
{
    protected $table = "passagem_plantao";
    protected $primaryKey = "id_passagem";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "situacao",
        "historico",
        "avaliacao",
        "recomendacao",
        "data_hora",
        "id_usuario_recebimento",
        "data_hora_recebimento"
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
        "situacao" => [
            "label" => "Situação",
            "rules" => "required|min_length[3]|max_length[16000]"
        ],
        "avaliacao" => [
            "label" => "Avaliação",
            "rules" => "required|min_length[3]|max_length[16000]"
        ],
        "recomendacao" => [
            "label" => "Recomendação",
            "rules" => "required|min_length[3]|max_length[16000]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    /**
     * Passagens de plantão do atendimento, com quem passou e quem recebeu.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("passagem_plantao.*, autor.nome AS autor, perfil.nome AS perfil_autor, recebedor.nome AS recebedor")
            ->join("usuario AS autor", "autor.id_usuario = passagem_plantao.id_usuario")
            ->join("perfil", "perfil.id_perfil = autor.id_perfil")
            ->join("usuario AS recebedor", "recebedor.id_usuario = passagem_plantao.id_usuario_recebimento", "left")
            ->where("passagem_plantao.id_atendimento", $idAtendimento)
            ->orderBy("passagem_plantao.data_hora", "DESC")
            ->orderBy("passagem_plantao.id_passagem", "DESC")
            ->findAll();
    }

    /**
     * Passagem com o perfil de quem a registrou.
     *
     * @param int $id
     * @return array
     */
    public function getComAutor(int $id): array
    {
        return $this->select("passagem_plantao.*, autor.id_perfil AS id_perfil_autor, atendimento.numero, atendimento.status")
            ->join("usuario AS autor", "autor.id_usuario = passagem_plantao.id_usuario")
            ->join("atendimento", "atendimento.id_atendimento = passagem_plantao.id_atendimento")
            ->where("passagem_plantao.id_passagem", $id)
            ->first() ?? [];
    }

    /**
     * Confirma o recebimento, somente se ainda não foi confirmado.
     *
     * @param int $id
     * @param int $idUsuario
     * @return bool
     */
    public function confirmaRecebimento(int $id, int $idUsuario): bool
    {
        return $this->where("id_passagem", $id)
            ->where("id_usuario_recebimento", null)
            ->set([
                "id_usuario_recebimento" => $idUsuario,
                "data_hora_recebimento"  => date("Y-m-d H:i:s"),
            ])
            ->update() && $this->db->affectedRows() == 1;
    }
}
