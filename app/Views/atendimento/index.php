<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('exames/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-flask me-1" aria-hidden="true"></i>Exames</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('medicamentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-medkit me-1" aria-hidden="true"></i>Prescrição</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-heartbeat me-1" aria-hidden="true"></i>Sinais e enfermagem</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('protocolos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-check-square-o me-1" aria-hidden="true"></i>Protocolos</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-print me-1" aria-hidden="true"></i>Documentos</a>
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Mais</button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= base_url('reclassificacao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-retweet" aria-hidden="true"></i>Reclassificar</a></li>
            <?php if ($atendimento['status'] === 'em_atendimento'): ?>
                <li><a class="dropdown-item" href="<?= base_url('observacao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-bed" aria-hidden="true"></i>Encaminhar para observação</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="<?= base_url('plantao/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-exchange" aria-hidden="true"></i>Passagem de plantão</a></li>
            <li><a class="dropdown-item" href="<?= base_url('acompanhantes/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-user-o" aria-hidden="true"></i>Acompanhante</a></li>
            <li><a class="dropdown-item" href="<?= base_url('notificacoes/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-bell-o" aria-hidden="true"></i>Notificação compulsória</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o" aria-hidden="true"></i>Prontuário</a></li>
        </ul>
    </div>
    <?php if (in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-brand btn-sm ms-md-auto" href="<?= base_url('atendimentos/encerramento/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-flag-checkered me-1" aria-hidden="true"></i>Encerrar atendimento</a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8 order-xl-2">
        <section class="admin-panel mb-3">
            <div class="admin-panel-header">
                <h2>Nova evolução</h2>
                <span class="texto-ajuda"><?= esc(session()->get('usuarioNome')) ?></span>
            </div>

            <?= form_open("atendimentos/store") ?>
                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

                <div class="admin-panel-body">
                    <div class="mb-3">
                        <label for="evolucao" class="form-label campo-obrigatorio">Evolução</label>
                        <textarea class="form-control <?= !empty($erros['evolucao']) ? 'is-invalid' : '' ?>" id="evolucao" name="evolucao" rows="5"
                            placeholder="Avaliação e evolução clínica do paciente."
                            required><?= setValue("evolucao", $dados) ?></textarea>
                        <?= setMsgErrorCampo("evolucao", $erros) ?>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações clínicas</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"
                            placeholder="Informações complementares, quando necessário."><?= setValue("observacoes", $dados) ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label for="hipotese_diagnostica" class="form-label">Hipótese diagnóstica</label>
                            <textarea class="form-control" id="hipotese_diagnostica" name="hipotese_diagnostica" rows="2"><?= setValue("hipotese_diagnostica", $dados) ?></textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="conclusao_clinica" class="form-label">Conclusão clínica</label>
                            <textarea class="form-control" id="conclusao_clinica" name="conclusao_clinica" rows="2"><?= setValue("conclusao_clinica", $dados) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <?php if ($atendimento['status'] === 'em_observacao'): ?>
                        <a class="btn btn-outline-secondary" href="<?= base_url('observacao') ?>">Voltar para a observação</a>
                    <?php else: ?>
                        <a class="btn btn-outline-secondary" href="<?= base_url('fila') ?>">Voltar para a fila</a>
                    <?php endif; ?>
                    <button class="btn btn-brand ms-auto" type="submit">Salvar evolução</button>
                </div>
            <?= form_close() ?>
        </section>

        <?php if (!empty($passagens)): ?>
            <section class="admin-panel mb-3">
                <div class="admin-panel-header">
                    <div>
                        <h2>Última passagem de plantão</h2>
                        <p>
                            <?= esc(formatDataHora($passagens[0]['data_hora'])) ?> · <?= esc($passagens[0]['autor']) ?> (<?= esc($passagens[0]['perfil_autor']) ?>)
                            <?= !empty($passagens[0]['recebedor']) ? '· recebida por ' . esc($passagens[0]['recebedor']) : '· aguardando recebimento' ?>
                        </p>
                    </div>
                </div>
                <div class="admin-panel-body">
                    <dl class="lista-dados row g-0 mb-0">
                        <div class="col-12 col-md-6 pe-md-3">
                            <dt>Situação</dt>
                            <dd><?= nl2br(esc($passagens[0]['situacao'])) ?></dd>
                            <?php if (!empty($passagens[0]['historico'])): ?>
                                <dt>Breve histórico</dt>
                                <dd><?= nl2br(esc($passagens[0]['historico'])) ?></dd>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-md-6">
                            <dt>Avaliação</dt>
                            <dd><?= nl2br(esc($passagens[0]['avaliacao'])) ?></dd>
                            <dt>Recomendação</dt>
                            <dd><?= nl2br(esc($passagens[0]['recomendacao'])) ?></dd>
                        </div>
                    </dl>
                </div>
            </section>
        <?php endif; ?>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Evoluções <span class="text-muted fw-normal">(<?= esc((string) count($evolucoes)) ?>)</span></h2>
            </div>
            <?php if (!empty($evolucoes)): ?>
                <div class="admin-panel-body linha-tempo">
                    <?php foreach ($evolucoes as $evolucao): ?>
                        <article>
                            <p class="linha-tempo-cabecalho"><strong><?= esc(formatDataHora($evolucao['data_hora'])) ?></strong> · <?= esc($evolucao['medico']) ?></p>
                            <p class="mb-2"><?= nl2br(esc($evolucao['evolucao'])) ?></p>
                            <dl class="lista-dados mb-0">
                                <?php if (!empty($evolucao['observacoes'])): ?>
                                    <dt>Observações clínicas</dt>
                                    <dd><?= nl2br(esc($evolucao['observacoes'])) ?></dd>
                                <?php endif; ?>
                                <?php if (!empty($evolucao['hipotese_diagnostica'])): ?>
                                    <dt>Hipótese diagnóstica</dt>
                                    <dd><?= nl2br(esc($evolucao['hipotese_diagnostica'])) ?></dd>
                                <?php endif; ?>
                                <?php if (!empty($evolucao['conclusao_clinica'])): ?>
                                    <dt>Conclusão clínica</dt>
                                    <dd><?= nl2br(esc($evolucao['conclusao_clinica'])) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma evolução registrada neste atendimento.</p>
            <?php endif; ?>
        </section>
    </div>

    <div class="col-12 col-xl-4 order-xl-1">
        <?php
            $atual = !empty($sinais) ? array_merge($sinais[0], ['gestante' => $atendimento['gestante']]) : $atendimento;
            $news2 = calculaNews2($atual, $atendimento['data_nascimento']);
        ?>
        <section class="admin-panel mb-3">
            <div class="admin-panel-header">
                <h2>Sinais vitais</h2>
                <?= badgeNews2($news2) ?>
            </div>
            <div class="admin-panel-body">
                <div class="sinais-vitais">
                    <div class="sinal-vital">
                        <span>PA</span>
                        <strong><?= esc($atual['pressao_arterial']) ?></strong> <small>mmHg</small>
                    </div>
                    <div class="sinal-vital">
                        <span>Temp.</span>
                        <strong><?= esc(formatValor($atual['temperatura'], 1)) ?></strong> <small>°C</small>
                    </div>
                    <div class="sinal-vital">
                        <span>FC</span>
                        <strong><?= esc($atual['frequencia_cardiaca']) ?></strong> <small>bpm</small>
                    </div>
                    <div class="sinal-vital">
                        <span>FR</span>
                        <strong><?= esc($atual['frequencia_respiratoria']) ?></strong> <small>irpm</small>
                    </div>
                    <div class="sinal-vital">
                        <span>SpO₂</span>
                        <strong><?= esc($atual['saturacao']) ?></strong> <small>%<?= $atual['oxigenio_suplementar'] == 1 ? ' com O₂' : '' ?></small>
                    </div>
                    <div class="sinal-vital">
                        <span>Consciência</span>
                        <strong><?= esc($atual['nivel_consciencia']) ?></strong> <small><?= esc(getDescricaoConsciencia($atual['nivel_consciencia'])) ?></small>
                    </div>
                </div>
                <?php if ($atual['dor'] !== null || $atual['glicemia'] !== null || !empty($atendimento['peso'])): ?>
                    <p class="lista-separada mt-2 mb-0">
                        <?php if ($atual['dor'] !== null): ?><span>Dor <strong><?= esc($atual['dor']) ?>/10</strong></span><?php endif; ?>
                        <?php if ($atual['glicemia'] !== null): ?><span>Glicemia <strong><?= esc($atual['glicemia']) ?></strong> mg/dL</span><?php endif; ?>
                        <?php if (!empty($atendimento['peso'])): ?><span>Peso <strong><?= esc(formatValor($atendimento['peso'], 1)) ?></strong> kg</span><?php endif; ?>
                    </p>
                <?php endif; ?>
                <p class="texto-ajuda mt-2 mb-0">
                    <?php if (!empty($sinais)): ?>
                        Aferidos às <?= esc(formatHora($atual['data_hora'])) ?> por <?= esc($atual['usuario']) ?> ·
                    <?php else: ?>
                        Aferidos na triagem às <?= esc(formatHora($atendimento['data_hora_triagem'])) ?> ·
                    <?php endif; ?>
                    <a href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><?= esc((string) (count($sinais) + 1)) ?> aferição(ões)</a>
                </p>
            </div>
        </section>

        <section class="admin-panel mb-3">
            <div class="admin-panel-header">
                <h2>Triagem</h2>
                <span class="texto-ajuda"><?= esc($atendimento['enfermeiro']) ?> · <?= esc(formatHora($atendimento['data_hora_triagem'])) ?></span>
            </div>
            <div class="admin-panel-body">
                <dl class="lista-dados mb-0">
                    <dt>Queixa principal</dt>
                    <dd><?= esc($atendimento['queixa_principal']) ?></dd>

                    <dt>Observações da triagem</dt>
                    <dd><?= esc($atendimento['observacoes_iniciais'] ?: '—') ?></dd>

                    <?php if (!empty($atendimento['observacao_inicial'])): ?>
                        <dt>Observação da admissão</dt>
                        <dd><?= esc($atendimento['observacao_inicial']) ?></dd>
                    <?php endif; ?>

                    <dt>Tempos</dt>
                    <dd>
                        Chegada <?= esc(formatHora($atendimento['data_hora_chegada'])) ?> ·
                        classificação <?= esc(formatHora($atendimento['data_hora_triagem'])) ?> ·
                        médico <?= esc(formatHora($atendimento['data_hora_inicio_atendimento'])) ?>
                    </dd>
                </dl>
            </div>
        </section>

        <?php if (!empty($reclassificacoes)): ?>
            <section class="admin-panel mb-3">
                <div class="admin-panel-header">
                    <h2>Reclassificações <span class="text-muted fw-normal">(<?= esc((string) count($reclassificacoes)) ?>)</span></h2>
                </div>
                <div class="admin-panel-body linha-tempo">
                    <?php foreach ($reclassificacoes as $reclassificacao): ?>
                        <article>
                            <p class="linha-tempo-cabecalho"><strong><?= esc(formatDataHora($reclassificacao['data_hora'])) ?></strong> · <?= esc($reclassificacao['usuario']) ?> (<?= esc($reclassificacao['perfil']) ?>)</p>
                            <p class="mb-1">
                                <span class="legenda-risco texto-risco-<?= esc($reclassificacao['cor_anterior']) ?>"><?= esc($reclassificacao['classificacao_anterior']) ?></span>
                                <i class="fa fa-long-arrow-right mx-1 text-muted" aria-hidden="true"></i>
                                <span class="legenda-risco texto-risco-<?= esc($reclassificacao['cor_nova']) ?>"><?= esc($reclassificacao['classificacao_nova']) ?></span>
                            </p>
                            <p class="mb-0 small"><?= esc($reclassificacao['motivo']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Anexos <span class="text-muted fw-normal">(<?= esc((string) count($anexos)) ?>)</span></h2>
            </div>
            <div class="admin-panel-body">
                <?php if (!empty($anexos)): ?>
                    <ul class="list-unstyled lista-anexos mb-3">
                        <?php foreach ($anexos as $anexo): ?>
                            <li>
                                <i class="fa fa-file-o" aria-hidden="true"></i>
                                <div>
                                    <a href="<?= base_url('anexos/download/' . $anexo['id_anexo']) ?>"><?= esc($anexo['nome_original']) ?></a>
                                    <span class="texto-secundario"><?= esc(formatDataHora($anexo['data_hora'])) ?><?= !empty($anexo['exame']) ? ' · ' . esc($anexo['exame']) : '' ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?= form_open_multipart("anexos/store") ?>
                    <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">
                    <label for="anexo_atendimento" class="form-label">Anexar documento</label>
                    <div class="input-group input-group-sm">
                        <input type="file" class="form-control form-control-sm" id="anexo_atendimento" name="anexo" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Enviar</button>
                    </div>
                    <p class="texto-ajuda mt-1 mb-0">PDF, JPG ou PNG até 2 MB.</p>
                <?= form_close() ?>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
