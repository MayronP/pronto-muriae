<div class="banner-paciente borda-risco-<?= esc($atendimento['cor'] ?? '') ?>">
    <div class="banner-principal">
        <div class="banner-identificacao">
            <span class="avatar-paciente" aria-hidden="true"><?= esc(getIniciais(nomePaciente($atendimento))) ?></span>
            <div>
                <p class="banner-nome"><?= esc(nomePaciente($atendimento)) ?></p>
                <p class="banner-meta">
                    <?php if (!empty($atendimento['data_nascimento'])): ?>
                        <span><?= esc(calculaIdade($atendimento['data_nascimento'])) ?></span>
                    <?php endif; ?>
                    <?php if (getDescricaoSexo($atendimento['sexo'] ?? null) !== ''): ?>
                        <span><?= esc(getDescricaoSexo($atendimento['sexo'])) ?></span>
                    <?php endif; ?>
                    <span><?= esc($atendimento['numero']) ?></span>
                    <span>chegada <?= esc(formatDataHora($atendimento['data_hora_chegada'])) ?></span>
                    <?php if ($atendimento['status'] !== 'encerrado'): ?>
                        <span>no PA há <?= esc(tempoDecorrido($atendimento['data_hora_chegada'])) ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="banner-tags">
            <?php foreach (array_filter(explode(', ', (string) ($atendimento['protocolos_abertos'] ?? ''))) as $protocolo): ?>
                <span class="tag tag-protocolo">Protocolo <?= esc($protocolo) ?></span>
            <?php endforeach; ?>
            <?php if (!empty($atendimento['classificacao'])): ?>
                <span class="badge-risco <?= esc(getClasseRisco($atendimento['cor'])) ?>"><?= esc($atendimento['classificacao']) ?></span>
            <?php endif; ?>
            <span class="status-badge <?= esc(getClasseStatus($atendimento['status'])) ?>"><?= esc(getDescricaoStatus($atendimento['status'])) ?></span>
        </div>
    </div>
    <?php
        $alertas      = alertasPaciente($atendimento);
        $notaAdmissao = $atendimento['status'] === 'aguardando_triagem' && !empty($atendimento['observacao_inicial']);
        $emObservacao = $atendimento['status'] === 'em_observacao';
        $aberto       = $atendimento['status'] !== 'encerrado';
        $idosoSozinho = $emObservacao && empty($atendimento['acompanhante_atual']) && idadeEmAnos($atendimento['data_nascimento']) >= IDADE_PESSOA_IDOSA;
    ?>
    <?php if ($alertas !== '' || $notaAdmissao || $emObservacao || !empty($atendimento['notificacoes_pendentes']) || ($aberto && !empty($atendimento['acompanhante_atual']))): ?>
        <div class="banner-alertas">
            <?php if ($emObservacao): ?>
                <p class="alerta-paciente">
                    <i class="fa fa-bed" aria-hidden="true"></i>
                    <span>Em observação em <strong><?= esc($atendimento['leito_observacao']) ?></strong> desde <?= esc(formatDataHora($atendimento['data_hora_inicio_observacao'])) ?> · permanência <?= badgePermanencia($atendimento['data_hora_chegada']) ?></span>
                </p>
            <?php endif; ?>
            <?= $alertas ?>
            <?php if (!empty($atendimento['notificacoes_pendentes'])): ?>
                <p class="alerta-paciente alerta-notificacao">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                    <span><strong>Notificação compulsória pendente:</strong> <?= esc($atendimento['notificacoes_pendentes']) ?></span>
                </p>
            <?php endif; ?>
            <?php if ($aberto && !empty($atendimento['acompanhante_atual'])): ?>
                <p class="alerta-paciente">
                    <i class="fa fa-user-o" aria-hidden="true"></i>
                    <span>Acompanhante: <?= esc($atendimento['acompanhante_atual']) ?></span>
                </p>
            <?php elseif ($idosoSozinho): ?>
                <p class="alerta-paciente">
                    <i class="fa fa-user-o" aria-hidden="true"></i>
                    <span>Pessoa idosa em observação sem acompanhante registrado.</span>
                </p>
            <?php endif; ?>
            <?php if ($notaAdmissao): ?>
                <p class="alerta-paciente">
                    <i class="fa fa-commenting-o" aria-hidden="true"></i>
                    <span><strong>Admissão:</strong> <?= esc($atendimento['observacao_inicial']) ?></span>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
