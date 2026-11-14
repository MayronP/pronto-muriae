<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Pacientes em observação <span class="text-muted fw-normal">(<?= esc((string) count($atendimentos)) ?>)</span></h2>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Leito</th>
                    <th>Paciente</th>
                    <th>Classificação</th>
                    <th>Em observação desde</th>
                    <th>Permanência no PA</th>
                    <th>Médico</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($atendimentos)): ?>
                    <?php foreach ($atendimentos as $atendimento): ?>
                        <tr class="<?= situacaoPermanencia($atendimento['data_hora_chegada'])['nivel'] === 'excedido' ? 'linha-atrasada' : '' ?>">
                            <td class="fw-semibold"><?= esc($atendimento['leito_observacao']) ?></td>
                            <td>
                                <span class="fw-semibold text-nowrap"><?= esc(nomePaciente($atendimento)) ?></span>
                                <?php if (!empty($atendimento['alergias'])): ?>
                                    <span class="tag tag-alerta ms-1" title="Alergias: <?= esc($atendimento['alergias']) ?>">Alergia</span>
                                <?php endif; ?>
                                <?php if (!empty($atendimento['nao_identificado'])): ?>
                                    <span class="tag tag-roxa ms-1">Não identificado</span>
                                <?php endif; ?>
                                <?php foreach (array_filter(explode(', ', (string) $atendimento['protocolos_abertos'])) as $protocolo): ?>
                                    <span class="tag tag-protocolo ms-1"><?= esc($protocolo) ?></span>
                                <?php endforeach; ?>
                                <span class="texto-secundario">
                                    <?= esc($atendimento['numero']) ?>
                                    <?= empty($atendimento['acompanhantes']) && idadeEmAnos($atendimento['data_nascimento']) >= IDADE_PESSOA_IDOSA ? ' · pessoa idosa sem acompanhante' : '' ?>
                                </span>
                                <?php $news2 = calculaNews2($atendimento, $atendimento['data_nascimento']); ?>
                                <?php if ($news2['escore'] !== null && $news2['risco'] !== 'Baixo'): ?>
                                    <span class="d-block mt-1"><?= badgeNews2($news2) ?></span>
                                <?php endif; ?>
                                <?= pendenciasQuadro($atendimento) ?>
                            </td>
                            <td>
                                <?php if (!empty($atendimento['classificacao'])): ?>
                                    <span class="legenda-risco texto-risco-<?= esc($atendimento['cor']) ?>"><?= esc($atendimento['classificacao']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap"><?= esc(formatDataHora($atendimento['data_hora_inicio_observacao'])) ?></td>
                            <td><?= badgePermanencia($atendimento['data_hora_chegada']) ?></td>
                            <td><?= esc($atendimento['medico'] ?? '—') ?></td>
                            <td class="acoes-tabela">
                                <?php if ($ehMedico): ?>
                                    <a class="btn btn-brand btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>">Reavaliar</a>
                                <?php elseif ($ehEnfermagem): ?>
                                    <a class="btn btn-outline-brand btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>">Enfermagem</a>
                                <?php else: ?>
                                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>">Prontuário</a>
                                <?php endif; ?>
                                <?php if ($ehMedico || $ehEnfermagem): ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if ($ehEnfermagem): ?>
                                                <li><a class="dropdown-item" href="<?= base_url('medicamentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-medkit" aria-hidden="true"></i>Medicamentos</a></li>
                                                <li><a class="dropdown-item" href="<?= base_url('reclassificacao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-retweet" aria-hidden="true"></i>Reclassificar</a></li>
                                                <li><a class="dropdown-item" href="<?= base_url('acompanhantes/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-user-o" aria-hidden="true"></i>Acompanhante</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="<?= base_url('protocolos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-check-square-o" aria-hidden="true"></i>Protocolos</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('plantao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-exchange" aria-hidden="true"></i>Passagem de plantão</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o" aria-hidden="true"></i>Prontuário</a></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="7"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente em observação.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
