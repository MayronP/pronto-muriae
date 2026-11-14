<?php

namespace App\Models;

class ProtocoloAtendimentoModel extends BaseModel
{
    protected $table = "protocolo_atendimento";
    protected $primaryKey = "id_protocolo_atendimento";
    protected $allowedFields = [
        "id_atendimento",
        "id_protocolo",
        "id_usuario",
        "data_hora_abertura",
        "inicio_sintomas",
        "situacao",
        "data_hora_encerramento",
        "observacao"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_protocolo" => [
            "label" => "Protocolo",
            "rules" => "required|is_not_unique[protocolo.id_protocolo]"
        ],
        "id_usuario" => [
            "label" => "Responsável",
            "rules" => "required|is_natural_no_zero"
        ],
        "data_hora_abertura" => [
            "label" => "Abertura",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "inicio_sintomas" => [
            "label" => "Início dos sintomas",
            "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"
        ],
        "situacao" => [
            "label" => "Situação",
            "rules" => "required|in_list[aberto,concluido,descartado]"
        ],
        "observacao" => [
            "label" => "Observação",
            "rules" => "permit_empty|max_length[255]"
        ]
    ];

    /**
     * Protocolos do atendimento, com o protocolo e quem abriu.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("protocolo_atendimento.*, protocolo.nome AS protocolo, protocolo.marco_inicial, protocolo.janela_minutos, protocolo.fonte, usuario.nome AS usuario")
            ->select("atendimento.data_hora_chegada")
            ->join("protocolo", "protocolo.id_protocolo = protocolo_atendimento.id_protocolo")
            ->join("atendimento", "atendimento.id_atendimento = protocolo_atendimento.id_atendimento")
            ->join("usuario", "usuario.id_usuario = protocolo_atendimento.id_usuario")
            ->where("protocolo_atendimento.id_atendimento", $idAtendimento)
            ->orderBy("protocolo_atendimento.data_hora_abertura")
            ->findAll();
    }

    /**
     * Protocolo do paciente com o atendimento e o marco inicial.
     *
     * @param int $id
     * @return array
     */
    public function getCompleto(int $id): array
    {
        return $this->select("protocolo_atendimento.*, protocolo.nome AS protocolo, protocolo.marco_inicial, atendimento.numero, atendimento.status, atendimento.data_hora_chegada")
            ->join("protocolo", "protocolo.id_protocolo = protocolo_atendimento.id_protocolo")
            ->join("atendimento", "atendimento.id_atendimento = protocolo_atendimento.id_atendimento")
            ->where("protocolo_atendimento.id_protocolo_atendimento", $id)
            ->first() ?? [];
    }

    /**
     * Encerra o protocolo (concluído ou descartado), somente se estiver aberto.
     *
     * @param int $id
     * @param string $situacao
     * @param string|null $observacao
     * @return bool
     */
    public function encerra(int $id, string $situacao, $observacao): bool
    {
        return $this->where("id_protocolo_atendimento", $id)
            ->where("situacao", "aberto")
            ->set([
                "situacao"               => $situacao,
                "observacao"             => $observacao,
                "data_hora_encerramento" => date("Y-m-d H:i:s"),
            ])
            ->update() && $this->db->affectedRows() == 1;
    }
}
