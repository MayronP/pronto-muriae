<?php

/**
 * subTitulo
 *
 * @param string $acao
 * @return string
 */
function subTitulo($acao) : string
{
    if ($acao == "insert") {
        return " - Inclusão";
    } elseif ($acao == "update") {
        return " - Alteração";
    } elseif ($acao == "view") {
        return " - Visualização";
    }

    return "";
}

/**
 * setValue
 *
 * @param string $campo
 * @param array $dados
 * @param string $defaultValue
 * @return string
 */
function setValue($campo, $dados = [], $defaultValue = "")
{
    if (!empty(set_value($campo))) {
        return set_value($campo);
    } else {
        if (isset($dados[$campo])) {
            return esc($dados[$campo]);
        } else {
            return esc($defaultValue);
        }
    }
}

/**
 * setMsgErrorCampo
 *
 * @param string $chave
 * @param array $errors
 * @return string
 */
function setMsgErrorCampo($chave, $errors)
{
    $texto = '';

    if (!empty($errors[$chave])) {
        $texto = '<div class="text-danger mt-2">' . esc($errors[$chave]) . "</div>";
    }

    return $texto;
}

/**
 * mensagem
 *
 * @return string
 */
function mensagem()
{
    $texto = '';

    if (session()->msgError != "") {

        $texto .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>' . esc(session()->get('msgError')) . '</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>';
    }

    if (session()->msgSucesso != "") {

        $texto .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>' . esc(session()->get('msgSucesso')) . '</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>';
    }

    return $texto;
}

/**
 * getDescricaoStatus - Descrição dos status gravados no banco
 *
 * @param string|null $status
 * @return string
 */
function getDescricaoStatus($status) : string
{
    $descricoes = [
        "aguardando_triagem" => "Aguardando triagem",
        "aguardando_medico"  => "Aguardando médico",
        "em_atendimento"     => "Em atendimento",
        "em_observacao"      => "Em observação",
        "encerrado"          => "Encerrado",
        "solicitado"         => "Solicitado",
        "concluido"          => "Concluído",
        "prescrito"          => "Prescrito",
        "administrado"       => "Administrado",
        "suspenso"           => "Suspenso",
        "alta"               => "Alta",
        "alta_pedido"        => "Alta a pedido",
        "observacao"         => "Observação",
        "transferencia"      => "Transferência",
        "internacao"         => "Internação",
        "obito"              => "Óbito",
        "evasao"             => "Saiu sem atendimento",
        "pendente"           => "Pendente",
        "nao_administrado"   => "Não administrado",
        "aberto"             => "Aberto",
        "descartado"         => "Descartado",
        "notificada"         => "Notificada",
    ];

    return $descricoes[$status] ?? "...";
}

/**
 * getClasseDesfecho - Classe do status-badge do desfecho
 *
 * @param string|null $desfecho
 * @return string
 */
function getClasseDesfecho($desfecho) : string
{
    if ($desfecho == "evasao") {
        return "status-suspenso";
    }

    return ($desfecho == "obito") ? "status-obito" : "status-concluido";
}

/**
 * getDesfechos - Desfechos do encerramento médico, com ícone e descrição
 *
 * @return array
 */
function getDesfechos() : array
{
    return [
        "alta"          => ["icone" => "fa-home", "descricao" => "Paciente liberado após a conclusão do atendimento."],
        "alta_pedido"   => ["icone" => "fa-file-text-o", "descricao" => "Com termo de responsabilidade assinado."],
        "transferencia" => ["icone" => "fa-ambulance", "descricao" => "Encaminhamento para outro serviço de saúde."],
        "internacao"    => ["icone" => "fa-bed", "descricao" => "Encaminhado para internação hospitalar."],
        "obito"         => ["icone" => "fa-minus-circle", "descricao" => "Com data, hora e declaração de óbito."],
    ];
}

/**
 * getFrequencias - Frequências da prescrição (chave = intervalo em horas)
 *
 * @return array
 */
function getFrequencias() : array
{
    return [
        "unica" => "Dose única",
        "4"     => "4/4 h",
        "6"     => "6/6 h",
        "8"     => "8/8 h",
        "12"    => "12/12 h",
        "24"    => "24/24 h",
        "sn"    => "Se necessário",
    ];
}

/**
 * getMotivosNaoAdministracao - Motivos para a dose não administrada
 *
 * @return array
 */
function getMotivosNaoAdministracao() : array
{
    return [
        "Paciente recusou",
        "Paciente em jejum",
        "Paciente ausente do leito",
        "Medicamento indisponível",
        "Acesso venoso indisponível",
        "Orientação médica",
    ];
}

/**
 * getTiposDocumento - Documentos emitidos no atendimento
 *
 * @return array
 */
function getTiposDocumento() : array
{
    return [
        "receita"        => "Receita",
        "atestado"       => "Atestado médico",
        "comparecimento" => "Declaração de comparecimento",
        "encaminhamento" => "Encaminhamento",
    ];
}

/**
 * pendenciasQuadro - Pendências do paciente mostradas no quadro
 *
 * @param array $item
 * @return string
 */
function pendenciasQuadro(array $item) : string
{
    $texto = '';

    if (!empty($item['doses_pendentes'])) {
        $texto .= '<span class="texto-secundario texto-alerta"><i class="fa fa-medkit me-1" aria-hidden="true"></i>' . esc($item['doses_pendentes']) . ' dose(s) a administrar</span>';
    }

    if (!empty($item['exames_pendentes'])) {
        $texto .= '<span class="texto-secundario"><i class="fa fa-flask me-1" aria-hidden="true"></i>' . esc($item['exames_pendentes']) . ' exame(s) sem resultado</span>';
    }

    return $texto;
}

/**
 * marcoProtocolo - De onde contam as metas do protocolo (chegada ou abertura)
 *
 * @param array $protocolo
 * @return string
 */
function marcoProtocolo(array $protocolo) : string
{
    return ($protocolo["marco_inicial"] == "chegada") ? $protocolo["data_hora_chegada"] : $protocolo["data_hora_abertura"];
}

/**
 * badgeDose - Situação da dose aprazada, com aviso de atraso
 *
 * @param array $dose
 * @return string
 */
function badgeDose(array $dose) : string
{
    if ($dose["situacao"] == "pendente" && !empty($dose["data_hora_prevista"]) && strtotime($dose["data_hora_prevista"]) <= time() - 60) {
        return '<span class="status-badge status-suspenso">Atrasada ' . esc(tempoDecorrido($dose["data_hora_prevista"])) . '</span>';
    }

    return '<span class="status-badge ' . esc(getClasseStatus($dose["situacao"])) . '">' . esc(getDescricaoStatus($dose["situacao"])) . '</span>';
}

/**
 * getDescricaoConsciencia - Nível de consciência da escala ACVPU
 *
 * @param string|null $nivel
 * @return string
 */
function getDescricaoConsciencia($nivel) : string
{
    $descricoes = [
        "A" => "Alerta",
        "C" => "Confusão nova",
        "V" => "Responde à voz",
        "P" => "Responde à dor",
        "U" => "Não responde",
    ];

    return $descricoes[$nivel] ?? "—";
}

/**
 * nomePaciente - Nome social, quando houver, senão o nome civil
 *
 * @param array $paciente
 * @return string
 */
function nomePaciente(array $paciente) : string
{
    return !empty($paciente["nome_social"]) ? $paciente["nome_social"] : (string) $paciente["nome_completo"];
}

/**
 * alertasPaciente - Avisos do paciente nos resumos das telas clínicas
 *
 * @param array $paciente
 * @return string
 */
function alertasPaciente(array $paciente) : string
{
    $texto = '';

    if (!empty($paciente["alergias"])) {
        $texto .= '<p class="alerta-paciente alerta-alergia"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>'
            . '<span><strong>Alergias:</strong> ' . esc($paciente["alergias"]) . '</span></p>';
    }

    if (!empty($paciente["nao_identificado"])) {
        $texto .= '<p class="alerta-paciente alerta-nao-identificado"><i class="fa fa-user-secret" aria-hidden="true"></i>'
            . '<span><strong>Paciente não identificado.</strong> ' . esc($paciente["caracteristicas"] ?? "") . '</span></p>';
    }

    if (!empty($paciente["nome_social"])) {
        $texto .= '<p class="alerta-paciente"><i class="fa fa-id-card-o" aria-hidden="true"></i>'
            . '<span>Nome social em uso. Nome civil: ' . esc($paciente["nome_completo"]) . '</span></p>';
    }

    return $texto;
}

/**
 * badgeNews2 - Etiqueta do escore NEWS2 (vazia quando não se aplica)
 *
 * @param array $news2
 * @return string
 */
function badgeNews2(array $news2) : string
{
    if ($news2["escore"] === null) {
        return '';
    }

    return '<span class="badge-news2 ' . esc($news2["classe"]) . '" title="NEWS2 pela escala 1 de saturação; não contempla escala 2. Exige avaliação profissional. Escore ' . esc($news2["escore"]) . ' - risco ' . esc(mb_strtolower($news2["risco"])) . '">
                NEWS2 ' . esc($news2["escore"]) . ' · ' . esc($news2["risco"]) . '</span>';
}

/**
 * badgeMeta - Espera pelo médico comparada ao tempo-alvo da cor
 *
 * @param string $referencia
 * @param int $tempoAlvo
 * @return string
 */
function badgeMeta($referencia, $tempoAlvo) : string
{
    $meta = situacaoMeta($referencia, $tempoAlvo);

    if ($meta["atrasado"]) {
        return '<span class="texto-alerta"><i class="fa fa-clock-o me-1" aria-hidden="true"></i>' . esc(formataMinutos(-$meta["restante"])) . ' acima da meta</span>';
    }

    if ((int) $tempoAlvo == 0) {
        return '<span class="texto-alerta">atendimento imediato</span>';
    }

    return '<span class="text-muted">no prazo · faltam ' . esc(formataMinutos($meta["restante"])) . '</span>';
}

/**
 * badgePermanencia - Tempo no pronto atendimento, com aviso perto do limite
 *
 * @param string $chegada
 * @return string
 */
function badgePermanencia($chegada) : string
{
    $permanencia = situacaoPermanencia($chegada);

    if ($permanencia["nivel"] == "excedido") {
        return '<span class="status-badge status-suspenso">' . esc(formataMinutos($permanencia["minutos"])) . ' · acima de ' . PERMANENCIA_MAXIMA_HORAS . ' h</span>';
    }

    if ($permanencia["nivel"] == "proximo") {
        return '<span class="status-badge status-andamento">' . esc(formataMinutos($permanencia["minutos"])) . ' · faltam ' . esc(formataMinutos($permanencia["restante"])) . '</span>';
    }

    return '<span class="status-badge status-indefinido">' . esc(formataMinutos($permanencia["minutos"])) . '</span>';
}

/**
 * getLocaisAtendimento - Locais usados nas chamadas do painel
 *
 * @return array
 */
function getLocaisAtendimento() : array
{
    return [
        "Sala de classificação de risco",
        "Consultório 1",
        "Consultório 2",
        "Consultório 3",
        "Sala de medicação",
        "Sala de procedimentos",
        "Sala de reanimação",
    ];
}

/**
 * getLocalAtendimento - Local escolhido para as chamadas (ou o padrão do perfil)
 *
 * @return string
 */
function getLocalAtendimento() : string
{
    if (session()->has("localAtendimento")) {
        return session()->get("localAtendimento");
    }

    return (session()->get("usuarioPerfilId") == PERFIL_ENFERMAGEM) ? "Sala de classificação de risco" : "Consultório 1";
}

/**
 * getNomePainel - Primeiro nome e inicial do sobrenome para o painel público
 *
 * @param array $chamada
 * @return string
 */
function getNomePainel(array $chamada) : string
{
    if (!empty($chamada["nao_identificado"])) {
        return "Atendimento " . $chamada["numero"];
    }

    $partes = explode(" ", trim(nomePaciente($chamada)));
    $nome   = mb_strtoupper($partes[0]);

    if (count($partes) > 1) {
        $nome .= " " . mb_strtoupper(mb_substr(end($partes), 0, 1)) . ".";
    }

    return $nome;
}

/**
 * getClasseStatus - Classe do status-badge conforme o status
 *
 * @param string|null $status
 * @return string
 */
function getClasseStatus($status) : string
{
    $classes = [
        "aguardando_triagem" => "status-aguardando",
        "aguardando_medico"  => "status-aguardando",
        "em_atendimento"     => "status-andamento",
        "em_observacao"      => "status-observacao",
        "encerrado"          => "status-concluido",
        "solicitado"         => "status-andamento",
        "concluido"          => "status-concluido",
        "prescrito"          => "status-andamento",
        "administrado"       => "status-concluido",
        "suspenso"           => "status-suspenso",
        "pendente"           => "status-aguardando",
        "nao_administrado"   => "status-suspenso",
        "aberto"             => "status-andamento",
        "notificada"         => "status-concluido",
    ];

    return $classes[$status] ?? "status-indefinido";
}

/**
 * getClasseRisco - Classe CSS da cor da classificação de risco
 *
 * @param string|null $cor
 * @return string
 */
function getClasseRisco($cor) : string
{
    if (empty($cor)) {
        return "risco-indefinido";
    }

    return "risco-" . $cor;
}

/**
 * getSituacaoUsuario
 *
 * @param int $ativo
 * @return string
 */
function getSituacaoUsuario($ativo) : string
{
    if ($ativo == 1) {
        return "Ativo";
    } elseif ($ativo == 0) {
        return "Inativo";
    } else {
        return "...";
    }
}
