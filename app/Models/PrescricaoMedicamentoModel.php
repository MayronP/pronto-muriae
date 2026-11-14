<?php

namespace App\Models;

class PrescricaoMedicamentoModel extends BaseModel
{
    protected $table = "prescricao_medicamento";
    protected $primaryKey = "id_item_prescricao";
    protected $allowedFields = [
        "id_prescricao",
        "id_medicamento",
        "dose",
        "via",
        "frequencia",
        "intervalo_horas",
        "se_necessario",
        "data_hora_inicio",
        "observacoes",
        "situacao"
    ];

    protected $validationRules = [
        "id_prescricao" => [
            "label" => "Prescrição",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_medicamento" => [
            "label" => "Medicamento",
            "rules" => "required|is_natural_no_zero"
        ],
        "dose" => [
            "label" => "Dose",
            "rules" => "required|max_length[80]"
        ],
        "via" => [
            "label" => "Via",
            "rules" => "required|in_list[Oral,Intravenosa,Intramuscular,Subcutânea]"
        ],
        "frequencia" => [
            "label" => "Frequência",
            "rules" => "required|max_length[100]"
        ],
        "intervalo_horas" => [
            "label" => "Intervalo",
            "rules" => "permit_empty|in_list[4,6,8,12,24]"
        ],
        "se_necessario" => [
            "label" => "Se necessário",
            "rules" => "required|in_list[0,1]"
        ],
        "data_hora_inicio" => [
            "label" => "Primeira dose",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "situacao" => [
            "label" => "Situação",
            "rules" => "required|in_list[prescrito,suspenso]"
        ]
    ];

    /**
     * Itens prescritos no atendimento com o medicamento e o prescritor.
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("prescricao_medicamento.*, medicamento.nome AS medicamento, medicamento.apresentacao")
            ->select("prescricao.data_hora AS data_hora_prescricao, prescritor.nome AS prescritor")
            ->join("prescricao", "prescricao.id_prescricao = prescricao_medicamento.id_prescricao")
            ->join("medicamento", "medicamento.id_medicamento = prescricao_medicamento.id_medicamento")
            ->join("usuario AS prescritor", "prescritor.id_usuario = prescricao.id_medico")
            ->where("prescricao.id_atendimento", $idAtendimento)
            ->orderBy("prescricao_medicamento.situacao = 'suspenso'", "", false)
            ->orderBy("prescricao_medicamento.id_item_prescricao")
            ->findAll();
    }

    /**
     * Medicamento ativo no atendimento com o mesmo nome (alerta de duplicidade).
     *
     * @param int $idAtendimento
     * @param string $medicamento
     * @return string
     */
    public function duplicidade(int $idAtendimento, string $medicamento): string
    {
        $ativos = $this->select("medicamento.nome")
            ->join("prescricao", "prescricao.id_prescricao = prescricao_medicamento.id_prescricao")
            ->join("medicamento", "medicamento.id_medicamento = prescricao_medicamento.id_medicamento")
            ->where("prescricao.id_atendimento", $idAtendimento)
            ->where("prescricao_medicamento.situacao", "prescrito")
            ->findAll();

        foreach ($ativos as $ativo) {
            if (normalizaTexto($ativo["nome"]) == normalizaTexto($medicamento)) {
                return $ativo["nome"];
            }
        }

        return "";
    }

    /**
     * Retorna o item com o id do atendimento ao qual pertence.
     *
     * @param int $id
     * @return array
     */
    public function getComAtendimento(int $id): array
    {
        return $this->select("prescricao_medicamento.*, prescricao.id_atendimento, medicamento.nome AS medicamento")
            ->join("prescricao", "prescricao.id_prescricao = prescricao_medicamento.id_prescricao")
            ->join("medicamento", "medicamento.id_medicamento = prescricao_medicamento.id_medicamento")
            ->where("prescricao_medicamento.id_item_prescricao", $id)
            ->first() ?? [];
    }
}
