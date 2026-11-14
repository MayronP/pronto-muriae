<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Pacientes no pronto atendimento <span class="text-muted fw-normal">(<?= esc((string) count($atendimentos)) ?>)</span></h2>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Classificação</th>
                    <th>Status</th>
                    <th>Última passagem</th>
                    <th>Recebimento</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($atendimentos)): ?>
                    <?php foreach ($atendimentos as $atendimento): ?>
                        <tr>
                            <td>
                                <span class="fw-semibold text-nowrap"><?= esc(nomePaciente($atendimento)) ?></span>
                                <?php if (!empty($atendimento['alergias'])): ?>
                                    <span class="tag tag-alerta ms-1" title="Alergias: <?= esc($atendimento['alergias']) ?>">Alergia</span>
                                <?php endif; ?>
                                <span class="texto-secundario">
                                    <?= esc($atendimento['numero']) ?>
                                    <?= !empty($atendimento['leito_observacao']) ? ' · ' . esc($atendimento['leito_observacao']) : '' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($atendimento['classificacao'])): ?>
                                    <span class="legenda-risco texto-risco-<?= esc($atendimento['cor']) ?>"><?= esc($atendimento['classificacao']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="status-badge <?= esc(getClasseStatus($atendimento['status'])) ?>"><?= esc(getDescricaoStatus($atendimento['status'])) ?></span></td>
                            <td class="text-nowrap">
                                <?php if (!empty($atendimento['id_passagem'])): ?>
                                    <?= esc(formatDataHora($atendimento['data_hora_passagem'])) ?>
                                    <span class="texto-secundario"><?= esc($atendimento['autor_passagem']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Nenhuma</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($atendimento['id_passagem'])): ?>
                                    <span class="text-muted">—</span>
                                <?php elseif (!empty($atendimento['data_hora_recebimento'])): ?>
                                    <span class="status-badge status-concluido">Recebida</span>
                                    <span class="texto-secundario"><?= esc($atendimento['recebedor_passagem']) ?> · <?= esc(formatHora($atendimento['data_hora_recebimento'])) ?></span>
                                <?php else: ?>
                                    <span class="status-badge status-andamento">Aguardando</span>
                                <?php endif; ?>
                            </td>
                            <td class="acoes-tabela">
                                <?php if ($atendimento['pode_confirmar']): ?>
                                    <?= form_open("plantao/confirmar", ["class" => "d-inline"]) ?>
                                        <input type="hidden" name="id_passagem" value="<?= esc($atendimento['id_passagem']) ?>">
                                        <button class="btn btn-brand btn-sm" type="submit">Confirmar recebimento</button>
                                    <?= form_close() ?>
                                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('plantao/form/' . $atendimento['id_atendimento']) ?>">Registrar passagem</a>
                                <?php else: ?>
                                    <a class="btn btn-outline-brand btn-sm" href="<?= base_url('plantao/form/' . $atendimento['id_atendimento']) ?>">Registrar passagem</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="6"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente triado no pronto atendimento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
