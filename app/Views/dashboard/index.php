<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<div class="row g-3 mb-3">
    <?php foreach ($indicadores as $indicador): ?>
        <div class="col-6 col-md-4 col-xxl-2">
            <article class="admin-panel kpi">
                <p class="kpi-rotulo"><?= esc($indicador['rotulo']) ?></p>
                <div class="kpi-valor"><?= esc((string) $indicador['valor']) ?></div>
                <p class="kpi-detalhe"><?= esc($indicador['detalhe']) ?></p>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-12">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Fila agora</h2>
                <div class="admin-panel-acoes">
                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('fila') ?>">Abrir fila</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Risco</th>
                            <th>Paciente</th>
                            <th>Espera</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fila)): ?>
                            <?php foreach ($fila as $item): ?>
                                <tr class="linha-prioridade-<?= esc($item['cor']) ?>">
                                    <td><span class="badge-risco <?= esc(getClasseRisco($item['cor'])) ?>"><?= esc($item['classificacao']) ?></span></td>
                                    <td>
                                        <span class="fw-semibold"><?= esc(nomePaciente($item)) ?></span>
                                        <span class="texto-secundario"><?= esc($item['numero']) ?> · <?= esc($item['queixa_principal']) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php if ($item['status'] === 'aguardando_medico'): ?>
                                            <?= esc(tempoDecorrido($item['data_hora_reclassificacao'] ?: $item['data_hora_triagem'])) ?>
                                            <span class="texto-secundario"><?= badgeMeta($item['data_hora_reclassificacao'] ?: $item['data_hora_triagem'], $item['tempo_alvo_minutos']) ?></span>
                                        <?php else: ?>
                                            <?= esc(formataMinutos($item['minutos_espera'])) ?>
                                            <span class="texto-secundario">até o médico</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="status-badge <?= esc(getClasseStatus($item['status'])) ?>"><?= esc(getDescricaoStatus($item['status'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente na fila médica.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
