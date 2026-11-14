<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <h2>Registros de auditoria <span class="text-muted fw-normal">(<?= esc((string) count($registros)) ?>)</span></h2>
    </div>

    <div class="admin-panel-filtros">
        <form class="row g-2 align-items-end" action="<?= base_url('admin/auditoria') ?>" method="get">
            <div class="col-12 col-md-6 col-lg-3">
                <label for="usuario_auditoria" class="form-label">Usuário</label>
                <input type="search" class="form-control form-control-sm" id="usuario_auditoria" name="usuario" placeholder="Nome do usuário" value="<?= esc($filtros['usuario']) ?>">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label for="acao_auditoria" class="form-label">Ação</label>
                <input type="search" class="form-control form-control-sm" id="acao_auditoria" name="acao" placeholder="Descrição da ação" value="<?= esc($filtros['acao']) ?>">
            </div>
            <div class="col-6 col-lg-2">
                <label for="data_inicial_auditoria" class="form-label">Data inicial</label>
                <input type="date" class="form-control form-control-sm" id="data_inicial_auditoria" name="data_inicial" value="<?= esc($filtros['data_inicial']) ?>">
            </div>
            <div class="col-6 col-lg-2">
                <label for="data_final_auditoria" class="form-label">Data final</label>
                <input type="date" class="form-control form-control-sm" id="data_final_auditoria" name="data_final" value="<?= esc($filtros['data_final']) ?>">
            </div>
            <div class="col-12 col-lg-2">
                <button class="btn btn-outline-secondary btn-sm w-100" type="submit">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Data e hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Registro relacionado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($registros)): ?>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td class="text-nowrap">
                                <?= esc(formatData($registro['data_hora'])) ?>
                                <span class="text-muted ms-1"><?= esc(formatHora($registro['data_hora'])) ?></span>
                            </td>
                            <td class="fw-semibold"><?= esc($registro['usuario']) ?></td>
                            <td><?= esc($registro['acao']) ?></td>
                            <td class="text-muted"><?= esc($registro['detalhes'] ?: $registro['entidade'] . ' #' . $registro['registro_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum registro de auditoria localizado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
