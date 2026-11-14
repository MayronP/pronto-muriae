<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_paciente') ?>

<div class="barra-acoes-paciente">
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes') ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Pacientes</a>
    <?php if ($podeAdmitir): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes/form/update/' . $paciente['id_paciente']) ?>"><i class="fa fa-pencil me-1" aria-hidden="true"></i><?= !empty($paciente['nao_identificado']) ? 'Identificar paciente' : 'Editar cadastro' ?></a>
        <?php if (!$temAtendimentoAberto): ?>
            <a class="btn btn-brand btn-sm ms-md-auto" href="<?= base_url('admissao/form/' . $paciente['id_paciente']) ?>"><i class="fa fa-plus me-1" aria-hidden="true"></i>Nova admissão</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<section class="admin-panel">

    <div class="admin-panel-header">
        <h2>Atendimentos <span class="text-muted fw-normal">(<?= esc((string) count($atendimentos)) ?>)</span></h2>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Atendimento</th>
                    <th>Chegada</th>
                    <th>Classificação</th>
                    <th>Médico</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($atendimentos)): ?>
                    <?php foreach ($atendimentos as $atendimento): ?>
                        <tr>
                            <td class="text-nowrap fw-semibold"><?= esc($atendimento['numero']) ?></td>
                            <td class="text-nowrap"><?= esc(formatDataHora($atendimento['data_hora_chegada'])) ?></td>
                            <td>
                                <?php if (!empty($atendimento['classificacao'])): ?>
                                    <span class="legenda-risco texto-risco-<?= esc($atendimento['cor']) ?>"><?= esc($atendimento['classificacao']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Sem triagem</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($atendimento['medico'] ?? '—') ?></td>
                            <td>
                                <?php if ($atendimento['status'] === 'encerrado'): ?>
                                    <span class="status-badge <?= esc(getClasseDesfecho($atendimento['desfecho'])) ?>"><?= esc(getDescricaoStatus($atendimento['desfecho'])) ?></span>
                                <?php else: ?>
                                    <span class="status-badge <?= esc(getClasseStatus($atendimento['status'])) ?>"><?= esc(getDescricaoStatus($atendimento['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="acoes-tabela">
                                <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>">Prontuário</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="6"><i class="fa fa-inbox" aria-hidden="true"></i>Este paciente ainda não possui atendimentos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
