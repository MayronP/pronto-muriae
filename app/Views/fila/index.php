<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <h2><?= $status === 'em_atendimento' ? 'Pacientes com médico' : 'Pacientes na fila' ?> <span class="text-muted fw-normal">(<?= esc((string) count($fila)) ?>)</span></h2>
        <div class="admin-panel-acoes">
            <span class="texto-ajuda d-none d-sm-inline">Atualizado às <?= esc($horaAtualizacao) ?></span>
            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('fila?status=' . $status) ?>"><i class="fa fa-refresh me-1" aria-hidden="true"></i>Atualizar</a>
        </div>
    </div>

    <div class="admin-panel-filtros">
        <form class="row g-2" action="<?= base_url('fila') ?>" method="get">
            <div class="col-12 col-md-5 col-lg-4">
                <label for="busca_fila" class="visually-hidden">Localizar na fila</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="search" class="form-control form-control-sm" id="busca_fila" name="busca" placeholder="Nome ou número do atendimento" value="<?= esc($busca) ?>">
                </div>
            </div>
            <div class="col-8 col-sm-6 col-md-3">
                <label for="status_fila" class="visually-hidden">Status</label>
                <select class="form-select form-select-sm" id="status_fila" name="status">
                    <option value="">Aguardando e em atendimento</option>
                    <option value="aguardando_medico" <?= $status === 'aguardando_medico' ? 'selected' : '' ?>>Aguardando médico</option>
                    <option value="em_atendimento" <?= $status === 'em_atendimento' ? 'selected' : '' ?>>Em atendimento</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Risco</th>
                    <th>Paciente</th>
                    <th>Queixa principal</th>
                    <th>Espera</th>
                    <th>Chegada</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($fila)): ?>
                    <?php foreach ($fila as $item): ?>
                        <?php
                            $referencia = $item['data_hora_reclassificacao'] ?: $item['data_hora_triagem'];
                            $aguardando = $item['status'] === 'aguardando_medico';
                            $atrasado   = $aguardando && situacaoMeta($referencia, $item['tempo_alvo_minutos'])['atrasado'];
                            $news2      = calculaNews2($item, $item['data_nascimento']);
                        ?>
                        <tr class="linha-prioridade-<?= esc($item['cor']) ?><?= $atrasado ? ' linha-atrasada' : '' ?>">
                            <td>
                                <span class="badge-risco <?= esc(getClasseRisco($item['cor'])) ?>"><?= esc($item['classificacao']) ?></span>
                                <?php if (!empty($item['data_hora_reclassificacao'])): ?>
                                    <span class="texto-secundario text-nowrap">reclassificado às <?= esc(formatHora($item['data_hora_reclassificacao'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-semibold text-nowrap"><?= esc(nomePaciente($item)) ?></span>
                                <?php if (!empty($item['alergias'])): ?>
                                    <span class="tag tag-alerta ms-1" title="Alergias: <?= esc($item['alergias']) ?>">Alergia</span>
                                <?php endif; ?>
                                <?php if (!empty($item['nao_identificado'])): ?>
                                    <span class="tag tag-roxa ms-1">Não identificado</span>
                                <?php endif; ?>
                                <?php foreach (array_filter(explode(', ', (string) $item['protocolos_abertos'])) as $protocolo): ?>
                                    <span class="tag tag-protocolo ms-1"><?= esc($protocolo) ?></span>
                                <?php endforeach; ?>
                                <span class="texto-secundario">
                                    <?= esc($item['numero']) ?><?= !empty($item['data_nascimento']) ? ' · ' . esc(calculaIdade($item['data_nascimento'])) : '' ?>
                                </span>
                                <?php if (!empty($item['total_chamadas'])): ?>
                                    <span class="texto-secundario"><i class="fa fa-bullhorn me-1" aria-hidden="true"></i>chamado <?= esc($item['total_chamadas']) ?>× · última às <?= esc(formatHora($item['ultima_chamada'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($item['queixa_principal']) ?>
                                <?php if ($news2['escore'] !== null && $news2['risco'] !== 'Baixo'): ?>
                                    <span class="d-block mt-1"><?= badgeNews2($news2) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php if ($aguardando): ?>
                                    <span class="fw-semibold"><?= esc(tempoDecorrido($referencia)) ?></span>
                                    <span class="texto-secundario"><?= badgeMeta($referencia, $item['tempo_alvo_minutos']) ?></span>
                                <?php else: ?>
                                    <?= esc(formataMinutos($item['minutos_espera'])) ?>
                                    <span class="texto-secundario">até o médico</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?= esc(formatHora($item['data_hora_chegada'])) ?>
                                <span class="texto-secundario">há <?= esc(tempoDecorrido($item['data_hora_chegada'])) ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?= esc(getClasseStatus($item['status'])) ?>"><?= esc(getDescricaoStatus($item['status'])) ?></span>
                                <?= pendenciasQuadro($item) ?>
                            </td>
                            <td class="acoes-tabela">
                                <?php if ($ehMedico): ?>
                                    <?= form_open("atendimentos/iniciar", ["class" => "d-inline"]) ?>
                                        <input type="hidden" name="id_atendimento" value="<?= esc($item['id_atendimento']) ?>">
                                        <button class="btn btn-brand btn-sm" type="submit"><?= $aguardando ? 'Iniciar' : 'Continuar' ?></button>
                                    <?= form_close() ?>
                                <?php elseif ($ehEnfermagem && $aguardando): ?>
                                    <a class="btn <?= $atrasado ? 'btn-brand' : 'btn-outline-brand' ?> btn-sm" href="<?= base_url('reclassificacao/form/' . $item['id_atendimento']) ?>">Reclassificar</a>
                                <?php elseif ($ehEnfermagem): ?>
                                    <a class="btn btn-outline-brand btn-sm" href="<?= base_url('enfermagem/index/' . $item['id_atendimento']) ?>">Enfermagem</a>
                                <?php else: ?>
                                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $item['id_atendimento']) ?>">Prontuário</a>
                                <?php endif; ?>
                                <?php if ($ehMedico || $ehEnfermagem): ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <?= form_open("painel/chamar") ?>
                                                    <input type="hidden" name="id_atendimento" value="<?= esc($item['id_atendimento']) ?>">
                                                    <button class="dropdown-item" type="submit"><i class="fa fa-bullhorn" aria-hidden="true"></i>Chamar no painel</button>
                                                <?= form_close() ?>
                                            </li>
                                            <?php if ($ehMedico && $aguardando): ?>
                                                <li><a class="dropdown-item" href="<?= base_url('reclassificacao/form/' . $item['id_atendimento']) ?>"><i class="fa fa-retweet" aria-hidden="true"></i>Reclassificar</a></li>
                                            <?php endif; ?>
                                            <?php if ($ehEnfermagem && $aguardando): ?>
                                                <li><a class="dropdown-item" href="<?= base_url('enfermagem/index/' . $item['id_atendimento']) ?>"><i class="fa fa-heartbeat" aria-hidden="true"></i>Sinais vitais e enfermagem</a></li>
                                            <?php endif; ?>
                                            <?php if ($ehEnfermagem && !$aguardando): ?>
                                                <li><a class="dropdown-item" href="<?= base_url('medicamentos/index/' . $item['id_atendimento']) ?>"><i class="fa fa-medkit" aria-hidden="true"></i>Medicamentos</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="<?= base_url('protocolos/index/' . $item['id_atendimento']) ?>"><i class="fa fa-check-square-o" aria-hidden="true"></i>Protocolos</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('atendimentos/detalhes/' . $item['id_atendimento']) ?>"><i class="fa fa-file-text-o" aria-hidden="true"></i>Prontuário</a></li>
                                            <?php if ($aguardando): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="<?= base_url('evasao/form/' . $item['id_atendimento']) ?>"><i class="fa fa-sign-out" aria-hidden="true"></i>Saiu sem atendimento</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="<?= base_url('acompanhantes/index/' . $item['id_atendimento']) ?>"><i class="fa fa-user-o" aria-hidden="true"></i>Acompanhante</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('documentos/index/' . $item['id_atendimento']) ?>"><i class="fa fa-print" aria-hidden="true"></i>Declaração de comparecimento</a></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="7"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente localizado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
