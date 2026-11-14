<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Tempo-alvo por cor</h2>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Prioridade</th>
                    <th>Classificação</th>
                    <th>Tempo-alvo</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classificacoes as $classificacao): ?>
                    <tr class="linha-prioridade-<?= esc($classificacao['cor']) ?>">
                        <td><span class="numero-fila"><?= esc($classificacao['ordem_prioridade']) ?></span></td>
                        <td><span class="badge-risco <?= esc(getClasseRisco($classificacao['cor'])) ?>"><?= esc($classificacao['nome']) ?></span></td>
                        <td class="fw-semibold"><?= $classificacao['tempo_alvo_minutos'] == 0 ? 'Imediato' : esc($classificacao['tempo_alvo_minutos']) . ' min' ?></td>
                        <td class="acoes-tabela">
                            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('admin/classificacoes/form/update/' . $classificacao['id_classificacao']) ?>">Alterar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
