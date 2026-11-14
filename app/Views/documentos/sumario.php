<?= $this->extend('templates/layout_impressao') ?>

<?= $this->section("main") ?>

<dl class="folha-identificacao">
    <div>
        <dt>Paciente</dt>
        <dd><?= esc(nomePaciente($atendimento)) ?><?= !empty($atendimento['nome_social']) ? ' <small>(nome civil: ' . esc($atendimento['nome_completo']) . ')</small>' : '' ?></dd>
    </div>
    <div>
        <dt>Nascimento</dt>
        <dd><?= esc(formatData($atendimento['data_nascimento'])) ?></dd>
    </div>
    <div>
        <dt>CPF</dt>
        <dd><?= esc($atendimento['cpf'] ?: '—') ?></dd>
    </div>
    <div>
        <dt>Atendimento</dt>
        <dd><?= esc($atendimento['numero']) ?></dd>
    </div>
</dl>

<?php if (!empty($atendimento['alergias'])): ?>
    <p class="folha-alerta"><strong>Alergias:</strong> <?= esc($atendimento['alergias']) ?></p>
<?php endif; ?>

<dl class="folha-dados">
    <div>
        <dt>Chegada</dt>
        <dd><?= esc(formatDataHora($atendimento['data_hora_chegada'])) ?></dd>
    </div>
    <div>
        <dt>Classificação de risco</dt>
        <dd><?= esc($atendimento['classificacao'] ?? '—') ?></dd>
    </div>
    <div>
        <dt>Encerramento</dt>
        <dd><?= esc(formatDataHora($atendimento['data_hora_encerramento'])) ?></dd>
    </div>
    <div>
        <dt>Desfecho</dt>
        <dd><?= esc(getDescricaoStatus($atendimento['desfecho'])) ?></dd>
    </div>
</dl>

<?php if ($atendimento['desfecho'] === 'obito'): ?>
    <p class="folha-texto">Óbito em <?= esc(formatDataHora($atendimento['data_hora_obito'])) ?><?= !empty($atendimento['declaracao_obito']) ? ' · declaração de óbito nº ' . esc($atendimento['declaracao_obito']) : '' ?>.</p>
<?php elseif ($atendimento['desfecho'] === 'alta_pedido'): ?>
    <p class="folha-texto">Alta a pedido, com termo de responsabilidade assinado pelo paciente ou responsável.</p>
<?php endif; ?>

<h2 class="folha-subtitulo">Queixa principal</h2>
<p class="folha-texto"><?= esc($atendimento['queixa_principal'] ?? '—') ?></p>

<?php if (!empty($atendimento['cid_principal'])): ?>
    <h2 class="folha-subtitulo">Diagnóstico principal (CID-10)</h2>
    <p class="folha-texto"><strong><?= esc(formataCid($atendimento['cid_principal'])) ?></strong> — <?= esc($atendimento['cid_descricao']) ?></p>
<?php endif; ?>

<h2 class="folha-subtitulo"><?= $atendimento['desfecho'] === 'evasao' ? 'Como a saída foi constatada' : 'Resumo do atendimento' ?></h2>
<div class="folha-texto"><?= nl2br(esc($atendimento['resumo_final'])) ?></div>

<?php if (!empty($exames)): ?>
    <h2 class="folha-subtitulo">Exames</h2>
    <ul class="folha-lista">
        <?php foreach ($exames as $exame): ?>
            <li><strong><?= esc($exame['descricao']) ?></strong>: <?= !empty($exame['resultado']) ? esc($exame['resultado']) : 'sem resultado registrado' ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($medicamentos)): ?>
    <h2 class="folha-subtitulo">Medicamentos no pronto atendimento</h2>
    <ul class="folha-lista">
        <?php foreach ($medicamentos as $item): ?>
            <li><strong><?= esc($item['medicamento']) ?></strong> <?= esc($item['dose']) ?> · <?= esc($item['via']) ?> · <?= esc($item['frequencia']) ?><?= $item['situacao'] === 'suspenso' ? ' (suspenso)' : '' ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($atendimento['observacoes_finais'])): ?>
    <h2 class="folha-subtitulo">Observações finais</h2>
    <p class="folha-texto"><?= nl2br(esc($atendimento['observacoes_finais'])) ?></p>
<?php endif; ?>

<?php if (!empty($atendimento['medico'])): ?>
    <div class="folha-assinatura">
        <p>Muriaé, <?= esc(formatData($atendimento['data_hora_encerramento'])) ?>.</p>
        <span class="linha-assinatura"></span>
        <strong><?= esc($atendimento['medico']) ?></strong>
        <span>Médico responsável<?= !empty($atendimento['registro_medico']) ? ' · ' . esc($atendimento['registro_medico']) : '' ?></span>
    </div>
<?php endif; ?>

<p class="folha-rodape">Sumário emitido em <?= esc(date("d/m/Y H:i")) ?> pelo sistema Muriaé Pronto. Protótipo acadêmico, sem validade legal.</p>

<?= $this->endSection() ?>
