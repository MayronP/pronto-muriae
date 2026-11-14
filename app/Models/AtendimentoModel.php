<?php

namespace App\Models;

class AtendimentoModel extends BaseModel
{
    protected $table = "atendimento";
    protected $primaryKey = "id_atendimento";
    protected $allowedFields = [
        "id_paciente",
        "id_usuario_admissao",
        "id_medico",
        "numero",
        "data_hora_chegada",
        "data_hora_inicio_atendimento",
        "id_classificacao_inicio",
        "tempo_alvo_inicio",
        "minutos_espera",
        "status",
        "observacao_inicial",
        "data_hora_inicio_observacao",
        "leito_observacao",
        "desfecho",
        "cid_principal",
        "data_hora_obito",
        "declaracao_obito",
        "termo_alta_pedido",
        "resumo_final",
        "observacoes_finais",
        "data_hora_encerramento"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Id",
            "rules" => "permit_empty|is_natural_no_zero"
        ],
        "id_paciente" => [
            "label" => "Paciente",
            "rules" => "required|is_not_unique[paciente.id_paciente]"
        ],
        "id_usuario_admissao" => [
            "label" => "Responsável pela admissão",
            "rules" => "required|is_natural_no_zero"
        ],
        "numero" => [
            "label" => "Número do atendimento",
            "rules" => "required|max_length[30]|is_unique[atendimento.numero,id_atendimento,{id_atendimento}]"
        ],
        "data_hora_chegada" => [
            "label" => "Data e horário de chegada",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ],
        "status" => [
            "label" => "Status",
            "rules" => "required|in_list[aguardando_triagem,aguardando_medico,em_atendimento,em_observacao,encerrado]"
        ],
        "leito_observacao" => [
            "label" => "Leito de observação",
            "rules" => "permit_empty|max_length[40]"
        ],
        "desfecho" => [
            "label" => "Desfecho",
            "rules" => "permit_empty|in_list[alta,alta_pedido,transferencia,internacao,obito,evasao]"
        ],
        "cid_principal" => [
            "label" => "CID-10",
            "rules" => "permit_empty|is_not_unique[cid10.codigo]"
        ]
    ];

    /**
     * Gera o próximo número de atendimento do ano (ex.: PA-2026-0001).
     *
     * @return string
     */
    public function gerarNumero(): string
    {
        $prefixo = "PA-" . date("Y") . "-";

        $ultimo = $this->like("numero", $prefixo, "after")
            ->orderBy("id_atendimento", "DESC")
            ->first();

        $sequencia = empty($ultimo) ? 1 : (int) substr($ultimo["numero"], strlen($prefixo)) + 1;

        return $prefixo . str_pad($sequencia, 4, "0", STR_PAD_LEFT);
    }

    /**
     * Retorna o atendimento em aberto (não encerrado) do paciente, se existir.
     *
     * @param int $idPaciente
     * @return array
     */
    public function getAberto(int $idPaciente): array
    {
        return $this->where("id_paciente", $idPaciente)
            ->where("status <>", "encerrado")
            ->first() ?? [];
    }

    /**
     * Retorna o atendimento com paciente, triagem, classificação e profissionais.
     *
     * @param int $id
     * @return array
     */
    public function getCompleto(int $id): array
    {
        return $this->select("atendimento.*, paciente.nome_completo, paciente.nome_social, paciente.cpf, paciente.cns, paciente.data_nascimento")
            ->select("paciente.nome_mae, paciente.sexo, paciente.telefone, paciente.alergias, paciente.nao_identificado, paciente.caracteristicas")
            ->select("triagem.id_triagem, triagem.id_classificacao, triagem.queixa_principal, triagem.observacoes_iniciais, triagem.pressao_arterial, triagem.temperatura")
            ->select("triagem.frequencia_cardiaca, triagem.frequencia_respiratoria, triagem.saturacao, triagem.oxigenio_suplementar, triagem.nivel_consciencia")
            ->select("triagem.gestante, triagem.dor, triagem.glicemia, triagem.peso, triagem.data_hora AS data_hora_triagem, triagem.data_hora_reclassificacao")
            ->select("classificacao_risco.nome AS classificacao, classificacao_risco.cor, classificacao_risco.tempo_alvo_minutos")
            ->select("classificacao_inicio.nome AS classificacao_inicio, atendimento.tempo_alvo_inicio")
            ->select("enfermeiro.nome AS enfermeiro, medico.nome AS medico, medico.registro_profissional AS registro_medico, admissao.nome AS usuario_admissao")
            ->select("cid10.descricao AS cid_descricao")
            ->select("(SELECT CONCAT(acompanhante.nome, IF(acompanhante.parentesco IS NULL OR acompanhante.parentesco = '', '', CONCAT(' (', acompanhante.parentesco, ')'))) FROM acompanhante WHERE acompanhante.id_atendimento = atendimento.id_atendimento AND acompanhante.data_hora_saida IS NULL ORDER BY acompanhante.data_hora_entrada DESC LIMIT 1) AS acompanhante_atual", false)
            ->select("(SELECT GROUP_CONCAT(protocolo.nome ORDER BY protocolo.nome SEPARATOR ', ') FROM protocolo_atendimento JOIN protocolo ON protocolo.id_protocolo = protocolo_atendimento.id_protocolo WHERE protocolo_atendimento.id_atendimento = atendimento.id_atendimento AND protocolo_atendimento.situacao = 'aberto') AS protocolos_abertos", false)
            ->select("(SELECT GROUP_CONCAT(notificacao.agravo ORDER BY notificacao.data_hora SEPARATOR ', ') FROM notificacao WHERE notificacao.id_atendimento = atendimento.id_atendimento AND notificacao.situacao = 'pendente') AS notificacoes_pendentes", false)
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao", "left")
            ->join("classificacao_risco AS classificacao_inicio", "classificacao_inicio.id_classificacao = atendimento.id_classificacao_inicio", "left")
            ->join("usuario AS enfermeiro", "enfermeiro.id_usuario = triagem.id_enfermeiro", "left")
            ->join("usuario AS medico", "medico.id_usuario = atendimento.id_medico", "left")
            ->join("usuario AS admissao", "admissao.id_usuario = atendimento.id_usuario_admissao", "left")
            ->join("cid10", "cid10.codigo = atendimento.cid_principal", "left")
            ->where("atendimento.id_atendimento", $id)
            ->first() ?? [];
    }

    /**
     * Atendimentos que aguardam triagem, por ordem de chegada.
     *
     * @return array
     */
    public function aguardandoTriagem(): array
    {
        return $this->select("atendimento.*, paciente.nome_completo, paciente.nome_social, paciente.cpf, paciente.data_nascimento")
            ->select("paciente.sexo, paciente.nao_identificado, paciente.alergias")
            ->select("(SELECT COUNT(*) FROM chamada WHERE chamada.id_atendimento = atendimento.id_atendimento) AS total_chamadas", false)
            ->select("(SELECT MAX(chamada.data_hora) FROM chamada WHERE chamada.id_atendimento = atendimento.id_atendimento) AS ultima_chamada", false)
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->where("atendimento.status", "aguardando_triagem")
            ->orderBy("atendimento.data_hora_chegada")
            ->findAll();
    }

    /**
     * Fila médica, ordenada pela prioridade e pela hora de chegada.
     *
     * @param string $status
     * @param string $busca
     * @param int $limite
     * @return array
     */
    public function fila(string $status = "", string $busca = "", int $limite = 0): array
    {
        $this->select("atendimento.*, paciente.nome_completo, paciente.nome_social, paciente.data_nascimento, paciente.nao_identificado, paciente.alergias")
            ->select("classificacao_risco.nome AS classificacao, classificacao_risco.cor, classificacao_risco.tempo_alvo_minutos")
            ->select("paciente.sexo, triagem.queixa_principal, triagem.data_hora AS data_hora_triagem, triagem.data_hora_reclassificacao, triagem.gestante")
            ->select("(SELECT COUNT(*) FROM chamada WHERE chamada.id_atendimento = atendimento.id_atendimento) AS total_chamadas", false)
            ->select("(SELECT MAX(chamada.data_hora) FROM chamada WHERE chamada.id_atendimento = atendimento.id_atendimento) AS ultima_chamada", false)
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao");

        $this->selecionaSinaisAtuais();
        $this->selecionaPendencias();

        if ($status != "") {
            $this->where("atendimento.status", $status);
        } else {
            $this->whereIn("atendimento.status", ["aguardando_medico", "em_atendimento"]);
        }

        if ($busca != "") {
            $this->groupStart()
                ->like("paciente.nome_completo", $busca)
                ->orLike("paciente.nome_social", $busca)
                ->orLike("atendimento.numero", $busca)
            ->groupEnd();
        }

        return $this->orderBy("classificacao_risco.ordem_prioridade")
            ->orderBy("atendimento.data_hora_chegada")
            ->findAll($limite);
    }

    /**
     * Pacientes em observação, por ordem de chegada.
     *
     * @return array
     */
    public function emObservacao(): array
    {
        $this->select("atendimento.*, paciente.nome_completo, paciente.nome_social, paciente.nao_identificado, paciente.alergias, paciente.data_nascimento")
            ->select("classificacao_risco.nome AS classificacao, classificacao_risco.cor, medico.nome AS medico, triagem.gestante")
            ->select("(SELECT COUNT(*) FROM acompanhante WHERE acompanhante.id_atendimento = atendimento.id_atendimento AND acompanhante.data_hora_saida IS NULL) AS acompanhantes", false)
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao", "left")
            ->join("usuario AS medico", "medico.id_usuario = atendimento.id_medico", "left");

        $this->selecionaSinaisAtuais();
        $this->selecionaPendencias();

        return $this->where("atendimento.status", "em_observacao")
            ->orderBy("atendimento.data_hora_chegada")
            ->findAll();
    }

    /**
     * Pacientes no pronto atendimento, com a última passagem do perfil informado.
     *
     * @param int $idPerfil
     * @return array
     */
    public function listaParaPlantao(int $idPerfil): array
    {
        return $this->select("atendimento.*, paciente.nome_completo, paciente.nome_social, paciente.nao_identificado, paciente.alergias")
            ->select("classificacao_risco.nome AS classificacao, classificacao_risco.cor, medico.nome AS medico")
            ->select("ultima.id_passagem, ultima.data_hora AS data_hora_passagem, ultima.id_usuario AS id_autor_passagem, ultima.data_hora_recebimento")
            ->select("autor.nome AS autor_passagem, autor.id_perfil AS id_perfil_autor_passagem, recebedor.nome AS recebedor_passagem")
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao", "left")
            ->join("usuario AS medico", "medico.id_usuario = atendimento.id_medico", "left")
            ->join("passagem_plantao AS ultima", "ultima.id_passagem = (SELECT MAX(p.id_passagem) FROM passagem_plantao AS p JOIN usuario AS u ON u.id_usuario = p.id_usuario WHERE p.id_atendimento = atendimento.id_atendimento AND u.id_perfil = " . $idPerfil . ")", "left", false)
            ->join("usuario AS autor", "autor.id_usuario = ultima.id_usuario", "left")
            ->join("usuario AS recebedor", "recebedor.id_usuario = ultima.id_usuario_recebimento", "left")
            ->whereIn("atendimento.status", ["aguardando_medico", "em_atendimento", "em_observacao"])
            ->orderBy("classificacao_risco.ordem_prioridade")
            ->orderBy("atendimento.data_hora_chegada")
            ->findAll();
    }

    /**
     * Registra o início do atendimento médico (só se ainda aguarda o médico).
     *
     * @param array $atendimento
     * @param int $idMedico
     * @return bool
     */
    public function iniciaAtendimento(array $atendimento, int $idMedico): bool
    {
        $agora      = time();
        $referencia = strtotime($atendimento["data_hora_reclassificacao"] ?: $atendimento["data_hora_triagem"]);

        return $this->where("id_atendimento", $atendimento["id_atendimento"])
            ->where("status", "aguardando_medico")
            ->set([
                "status"                       => "em_atendimento",
                "id_medico"                    => $idMedico,
                "data_hora_inicio_atendimento" => date("Y-m-d H:i:s", $agora),
                "id_classificacao_inicio"      => $atendimento["id_classificacao"],
                "tempo_alvo_inicio"            => $atendimento["tempo_alvo_minutos"],
                "minutos_espera"               => max(0, intdiv($agora - $referencia, 60)),
            ])
            ->update() && $this->db->affectedRows() == 1;
    }

    /**
     * Histórico de atendimentos do paciente (mais recentes primeiro).
     *
     * @param int $idPaciente
     * @return array
     */
    public function historicoPaciente(int $idPaciente): array
    {
        return $this->select("atendimento.*, classificacao_risco.nome AS classificacao, classificacao_risco.cor, medico.nome AS medico")
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao", "left")
            ->join("usuario AS medico", "medico.id_usuario = atendimento.id_medico", "left")
            ->where("atendimento.id_paciente", $idPaciente)
            ->orderBy("atendimento.data_hora_chegada", "DESC")
            ->findAll();
    }

    /**
     * Total de atendimentos nos status informados.
     *
     * @param array $status
     * @return int
     */
    public function totalPorStatus(array $status): int
    {
        return $this->whereIn("status", $status)->countAllResults();
    }

    /**
     * Total de atendimentos que chegaram hoje e já passaram pelo médico.
     *
     * @return int
     */
    public function totalAtendidosHoje(): int
    {
        return $this->where("data_hora_chegada >=", date("Y-m-d 00:00:00"))
            ->where("data_hora_chegada <=", date("Y-m-d 23:59:59"))
            ->where("minutos_espera IS NOT NULL")
            ->countAllResults();
    }

    /**
     * Total de atendimentos em aberto classificados como vermelho ou laranja.
     *
     * @return int
     */
    public function totalPrioritarios(): int
    {
        return $this->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao")
            ->where("atendimento.status <>", "encerrado")
            ->where("classificacao_risco.ordem_prioridade <=", 2)   // 1 = Vermelho, 2 = Laranja
            ->countAllResults();
    }

    /**
     * Relatório agrupado por dia de chegada e médico responsável.
     *
     * @param array $filtros
     * @return array
     */
    public function relatorio(array $filtros): array
    {
        $this->select("DATE(atendimento.data_hora_chegada) AS periodo, medico.nome AS profissional, COUNT(*) AS total", false)
            ->select("SUM(atendimento.status IN ('aguardando_triagem', 'aguardando_medico')) AS aguardando", false)
            ->select("SUM(atendimento.status = 'em_atendimento') AS em_atendimento", false)
            ->select("SUM(atendimento.status = 'em_observacao') AS em_observacao", false)
            ->select("SUM(atendimento.status = 'encerrado' AND atendimento.desfecho <> 'evasao') AS encerrados", false)
            ->select("SUM(atendimento.desfecho = 'evasao') AS evasoes", false)
            ->select("SUM(atendimento.desfecho = 'obito') AS obitos", false)
            ->join("usuario AS medico", "medico.id_usuario = atendimento.id_medico", "left");

        $this->aplicaFiltros($filtros);

        return $this->groupBy(["periodo", "atendimento.id_medico", "profissional"])
            ->orderBy("periodo", "DESC")
            ->orderBy("profissional")
            ->findAll();
    }

    /**
     * Totais do relatório.
     *
     * @param array $filtros
     * @return array
     */
    public function resumoRelatorio(array $filtros): array
    {
        $this->select("COUNT(*) AS atendimentos", false)
            ->select("SUM(atendimento.status = 'em_atendimento') AS em_atendimento", false)
            ->select("SUM(atendimento.status = 'em_observacao') AS em_observacao", false)
            ->select("SUM(classificacao_risco.ordem_prioridade <= 2) AS prioritarios", false)   // 1 = Vermelho, 2 = Laranja
            ->select("SUM(atendimento.status = 'encerrado' AND atendimento.desfecho <> 'evasao') AS encerrados", false)
            ->select("SUM(atendimento.desfecho = 'evasao') AS evasoes", false)
            ->select("SUM(atendimento.desfecho = 'obito') AS obitos", false)
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco", "classificacao_risco.id_classificacao = triagem.id_classificacao", "left");

        $this->aplicaFiltros($filtros);

        return $this->first() ?? [];
    }

    /**
     * Indicadores de tempo dos atendimentos que chegaram no período.
     *
     * @param array $filtros
     * @return array
     */
    public function indicadoresTempo(array $filtros): array
    {
        $this->select("COUNT(*) AS chegadas", false)
            ->select("AVG(TIMESTAMPDIFF(MINUTE, atendimento.data_hora_chegada, triagem.data_hora)) AS media_classificacao", false)
            ->select("COUNT(atendimento.minutos_espera) AS atendidos", false)
            ->select("SUM(atendimento.minutos_espera IS NOT NULL AND atendimento.tempo_alvo_inicio IS NOT NULL) AS com_meta_historica", false)
            ->select("AVG(atendimento.minutos_espera) AS media_espera", false)
            ->select("SUM(atendimento.minutos_espera <= atendimento.tempo_alvo_inicio) AS dentro_meta", false)
            ->select("AVG(IF(atendimento.status = 'encerrado' AND atendimento.desfecho <> 'evasao', TIMESTAMPDIFF(MINUTE, atendimento.data_hora_chegada, atendimento.data_hora_encerramento), NULL)) AS media_permanencia", false)
            ->select("SUM(atendimento.desfecho = 'evasao') AS evasoes", false)
            ->select("SUM(atendimento.desfecho = 'obito') AS obitos", false)
            ->join("triagem", "triagem.id_atendimento = atendimento.id_atendimento", "left")
            ->join("classificacao_risco AS classificacao_inicio", "classificacao_inicio.id_classificacao = atendimento.id_classificacao_inicio", "left");

        $this->aplicaFiltros($filtros);

        return $this->first() ?? [];
    }

    /**
     * Espera até o médico pela cor vigente no início do atendimento.
     *
     * @param array $filtros
     * @return array
     */
    public function temposPorClassificacao(array $filtros): array
    {
        $this->select("atendimento.id_classificacao_inicio AS id_classificacao, COUNT(*) AS atendidos", false)
            ->select("AVG(atendimento.minutos_espera) AS media_espera, MAX(atendimento.minutos_espera) AS maior_espera", false)
            ->select("SUM(atendimento.tempo_alvo_inicio IS NOT NULL) AS com_meta_historica", false)
            ->select("SUM(atendimento.minutos_espera <= atendimento.tempo_alvo_inicio) AS dentro_meta", false)
            ->join("classificacao_risco AS classificacao_inicio", "classificacao_inicio.id_classificacao = atendimento.id_classificacao_inicio")
            ->where("atendimento.minutos_espera IS NOT NULL");

        $this->aplicaFiltros($filtros);

        $linhas = $this->groupBy("atendimento.id_classificacao_inicio")->findAll();

        return array_column($linhas, null, "id_classificacao");
    }

    /**
     * Seleciona os sinais vitais mais recentes (ou os da triagem).
     *
     * @return void
     */
    protected function selecionaSinaisAtuais()
    {
        // Exige o join com a triagem
        foreach (["pressao_arterial", "temperatura", "frequencia_cardiaca", "frequencia_respiratoria", "saturacao", "oxigenio_suplementar", "nivel_consciencia"] as $campo) {
            $this->select("COALESCE(ultimo_sinal." . $campo . ", triagem." . $campo . ") AS " . $campo, false);
        }

        $this->join("sinal_vital AS ultimo_sinal", "ultimo_sinal.id_sinal_vital = (SELECT MAX(sv.id_sinal_vital) FROM sinal_vital AS sv WHERE sv.id_atendimento = atendimento.id_atendimento)", "left", false);
    }

    /**
     * Seleciona as pendências de exames, doses e protocolos.
     *
     * @return void
     */
    protected function selecionaPendencias()
    {
        $agora = $this->db->escape(date("Y-m-d H:i:s"));

        $this->select("(SELECT COUNT(*) FROM exame WHERE exame.id_atendimento = atendimento.id_atendimento AND exame.status = 'solicitado') AS exames_pendentes", false)
            ->select("(SELECT COUNT(*) FROM administracao JOIN prescricao_medicamento ON prescricao_medicamento.id_item_prescricao = administracao.id_item_prescricao JOIN prescricao ON prescricao.id_prescricao = prescricao_medicamento.id_prescricao WHERE prescricao.id_atendimento = atendimento.id_atendimento AND administracao.situacao = 'pendente' AND administracao.data_hora_prevista <= " . $agora . ") AS doses_pendentes", false)
            ->select("(SELECT GROUP_CONCAT(protocolo.nome ORDER BY protocolo.nome SEPARATOR ', ') FROM protocolo_atendimento JOIN protocolo ON protocolo.id_protocolo = protocolo_atendimento.id_protocolo WHERE protocolo_atendimento.id_atendimento = atendimento.id_atendimento AND protocolo_atendimento.situacao = 'aberto') AS protocolos_abertos", false);
    }

    /**
     * Aplica os filtros de período, profissional e status do relatório.
     *
     * @param array $filtros
     * @return void
     */
    protected function aplicaFiltros(array $filtros)
    {
        if (!empty($filtros["data_inicial"])) {
            $this->where("atendimento.data_hora_chegada >=", $filtros["data_inicial"] . " 00:00:00");
        }

        if (!empty($filtros["data_final"])) {
            $this->where("atendimento.data_hora_chegada <=", $filtros["data_final"] . " 23:59:59");
        }

        if (!empty($filtros["id_medico"])) {
            $this->where("atendimento.id_medico", $filtros["id_medico"]);
        }

        if ($filtros["status"] == "aguardando") {
            $this->whereIn("atendimento.status", ["aguardando_triagem", "aguardando_medico"]);
        } elseif (in_array($filtros["status"], ["em_atendimento", "em_observacao"])) {
            $this->where("atendimento.status", $filtros["status"]);
        } elseif ($filtros["status"] == "encerrado") {
            $this->where("atendimento.status", "encerrado")
                ->where("atendimento.desfecho <>", "evasao");
        } elseif ($filtros["status"] == "evasao") {
            $this->where("atendimento.desfecho", "evasao");
        }
    }
}
