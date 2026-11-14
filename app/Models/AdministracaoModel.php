<?php

namespace App\Models;

class AdministracaoModel extends BaseModel
{
    protected $table = "administracao";
    protected $primaryKey = "id_administracao";
    protected $allowedFields = [
        "id_item_prescricao",
        "data_hora_prevista",
        "situacao",
        "id_usuario",
        "data_hora",
        "motivo"
    ];

    protected $validationRules = [
        "id_item_prescricao" => [
            "label" => "Medicamento",
            "rules" => "required|is_natural_no_zero"
        ],
        "situacao" => [
            "label" => "Situação",
            "rules" => "required|in_list[pendente,administrado,nao_administrado,suspenso]"
        ],
        "motivo" => [
            "label" => "Motivo",
            "rules" => "permit_empty|max_length[255]"
        ]
    ];

    /**
     * Programa as doses do item (aprazamento) dentro do limite de permanência.
     *
     * @param int $idItem
     * @param string $inicio
     * @param int|null $intervaloHoras
     * @param bool $seNecessario
     * @return int
     */
    public function aprazar(int $idItem, string $inicio, $intervaloHoras, bool $seNecessario): int
    {
        if ($seNecessario) {
            return 0;
        }

        $primeira = strtotime($inicio);
        $horarios = [$primeira];

        if (!empty($intervaloHoras)) {
            $limite = $primeira + PERMANENCIA_MAXIMA_HORAS * 3600;

            for ($horario = $primeira + $intervaloHoras * 3600; $horario < $limite; $horario += $intervaloHoras * 3600) {
                $horarios[] = $horario;
            }
        }

        $doses = [];

        foreach ($horarios as $horario) {
            $doses[] = [
                "id_item_prescricao" => $idItem,
                "data_hora_prevista" => date("Y-m-d H:i:s", $horario),
                "situacao"           => "pendente",
            ];
        }

        $this->insertBatch($doses);

        return count($doses);
    }

    /**
     * Doses do atendimento por horário previsto, com quem checou.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("administracao.*, usuario.nome AS checado_por")
            ->join("prescricao_medicamento", "prescricao_medicamento.id_item_prescricao = administracao.id_item_prescricao")
            ->join("prescricao", "prescricao.id_prescricao = prescricao_medicamento.id_prescricao")
            ->join("usuario", "usuario.id_usuario = administracao.id_usuario", "left")
            ->where("prescricao.id_atendimento", $idAtendimento)
            ->orderBy("administracao.data_hora_prevista IS NULL", "", false)
            ->orderBy("administracao.data_hora_prevista")
            ->orderBy("administracao.data_hora")
            ->orderBy("administracao.id_administracao")
            ->findAll();
    }

    /**
     * Dose com o item, o medicamento e o atendimento a que pertence.
     *
     * @param int $id
     * @return array
     */
    public function getComAtendimento(int $id): array
    {
        return $this->select("administracao.*, prescricao_medicamento.situacao AS situacao_item, prescricao.id_atendimento, medicamento.nome AS medicamento")
            ->join("prescricao_medicamento", "prescricao_medicamento.id_item_prescricao = administracao.id_item_prescricao")
            ->join("prescricao", "prescricao.id_prescricao = prescricao_medicamento.id_prescricao")
            ->join("medicamento", "medicamento.id_medicamento = prescricao_medicamento.id_medicamento")
            ->where("administracao.id_administracao", $id)
            ->first() ?? [];
    }

    /**
     * Registra a checagem da dose, somente se ainda estiver pendente.
     *
     * @param int $id
     * @param string $situacao
     * @param int $idUsuario
     * @param string|null $motivo
     * @return bool
     */
    public function checar(int $id, string $situacao, int $idUsuario, $motivo = null): bool
    {
        return $this->where("id_administracao", $id)
            ->where("situacao", "pendente")
            ->set([
                "situacao"   => $situacao,
                "id_usuario" => $idUsuario,
                "data_hora"  => date("Y-m-d H:i:s"),
                "motivo"     => $motivo,
            ])
            ->update() && $this->db->affectedRows() == 1;
    }

    /**
     * Checa como administrada a primeira dose pendente do item.
     *
     * @param int $idItem
     * @param int $idUsuario
     * @return bool
     */
    public function checaPrimeira(int $idItem, int $idUsuario): bool
    {
        $dose = $this->where("id_item_prescricao", $idItem)
            ->where("situacao", "pendente")
            ->orderBy("data_hora_prevista")
            ->first();

        return !empty($dose) && $this->checar($dose["id_administracao"], "administrado", $idUsuario);
    }

    /**
     * Registra uma administração feita agora.
     *
     * @param int $idItem
     * @param int $idUsuario
     * @return int
     */
    public function registraAgora(int $idItem, int $idUsuario): int
    {
        $agora = date("Y-m-d H:i:s");

        return $this->insert([
            "id_item_prescricao" => $idItem,
            "data_hora_prevista" => $agora,
            "situacao"           => "administrado",
            "id_usuario"         => $idUsuario,
            "data_hora"          => $agora,
        ]);
    }

    /**
     * Suspende as doses pendentes do item (prescrição suspensa pelo médico).
     *
     * @param int $idItem
     * @return bool
     */
    public function suspendePendentes(int $idItem): bool
    {
        return $this->where("id_item_prescricao", $idItem)
            ->where("situacao", "pendente")
            ->set(["situacao" => "suspenso"])
            ->update();
    }
}
