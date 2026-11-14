<?= $this->extend('templates/layout_impressao') ?>

<?= $this->section("main") ?>

<?php
    $nome   = nomePaciente($documento);
    $inicio = $documento['data_hora_chegada'];
    $fim    = $documento['data_hora_encerramento'] ?: $documento['data_hora'];
?>

<dl class="folha-identificacao">
    <div>
        <dt>Paciente</dt>
        <dd><?= esc($nome) ?><?= !empty($documento['nome_social']) ? ' <small>(nome civil: ' . esc($documento['nome_completo']) . ')</small>' : '' ?></dd>
    </div>
    <div>
        <dt>Nascimento</dt>
        <dd><?= esc(formatData($documento['data_nascimento'])) ?></dd>
    </div>
    <div>
        <dt>CPF</dt>
        <dd><?= esc($documento['cpf'] ?: '—') ?></dd>
    </div>
    <div>
        <dt>Atendimento</dt>
        <dd><?= esc($documento['numero']) ?></dd>
    </div>
</dl>

<?php if ($documento['tipo'] === 'receita'): ?>
    <h2 class="folha-subtitulo">Uso conforme orientação</h2>
    <div class="folha-texto"><?= nl2br(esc($documento['conteudo'])) ?></div>

<?php elseif ($documento['tipo'] === 'atestado'): ?>
    <p class="folha-texto">
        Atesto, para os devidos fins, que <strong><?= esc($nome) ?></strong> foi atendido(a) neste pronto atendimento em
        <?= esc(formatData($inicio)) ?> e necessita de afastamento de suas atividades por
        <strong><?= esc($documento['dias_afastamento']) ?> dia(s)</strong>, a partir de <?= esc(formatData($documento['data_hora'])) ?>.
    </p>
    <?php if (!empty($documento['cid'])): ?>
        <p class="folha-texto">CID-10: <strong><?= esc(formataCid($documento['cid'])) ?></strong> — <?= esc($documento['cid_descricao']) ?>. Informado com autorização expressa do paciente.</p>
    <?php endif; ?>
    <?php if (!empty($documento['conteudo'])): ?>
        <p class="folha-texto">Observação: <?= nl2br(esc($documento['conteudo'])) ?></p>
    <?php endif; ?>

<?php elseif ($documento['tipo'] === 'comparecimento'): ?>
    <p class="folha-texto">
        Declaro, para os devidos fins, que <strong><?= esc($nome) ?></strong> esteve neste pronto atendimento
        <?php if (formatData($inicio) === formatData($fim)): ?>
            em <?= esc(formatData($inicio)) ?>, das <?= esc(formatHora($inicio)) ?> às <?= esc(formatHora($fim)) ?>.
        <?php else: ?>
            de <?= esc(formatDataHora($inicio)) ?> a <?= esc(formatDataHora($fim)) ?>.
        <?php endif; ?>
    </p>
    <?php if (!empty($documento['conteudo'])): ?>
        <p class="folha-texto">Observação: <?= nl2br(esc($documento['conteudo'])) ?></p>
    <?php endif; ?>

<?php else: ?>
    <p class="folha-texto">Encaminho o(a) paciente <strong><?= esc($nome) ?></strong> para: <strong><?= esc($documento['destino']) ?></strong>.</p>
    <h2 class="folha-subtitulo">Motivo e resumo clínico</h2>
    <div class="folha-texto"><?= nl2br(esc($documento['conteudo'])) ?></div>
<?php endif; ?>

<div class="folha-assinatura">
    <p>Muriaé, <?= esc(formatData($documento['data_hora'])) ?>.</p>
    <span class="linha-assinatura"></span>
    <strong><?= esc($documento['usuario']) ?></strong>
    <span><?= esc($documento['perfil']) ?><?= !empty($documento['registro_profissional']) ? ' · ' . esc($documento['registro_profissional']) : '' ?></span>
</div>

<p class="folha-rodape">Documento nº <?= esc($documento['id_documento']) ?> emitido em <?= esc(formatDataHora($documento['data_hora'])) ?> pelo sistema Muriaé Pronto. Protótipo acadêmico, sem validade legal.</p>

<?= $this->endSection() ?>
