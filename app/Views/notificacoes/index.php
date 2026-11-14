<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Notificações compulsórias <span class="text-muted fw-normal">(<?= esc((string) count($notificacoes)) ?>)</span></h2>
        </div>
        <div class="btn-group btn-group-sm" role="group" aria-label="Situação">
            <a class="btn <?= $situacao === 'pendente' ? 'btn-brand' : 'btn-outline-secondary' ?>" href="<?= base_url('notificacoes?situacao=pendente') ?>">Pendentes</a>
            <a class="btn <?= $situacao === 'notificada' ? 'btn-brand' : 'btn-outline-secondary' ?>" href="<?= base_url('notificacoes?situacao=notificada') ?>">Notificadas</a>
            <a class="btn <?= $situacao === 'todas' ? 'btn-brand' : 'btn-outline-secondary' ?>" href="<?= base_url('notificacoes?situacao=todas') ?>">Todas</a>
        </div>
    </div>

    <?= $this->include('notificacoes/tabela') ?>

</section>

<?= $this->endSection() ?>
