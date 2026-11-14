<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?php foreach ($protocolos as $protocolo => $etapasProtocolo): ?>
    <section class="admin-panel mb-3">
        <div class="admin-panel-header">
            <div>
                <h2><?= esc($protocolo) ?></h2>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Ordem</th>
                        <th>Etapa</th>
                        <th>Meta</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etapasProtocolo as $etapa): ?>
                        <tr>
                            <td><span class="numero-fila"><?= esc($etapa['ordem']) ?></span></td>
                            <td class="fw-semibold"><?= esc($etapa['descricao']) ?></td>
                            <td>até <?= esc($etapa['meta_minutos']) ?> min</td>
                            <td class="acoes-tabela">
                                <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('admin/protocolos/form/update/' . $etapa['id_etapa']) ?>">Alterar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endforeach; ?>

<?= $this->endSection() ?>
