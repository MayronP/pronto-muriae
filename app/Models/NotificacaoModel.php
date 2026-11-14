<?php

namespace App\Models;

class NotificacaoModel extends BaseModel
{
    protected $table = "notificacao";
    protected $primaryKey = "id_notificacao";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "agravo",
        "tipo",
        "data_hora",
        "situacao",
        "id_usuario_notificacao",
        "data_hora_notificacao",
        "numero_sinan"
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
        "agravo" => [
            "label" => "Doença ou agravo",
            "rules" => "required|min_length[3]|max_length[150]"
        ],
        "tipo" => [
            "label" => "Prazo",
            "rules" => "required|in_list[imediata,semanal]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "situacao" => [
            "label" => "Situação",
            "rules" => "required|in_list[pendente,notificada]"
        ]
    ];

    /**
     * Notificações do atendimento, com o prazo de envio.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("notificacao.*, usuario.nome AS usuario, notificador.nome AS notificador")
            ->select("IF(notificacao.tipo = 'imediata', notificacao.data_hora + INTERVAL 24 HOUR, notificacao.data_hora + INTERVAL 7 DAY) AS prazo", false)
            ->join("usuario", "usuario.id_usuario = notificacao.id_usuario")
            ->join("usuario AS notificador", "notificador.id_usuario = notificacao.id_usuario_notificacao", "left")
            ->where("notificacao.id_atendimento", $idAtendimento)
            ->orderBy("notificacao.data_hora", "DESC")
            ->findAll();
    }

    /**
     * Notificações de todos os atendimentos, filtradas pela situação.
     *
     * @param string $situacao
     * @return array
     */
    public function listaPorSituacao(string $situacao = "pendente"): array
    {
        $this->select("notificacao.*, usuario.nome AS usuario, notificador.nome AS notificador")
            ->select("atendimento.numero, atendimento.status, paciente.nome_completo, paciente.nome_social")
            ->select("IF(notificacao.tipo = 'imediata', notificacao.data_hora + INTERVAL 24 HOUR, notificacao.data_hora + INTERVAL 7 DAY) AS prazo", false)
            ->join("usuario", "usuario.id_usuario = notificacao.id_usuario")
            ->join("usuario AS notificador", "notificador.id_usuario = notificacao.id_usuario_notificacao", "left")
            ->join("atendimento", "atendimento.id_atendimento = notificacao.id_atendimento")
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente");

        if (in_array($situacao, ["pendente", "notificada"])) {
            $this->where("notificacao.situacao", $situacao);
        }

        return $this->orderBy("notificacao.situacao = 'pendente'", "DESC", false)
            ->orderBy("notificacao.tipo = 'imediata'", "DESC", false)
            ->orderBy("notificacao.data_hora", $situacao == "notificada" ? "DESC" : "ASC")
            ->findAll(200);
    }

    /**
     * Registra o envio ao Sinan, somente se a notificação estiver pendente.
     *
     * @param int $id
     * @param int $idUsuario
     * @param string $numeroSinan
     * @return bool
     */
    public function notifica(int $id, int $idUsuario, string $numeroSinan): bool
    {
        return $this->where("id_notificacao", $id)
            ->where("situacao", "pendente")
            ->set([
                "situacao"               => "notificada",
                "id_usuario_notificacao" => $idUsuario,
                "data_hora_notificacao"  => date("Y-m-d H:i:s"),
                "numero_sinan"           => $numeroSinan !== "" ? $numeroSinan : null,
            ])
            ->update() && $this->db->affectedRows() == 1;
    }
}
