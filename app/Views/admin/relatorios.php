<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel mb-3 nao-imprimir">
    <div class="admin-panel-body py-3">
        <form class="row g-2 align-items-end" action="<?= base_url('admin/relatorios') ?>" method="get">
            <div class="col-6 col-lg-2">
                <label for="data_inicial" class="form-label">Data inicial</label>
                <input type="date" class="form-control form-control-sm" id="data_inicial" name="data_inicial" value="<?= esc($filtros['data_inicial']) ?>">
            </div>
            <div class="col-6 col-lg-2">
                <label for="data_final" class="form-label">Data final</label>
                <input type="date" class="form-control form-control-sm" id="data_final" name="data_final" value="<?= esc($filtros['data_final']) ?>">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label for="profissional" class="form-label">Profissional</label>
                <select class="form-select form-select-sm" id="profissional" name="id_medico">
                    <option value="">Todos os profissionais</option>
                    <?php foreach ($profissionais as $profissional): ?>
                        <option value="<?= esc($profissional['id_usuario']) ?>" <?= $profissional['id_usuario'] == $filtros['id_medico'] ? 'selected' : '' ?>><?= esc($profissional['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label for="status_relatorio" class="form-label">Status</label>
                <select class="form-select form-select-sm" id="status_relatorio" name="status">
                    <option value="">Todos os status</option>
                    <option value="aguardando" <?= $filtros['status'] === 'aguardando' ? 'selected' : '' ?>>Aguardando</option>
                    <option value="em_atendimento" <?= $filtros['status'] === 'em_atendimento' ? 'selected' : '' ?>>Em atendimento</option>
                    <option value="em_observacao" <?= $filtros['status'] === 'em_observacao' ? 'selected' : '' ?>>Em observação</option>
                    <option value="encerrado" <?= $filtros['status'] === 'encerrado' ? 'selected' : '' ?>>Encerrado</option>
                    <option value="evasao" <?= $filtros['status'] === 'evasao' ? 'selected' : '' ?>>Saiu sem atendimento</option>
                </select>
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button class="btn btn-brand btn-sm flex-fill" type="submit">Aplicar</button>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-imprimir title="Imprimir"><i class="fa fa-print" aria-hidden="true"></i><span class="visually-hidden">Imprimir</span></button>
            </div>
        </form>
    </div>
</section>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-xl-2">
        <article class="admin-panel kpi">
            <p class="kpi-rotulo">Atendimentos</p>
            <div class="kpi-valor"><?= esc((string) (int) $resumoRelatorio['atendimentos']) ?></div>
            <p class="kpi-detalhe">No período selecionado</p>
        </article>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <article class="admin-panel kpi">
            <p class="kpi-rotulo">Prioritários</p>
            <div class="kpi-valor"><?= esc((string) (int) $resumoRelatorio['prioritarios']) ?></div>
            <p class="kpi-detalhe">Vermelho ou laranja</p>
        </article>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <article class="admin-panel kpi">
            <p class="kpi-rotulo">Encerrados</p>
            <div class="kpi-valor"><?= esc((string) (int) $resumoRelatorio['encerrados']) ?></div>
            <p class="kpi-detalhe">Alta, internação, transferência ou óbito</p>
        </article>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <article class="admin-panel kpi">
            <p class="kpi-rotulo">Saíram sem atendimento</p>
            <div class="kpi-valor"><?= esc((string) (int) $resumoRelatorio['evasoes']) ?></div>
            <p class="kpi-detalhe">Taxa de evasão: <?= esc(formataPercentual($resumoRelatorio['evasoes'], $resumoRelatorio['atendimentos'])) ?></p>
        </article>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <article class="admin-panel kpi">
            <p class="kpi-rotulo">Óbitos</p>
            <div class="kpi-valor"><?= esc((string) (int) $resumoRelatorio['obitos']) ?></div>
            <p class="kpi-detalhe">Taxa de óbito: <?= esc(formataPercentual($resumoRelatorio['obitos'], $resumoRelatorio['atendimentos'])) ?></p>
        </article>
    </div>
</div>

<section class="admin-panel mb-3">
    <div class="admin-panel-header">
        <div>
            <h2>Tempos do período</h2>
        </div>
    </div>

    <div class="metricas border-bottom">
        <div>
            <p class="kpi-rotulo">Até a classificação</p>
            <div class="kpi-valor"><?= esc(formataMinutos($tempos['media_classificacao'])) ?></div>
            <p class="kpi-detalhe">Média da chegada à triagem</p>
        </div>
        <div>
            <p class="kpi-rotulo">Até o médico</p>
            <div class="kpi-valor"><?= esc(formataMinutos($tempos['media_espera'])) ?></div>
            <p class="kpi-detalhe">Média após a classificação</p>
        </div>
        <div>
            <p class="kpi-rotulo">Dentro da meta</p>
            <div class="kpi-valor"><?= esc(formataPercentual($tempos['dentro_meta'], $tempos['com_meta_historica'])) ?></div>
            <p class="kpi-detalhe"><?= esc((string) (int) $tempos['dentro_meta']) ?> de <?= esc((string) (int) $tempos['com_meta_historica']) ?> com meta histórica</p>
        </div>
        <div>
            <p class="kpi-rotulo">Permanência média</p>
            <div class="kpi-valor"><?= esc(formataMinutos($tempos['media_permanencia'])) ?></div>
            <p class="kpi-detalhe">Da chegada ao desfecho</p>
        </div>
    </div>

    <p class="texto-ajuda px-3">O percentual usa a meta registrada no início de cada atendimento. Registros antigos sem essa informação ficam fora do percentual; a meta atual é apenas referência.</p>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Classificação</th>
                    <th>Meta atual (referência)</th>
                    <th class="text-end">Atendidos</th>
                    <th class="text-end">Espera média</th>
                    <th class="text-end">Maior espera</th>
                    <th class="text-end">Dentro da meta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classificacoes as $classificacao): ?>
                    <?php $linha = $temposPorCor[$classificacao['id_classificacao']] ?? null; ?>
                    <tr>
                        <td><span class="legenda-risco texto-risco-<?= esc($classificacao['cor']) ?>"><?= esc($classificacao['nome']) ?></span></td>
                        <td><?= $classificacao['tempo_alvo_minutos'] == 0 ? 'Imediato' : esc($classificacao['tempo_alvo_minutos']) . ' min' ?></td>
                        <td class="text-end"><?= esc((string) ($linha['atendidos'] ?? 0)) ?></td>
                        <td class="text-end"><?= esc(formataMinutos($linha['media_espera'] ?? null)) ?></td>
                        <td class="text-end"><?= esc(formataMinutos($linha['maior_espera'] ?? null)) ?></td>
                        <td class="text-end"><?= esc(formataPercentual($linha['dentro_meta'] ?? 0, $linha['com_meta_historica'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-panel">
    <div class="admin-panel-header">
        <h2>Atendimentos por dia e profissional</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Profissional</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Aguardando</th>
                    <th class="text-end">Em atendimento</th>
                    <th class="text-end">Em observação</th>
                    <th class="text-end">Encerrados</th>
                    <th class="text-end">Óbitos</th>
                    <th class="text-end">Saíram sem atendimento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultados)): ?>
                    <?php foreach ($resultados as $resultado): ?>
                        <tr>
                            <td class="fw-semibold text-nowrap"><?= esc(formatData($resultado['periodo'])) ?></td>
                            <td><?= esc($resultado['profissional'] ?? 'Sem médico definido') ?></td>
                            <td class="text-end fw-semibold"><?= esc((string) $resultado['total']) ?></td>
                            <td class="text-end"><?= esc((string) $resultado['aguardando']) ?></td>
                            <td class="text-end"><?= esc((string) $resultado['em_atendimento']) ?></td>
                            <td class="text-end"><?= esc((string) $resultado['em_observacao']) ?></td>
                            <td class="text-end"><?= esc((string) (int) $resultado['encerrados']) ?></td>
                            <td class="text-end"><?= esc((string) (int) $resultado['obitos']) ?></td>
                            <td class="text-end"><?= esc((string) (int) $resultado['evasoes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="9"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum resultado para os filtros informados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-panel mt-3">
    <div class="admin-panel-header">
        <div>
            <h2>Protocolos gerenciados</h2>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Etapa</th>
                    <th>Meta atual (referência)</th>
                    <th class="text-end">Protocolos</th>
                    <th class="text-end">Registradas</th>
                    <th class="text-end">Dentro da meta</th>
                    <th class="text-end">Tempo médio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($protocolos)): ?>
                    <?php foreach ($protocolos as $etapa): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($etapa['protocolo']) ?></td>
                            <td><?= esc($etapa['descricao']) ?></td>
                            <td class="text-nowrap">até <?= esc($etapa['meta_minutos']) ?> min</td>
                            <td class="text-end"><?= esc((string) $etapa['protocolos']) ?></td>
                            <td class="text-end"><?= esc((string) $etapa['registradas']) ?></td>
                            <td class="text-end"><?= esc((string) (int) $etapa['dentro_meta']) ?> <span class="text-muted">(<?= esc(formataPercentual($etapa['dentro_meta'], $etapa['registradas'])) ?>)</span></td>
                            <td class="text-end"><?= esc(formataMinutos($etapa['media_minutos'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="7"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum protocolo aberto no período.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?= $this->endSection() ?>
