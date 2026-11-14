<?php

namespace App\Models;

class ProtocoloEtapaModel extends BaseModel
{
    protected $table = "protocolo_etapa";
    protected $primaryKey = "id_etapa";
    protected $allowedFields = [
        "id_protocolo",
        "descricao",
        "meta_minutos",
        "ordem"
    ];

    protected $validationRules = [
        "meta_minutos" => [
            "label" => "Meta",
            "rules" => "required|is_natural_no_zero|less_than_equal_to[1440]"
        ]
    ];

    /**
     * Etapas de todos os protocolos, com o nome e a fonte do protocolo.
     *
     * @return array
     */
    public function listaComProtocolo(): array
    {
        return $this->select("protocolo_etapa.*, protocolo.nome AS protocolo, protocolo.marco_inicial, protocolo.fonte")
            ->join("protocolo", "protocolo.id_protocolo = protocolo_etapa.id_protocolo")
            ->orderBy("protocolo.nome")
            ->orderBy("protocolo_etapa.ordem")
            ->findAll();
    }

    /**
     * Etapa com o nome e o marco inicial do protocolo.
     *
     * @param int $id
     * @return array
     */
    public function getComProtocolo(int $id): array
    {
        return $this->select("protocolo_etapa.*, protocolo.nome AS protocolo, protocolo.marco_inicial, protocolo.fonte")
            ->join("protocolo", "protocolo.id_protocolo = protocolo_etapa.id_protocolo")
            ->where("protocolo_etapa.id_etapa", $id)
            ->first() ?? [];
    }

    /**
     * Indicadores de cada etapa nos protocolos abertos no período.
     *
     * @param array $filtros
     * @return array
     */
    public function indicadores(array $filtros): array
    {
        $marco = "IF(protocolo.marco_inicial = 'chegada', atendimento.data_hora_chegada, protocolo_atendimento.data_hora_abertura)";
        $abertos = "protocolo_atendimento.id_protocolo = protocolo_etapa.id_protocolo AND protocolo_atendimento.situacao <> 'descartado'";

        if (!empty($filtros["data_inicial"])) {
            $abertos .= " AND protocolo_atendimento.data_hora_abertura >= " . $this->db->escape($filtros["data_inicial"] . " 00:00:00");
        }

        if (!empty($filtros["data_final"])) {
            $abertos .= " AND protocolo_atendimento.data_hora_abertura <= " . $this->db->escape($filtros["data_final"] . " 23:59:59");
        }

        return $this->select("protocolo_etapa.id_etapa, protocolo_etapa.descricao, protocolo_etapa.meta_minutos, protocolo.nome AS protocolo")
            ->select("COUNT(DISTINCT protocolo_atendimento.id_protocolo_atendimento) AS protocolos, COUNT(protocolo_registro.id_registro) AS registradas", false)
            ->select("SUM(TIMESTAMPDIFF(MINUTE, " . $marco . ", protocolo_registro.data_hora) <= protocolo_etapa.meta_minutos) AS dentro_meta", false)
            ->select("AVG(TIMESTAMPDIFF(MINUTE, " . $marco . ", protocolo_registro.data_hora)) AS media_minutos", false)
            ->join("protocolo", "protocolo.id_protocolo = protocolo_etapa.id_protocolo")
            ->join("protocolo_atendimento", $abertos, "inner", false)
            ->join("atendimento", "atendimento.id_atendimento = protocolo_atendimento.id_atendimento")
            ->join("protocolo_registro", "protocolo_registro.id_protocolo_atendimento = protocolo_atendimento.id_protocolo_atendimento AND protocolo_registro.id_etapa = protocolo_etapa.id_etapa", "left")
            ->groupBy(["protocolo_etapa.id_etapa", "protocolo_etapa.descricao", "protocolo_etapa.meta_minutos", "protocolo.nome", "protocolo_etapa.ordem"])
            ->orderBy("protocolo.nome")
            ->orderBy("protocolo_etapa.ordem")
            ->findAll();
    }
}
