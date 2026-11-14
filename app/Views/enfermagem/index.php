<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <?php if ($ehEnfermagem): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('medicamentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-medkit me-1" aria-hidden="true"></i>Medicamentos</a>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('protocolos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-check-square-o me-1" aria-hidden="true"></i>Protocolos</a>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('acompanhantes/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-user-o me-1" aria-hidden="true"></i>Acompanhante</a>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Mais</button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= base_url('reclassificacao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-retweet" aria-hidden="true"></i>Reclassificar</a></li>
                <li><a class="dropdown-item" href="<?= base_url('notificacoes/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-bell-o" aria-hidden="true"></i>Notificação compulsória</a></li>
                <li><a class="dropdown-item" href="<?= base_url('plantao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-exchange" aria-hidden="true"></i>Passagem de plantão</a></li>
                <li><a class="dropdown-item" href="<?= base_url('documentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-print" aria-hidden="true"></i>Declaração de comparecimento</a></li>
                <li><a class="dropdown-item" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o" aria-hidden="true"></i>Prontuário</a></li>
            </ul>
        </div>
    <?php else: ?>
        <?php if (in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
        <?php else: ?>
            <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('fila') ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Fila</a>
        <?php endif; ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Prontuário</a>
    <?php endif; ?>
</div>

<section class="admin-panel mb-3">
    <div class="admin-panel-header">
        <div>
            <h2>Sinais vitais <span class="text-muted fw-normal">(<?= esc((string) (count($sinais) + 1)) ?> aferições)</span></h2>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle tabela-sinais">
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>PA</th>
                    <th>FC</th>
                    <th>FR</th>
                    <th>Temp.</th>
                    <th>SpO₂</th>
                    <th>Consc.</th>
                    <th>Dor</th>
                    <th>Glicemia</th>
                    <th>NEWS2</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sinais as $sinal): ?>
                    <?php $news2 = calculaNews2(array_merge($sinal, ['gestante' => $atendimento['gestante']]), $atendimento['data_nascimento']); ?>
                    <tr>
                        <td class="text-nowrap fw-semibold"><?= esc(formatDataHora($sinal['data_hora'])) ?></td>
                        <td class="text-nowrap"><?= esc($sinal['pressao_arterial']) ?></td>
                        <td><?= esc($sinal['frequencia_cardiaca']) ?></td>
                        <td><?= esc($sinal['frequencia_respiratoria']) ?></td>
                        <td><?= esc(formatValor($sinal['temperatura'], 1)) ?></td>
                        <td class="text-nowrap"><?= esc($sinal['saturacao']) ?>%<?= $sinal['oxigenio_suplementar'] == 1 ? ' <span class="texto-ajuda">O₂</span>' : '' ?></td>
                        <td><?= esc($sinal['nivel_consciencia']) ?></td>
                        <td><?= $sinal['dor'] !== null ? esc($sinal['dor']) . '/10' : '<span class="text-muted">—</span>' ?></td>
                        <td><?= $sinal['glicemia'] !== null ? esc($sinal['glicemia']) : '<span class="text-muted">—</span>' ?></td>
                        <td><?= $news2['escore'] !== null ? badgeNews2($news2) : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <?= esc($sinal['usuario']) ?>
                            <?php if (!empty($sinal['observacoes'])): ?>
                                <span class="texto-secundario"><?= esc($sinal['observacoes']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php $news2 = calculaNews2($atendimento, $atendimento['data_nascimento']); ?>
                <tr class="linha-triagem">
                    <td class="text-nowrap fw-semibold"><?= esc(formatDataHora($atendimento['data_hora_triagem'])) ?> <span class="tag tag-neutra ms-1">Triagem</span></td>
                    <td class="text-nowrap"><?= esc($atendimento['pressao_arterial']) ?></td>
                    <td><?= esc($atendimento['frequencia_cardiaca']) ?></td>
                    <td><?= esc($atendimento['frequencia_respiratoria']) ?></td>
                    <td><?= esc(formatValor($atendimento['temperatura'], 1)) ?></td>
                    <td class="text-nowrap"><?= esc($atendimento['saturacao']) ?>%<?= $atendimento['oxigenio_suplementar'] == 1 ? ' <span class="texto-ajuda">O₂</span>' : '' ?></td>
                    <td><?= esc($atendimento['nivel_consciencia']) ?></td>
                    <td><?= $atendimento['dor'] !== null ? esc($atendimento['dor']) . '/10' : '<span class="text-muted">—</span>' ?></td>
                    <td><?= $atendimento['glicemia'] !== null ? esc($atendimento['glicemia']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= $news2['escore'] !== null ? badgeNews2($news2) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= esc($atendimento['enfermeiro']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?= form_open("enfermagem/sinais") ?>
        <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

        <div class="admin-panel-body border-top">
            <h3 class="secao-form">Nova aferição</h3>
            <div class="row g-2">
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="pressao_arterial" class="form-label campo-obrigatorio">PA (mmHg)</label>
                    <input type="text" class="form-control form-control-sm <?= !empty($erros['pressao_arterial']) ? 'is-invalid' : '' ?>" id="pressao_arterial" name="pressao_arterial"
                        placeholder="120/80" value="<?= setValue("pressao_arterial", $dados) ?>" required>
                    <?= setMsgErrorCampo("pressao_arterial", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-1">
                    <label for="frequencia_cardiaca" class="form-label campo-obrigatorio">FC</label>
                    <input type="number" class="form-control form-control-sm <?= !empty($erros['frequencia_cardiaca']) ? 'is-invalid' : '' ?>" id="frequencia_cardiaca" name="frequencia_cardiaca"
                        min="1" max="300" value="<?= setValue("frequencia_cardiaca", $dados) ?>" required>
                    <?= setMsgErrorCampo("frequencia_cardiaca", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-1">
                    <label for="frequencia_respiratoria" class="form-label campo-obrigatorio">FR</label>
                    <input type="number" class="form-control form-control-sm <?= !empty($erros['frequencia_respiratoria']) ? 'is-invalid' : '' ?>" id="frequencia_respiratoria" name="frequencia_respiratoria"
                        min="1" max="100" value="<?= setValue("frequencia_respiratoria", $dados) ?>" required>
                    <?= setMsgErrorCampo("frequencia_respiratoria", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-1">
                    <label for="temperatura" class="form-label campo-obrigatorio">Temp. (°C)</label>
                    <input type="number" class="form-control form-control-sm <?= !empty($erros['temperatura']) ? 'is-invalid' : '' ?>" id="temperatura" name="temperatura"
                        step="0.1" min="30" max="45" value="<?= setValue("temperatura", $dados) ?>" required>
                    <?= setMsgErrorCampo("temperatura", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-1">
                    <label for="saturacao" class="form-label campo-obrigatorio">SpO₂ (%)</label>
                    <input type="number" class="form-control form-control-sm <?= !empty($erros['saturacao']) ? 'is-invalid' : '' ?>" id="saturacao" name="saturacao"
                        min="0" max="100" value="<?= setValue("saturacao", $dados) ?>" required>
                    <?= setMsgErrorCampo("saturacao", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="oxigenio_suplementar" class="form-label campo-obrigatorio">O₂ suplementar</label>
                    <select class="form-select form-select-sm <?= !empty($erros['oxigenio_suplementar']) ? 'is-invalid' : '' ?>" id="oxigenio_suplementar" name="oxigenio_suplementar" required>
                        <option value="0" <?= setValue("oxigenio_suplementar", $dados) == "0" ? "selected" : "" ?>>Não</option>
                        <option value="1" <?= setValue("oxigenio_suplementar", $dados) == "1" ? "selected" : "" ?>>Sim</option>
                    </select>
                    <?= setMsgErrorCampo("oxigenio_suplementar", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="nivel_consciencia" class="form-label campo-obrigatorio">Consciência</label>
                    <select class="form-select form-select-sm <?= !empty($erros['nivel_consciencia']) ? 'is-invalid' : '' ?>" id="nivel_consciencia" name="nivel_consciencia" required>
                        <?php foreach (["A", "C", "V", "P", "U"] as $nivel): ?>
                            <option value="<?= $nivel ?>" <?= setValue("nivel_consciencia", $dados) == $nivel ? "selected" : "" ?>><?= $nivel ?> — <?= esc(getDescricaoConsciencia($nivel)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= setMsgErrorCampo("nivel_consciencia", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="dor" class="form-label">Dor (0 a 10)</label>
                    <select class="form-select form-select-sm <?= !empty($erros['dor']) ? 'is-invalid' : '' ?>" id="dor" name="dor">
                        <option value="">Não avaliada</option>
                        <?php for ($nota = 0; $nota <= 10; $nota++): ?>
                            <option value="<?= $nota ?>" <?= setValue("dor", $dados) === (string) $nota ? "selected" : "" ?>><?= $nota ?></option>
                        <?php endfor; ?>
                    </select>
                    <?= setMsgErrorCampo("dor", $erros) ?>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="glicemia" class="form-label">Glicemia (mg/dL)</label>
                    <input type="number" class="form-control form-control-sm <?= !empty($erros['glicemia']) ? 'is-invalid' : '' ?>" id="glicemia" name="glicemia"
                        min="1" max="2000" value="<?= setValue("glicemia", $dados) ?>">
                    <?= setMsgErrorCampo("glicemia", $erros) ?>
                </div>
                <div class="col-12 col-md-9 col-xl-10">
                    <label for="observacoes_sinais" class="form-label">Observação</label>
                    <input type="text" class="form-control form-control-sm <?= !empty($erros['observacoes']) ? 'is-invalid' : '' ?>" id="observacoes_sinais" name="observacoes"
                        maxlength="255" value="<?= setValue("observacoes", $dados) ?>" placeholder="Ex.: aferição após analgesia">
                    <?= setMsgErrorCampo("observacoes", $erros) ?>
                </div>
            </div>
        </div>

        <div class="admin-panel-footer">
            <span class="texto-ajuda me-auto">Registrado por <?= esc(session()->get('usuarioNome')) ?>.</span>
            <button class="btn btn-brand btn-sm" type="submit">Registrar aferição</button>
        </div>
    <?= form_close() ?>
</section>

<div class="row g-3">
    <?php if ($ehEnfermagem): ?>
        <div class="col-12 col-xl-5">
            <?= form_open("enfermagem/evolucao") ?>

                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

                <section class="admin-panel">
                    <div class="admin-panel-header">
                        <div>
                            <h2>Novo registro de enfermagem</h2>
                        </div>
                    </div>
                    <div class="admin-panel-body">
                        <div class="mb-3">
                            <label for="avaliacao" class="form-label">Avaliação de enfermagem</label>
                            <textarea class="form-control <?= !empty($erros['avaliacao']) ? 'is-invalid' : '' ?>" id="avaliacao" name="avaliacao" rows="3"
                                placeholder="O que o paciente relata e o exame físico."><?= setValue("avaliacao", $dados) ?></textarea>
                            <?= setMsgErrorCampo("avaliacao", $erros) ?>
                        </div>
                        <div>
                            <label for="implementacao" class="form-label">Cuidados realizados</label>
                            <textarea class="form-control <?= !empty($erros['implementacao']) ? 'is-invalid' : '' ?>" id="implementacao" name="implementacao" rows="2"><?= setValue("implementacao", $dados) ?></textarea>
                            <?= setMsgErrorCampo("implementacao", $erros) ?>
                        </div>
                    </div>
                    <div class="admin-panel-footer">
                        <button class="btn btn-brand ms-auto" type="submit">Salvar registro</button>
                    </div>
                </section>

            <?= form_close() ?>
        </div>
    <?php endif; ?>

    <div class="col-12 <?= $ehEnfermagem ? 'col-xl-7' : '' ?>">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Registros de enfermagem <span class="text-muted fw-normal">(<?= esc((string) count($evolucoes)) ?>)</span></h2>
            </div>
            <?php if (!empty($evolucoes)): ?>
                <div class="admin-panel-body linha-tempo">
                    <?php foreach ($evolucoes as $evolucao): ?>
                        <article>
                            <p class="linha-tempo-cabecalho">
                                <strong><?= esc(formatDataHora($evolucao['data_hora'])) ?></strong> · <?= esc($evolucao['usuario']) ?><?= !empty($evolucao['registro_profissional']) ? ' · ' . esc($evolucao['registro_profissional']) : '' ?>
                            </p>
                            <dl class="lista-dados mb-0">
                                <?php foreach (['avaliacao' => 'Avaliação', 'diagnostico' => 'Diagnóstico de enfermagem', 'planejamento' => 'Planejamento', 'implementacao' => 'Cuidados realizados', 'evolucao' => 'Evolução'] as $campo => $rotulo): ?>
                                    <?php if (!empty($evolucao[$campo])): ?>
                                        <dt><?= esc($rotulo) ?></dt>
                                        <dd><?= nl2br(esc($evolucao[$campo])) ?></dd>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </dl>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum registro de enfermagem neste atendimento.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
