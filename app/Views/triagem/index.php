<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Aguardando classificação de risco <span class="text-muted fw-normal">(<?= esc((string) count($atendimentos)) ?>)</span></h2>
        </div>
        <div class="admin-panel-acoes">
            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('triagem') ?>"><i class="fa fa-refresh me-1" aria-hidden="true"></i>Atualizar</a>
            <a class="btn btn-outline-brand btn-sm" href="<?= base_url('pacientes') ?>"><i class="fa fa-plus me-1" aria-hidden="true"></i>Nova admissão</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Idade</th>
                    <th>Chegada</th>
                    <th>Espera</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($atendimentos)): ?>
                    <?php foreach ($atendimentos as $ordem => $atendimento): ?>
                        <tr>
                            <td><span class="numero-fila"><?= esc($ordem + 1) ?></span></td>
                            <td>
                                <span class="fw-semibold text-nowrap"><?= esc(nomePaciente($atendimento)) ?></span>
                                <?php if (!empty($atendimento['alergias'])): ?>
                                    <span class="tag tag-alerta ms-1" title="Alergias: <?= esc($atendimento['alergias']) ?>">Alergia</span>
                                <?php endif; ?>
                                <?php if (!empty($atendimento['nao_identificado'])): ?>
                                    <span class="tag tag-roxa ms-1">Não identificado</span>
                                <?php endif; ?>
                                <span class="texto-secundario">
                                    <?= esc($atendimento['numero']) ?>
                                    <?= !empty($atendimento['total_chamadas']) ? ' · chamado ' . esc($atendimento['total_chamadas']) . '× (' . esc(formatHora($atendimento['ultima_chamada'])) . ')' : '' ?>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <?= esc(calculaIdade($atendimento['data_nascimento'])) ?>
                                <?php if (getDescricaoSexo($atendimento['sexo']) !== ''): ?>
                                    <span class="texto-secundario"><?= esc(getDescricaoSexo($atendimento['sexo'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap"><?= esc(formatHora($atendimento['data_hora_chegada'])) ?></td>
                            <td class="text-nowrap fw-semibold"><?= esc(tempoDecorrido($atendimento['data_hora_chegada'])) ?></td>
                            <td class="acoes-tabela">
                                <a class="btn btn-brand btn-sm" href="<?= base_url('triagem/form/' . $atendimento['id_atendimento']) ?>">Classificar</a>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <?= form_open("painel/chamar") ?>
                                                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">
                                                <button class="dropdown-item" type="submit"><i class="fa fa-bullhorn" aria-hidden="true"></i>Chamar no painel</button>
                                            <?= form_close() ?>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="<?= base_url('evasao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-sign-out" aria-hidden="true"></i>Saiu sem atendimento</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="6"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente aguardando classificação de risco.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
