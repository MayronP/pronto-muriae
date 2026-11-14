<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente nao-imprimir">
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes/historico/' . $atendimento['id_paciente']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Histórico do paciente</a>
    <button class="btn btn-outline-secondary btn-sm" type="button" data-imprimir><i class="fa fa-print me-1" aria-hidden="true"></i>Imprimir prontuário</button>
    <?php if ($atendimento['status'] === 'encerrado'): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/sumario/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Sumário do atendimento</a>
    <?php endif; ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-print me-1" aria-hidden="true"></i>Documentos</a>
    <?php if (session()->get('usuarioPerfilId') == PERFIL_MEDICO && in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-brand btn-sm ms-md-auto" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-stethoscope me-1" aria-hidden="true"></i>Abrir atendimento</a>
    <?php endif; ?>
</div>

<?php $news2 = calculaNews2($atendimento, $atendimento['data_nascimento'], $atendimento['data_hora_triagem']); ?>

<section class="admin-panel">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="aba-resumo-tab" data-bs-toggle="tab" data-bs-target="#aba-resumo" type="button" role="tab" aria-controls="aba-resumo" aria-selected="true">Resumo</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-evolucoes-tab" data-bs-toggle="tab" data-bs-target="#aba-evolucoes" type="button" role="tab" aria-controls="aba-evolucoes" aria-selected="false">Evoluções<span class="contador"><?= esc((string) count($evolucoes)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-enfermagem-tab" data-bs-toggle="tab" data-bs-target="#aba-enfermagem" type="button" role="tab" aria-controls="aba-enfermagem" aria-selected="false">Enfermagem<span class="contador"><?= esc((string) (count($sinais) + count($evolucoesEnfermagem))) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-exames-tab" data-bs-toggle="tab" data-bs-target="#aba-exames" type="button" role="tab" aria-controls="aba-exames" aria-selected="false">Exames<span class="contador"><?= esc((string) count($exames)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-medicamentos-tab" data-bs-toggle="tab" data-bs-target="#aba-medicamentos" type="button" role="tab" aria-controls="aba-medicamentos" aria-selected="false">Medicamentos<span class="contador"><?= esc((string) count($medicamentos)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-protocolos-tab" data-bs-toggle="tab" data-bs-target="#aba-protocolos" type="button" role="tab" aria-controls="aba-protocolos" aria-selected="false">Protocolos<span class="contador"><?= esc((string) count($protocolos)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-documentos-tab" data-bs-toggle="tab" data-bs-target="#aba-documentos" type="button" role="tab" aria-controls="aba-documentos" aria-selected="false">Documentos<span class="contador"><?= esc((string) count($documentos)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-plantao-tab" data-bs-toggle="tab" data-bs-target="#aba-plantao" type="button" role="tab" aria-controls="aba-plantao" aria-selected="false">Plantão<span class="contador"><?= esc((string) count($passagens)) ?></span></button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aba-anexos-tab" data-bs-toggle="tab" data-bs-target="#aba-anexos" type="button" role="tab" aria-controls="aba-anexos" aria-selected="false">Anexos<span class="contador"><?= esc((string) count($anexos)) ?></span></button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- Resumo -->
        <div class="tab-pane fade show active" id="aba-resumo" role="tabpanel" aria-labelledby="aba-resumo-tab" tabindex="0">
            <div class="admin-panel-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-4">
                        <h3 class="secao-form">Tempos</h3>
                        <dl class="lista-dados mb-0">
                            <dt>Chegada</dt>
                            <dd><?= esc(formatDataHora($atendimento['data_hora_chegada'])) ?> <span class="texto-secundario">admissão por <?= esc($atendimento['usuario_admissao']) ?></span></dd>

                            <dt>Classificação de risco</dt>
                            <dd>
                                <?= esc(formatDataHora($atendimento['data_hora_triagem'])) ?>
                                <?php if (!empty($atendimento['data_hora_triagem'])): ?>
                                    <span class="texto-secundario"><?= esc(tempoDecorrido($atendimento['data_hora_chegada'], $atendimento['data_hora_triagem'])) ?> após a chegada</span>
                                <?php endif; ?>
                            </dd>

                            <dt>Atendimento médico</dt>
                            <dd>
                                <?= esc(formatDataHora($atendimento['data_hora_inicio_atendimento'])) ?>
                            </dd>

                            <?php if (!empty($atendimento['data_hora_inicio_observacao'])): ?>
                                <dt>Observação</dt>
                                <dd><?= esc($atendimento['leito_observacao']) ?>, desde <?= esc(formatDataHora($atendimento['data_hora_inicio_observacao'])) ?></dd>
                            <?php endif; ?>

                            <dt>Permanência</dt>
                            <dd>
                                <?php if ($atendimento['status'] === 'encerrado'): ?>
                                    <?= esc(tempoDecorrido($atendimento['data_hora_chegada'], $atendimento['data_hora_encerramento'])) ?>
                                    <span class="texto-secundario">até <?= esc(formatDataHora($atendimento['data_hora_encerramento'])) ?></span>
                                <?php else: ?>
                                    <?= badgePermanencia($atendimento['data_hora_chegada']) ?>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>

                    <div class="col-12 col-lg-4">
                        <h3 class="secao-form d-flex justify-content-between align-items-center">Triagem <?= !empty($atendimento['id_triagem']) ? badgeNews2($news2) : '' ?></h3>
                        <?php if (!empty($atendimento['id_triagem'])): ?>
                            <dl class="lista-dados mb-0">
                                <dt>Queixa principal</dt>
                                <dd><?= esc($atendimento['queixa_principal']) ?></dd>

                                <dt>Observações</dt>
                                <dd><?= esc($atendimento['observacoes_iniciais'] ?: '—') ?></dd>

                                <dt>Sinais vitais</dt>
                                <dd>
                                    PA <?= esc($atendimento['pressao_arterial']) ?> mmHg ·
                                    <?= esc(formatValor($atendimento['temperatura'], 1)) ?> °C ·
                                    FC <?= esc($atendimento['frequencia_cardiaca']) ?> bpm ·
                                    FR <?= esc($atendimento['frequencia_respiratoria']) ?> irpm ·
                                    SpO₂ <?= esc($atendimento['saturacao']) ?>%<?= $atendimento['oxigenio_suplementar'] == 1 ? ' com O₂' : '' ?>
                                    <?php if (!empty($atendimento['nivel_consciencia'])): ?>
                                        · <?= esc(getDescricaoConsciencia($atendimento['nivel_consciencia'])) ?>
                                    <?php endif; ?>
                                </dd>

                                <dt>Responsável</dt>
                                <dd><?= esc($atendimento['enfermeiro']) ?>, <?= esc(formatDataHora($atendimento['data_hora_triagem'])) ?></dd>
                            </dl>
                        <?php else: ?>
                            <p class="texto-ajuda mb-0">Triagem ainda não realizada.</p>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-lg-4">
                        <h3 class="secao-form">Desfecho</h3>
                        <?php if ($atendimento['status'] === 'encerrado'): ?>
                            <dl class="lista-dados mb-0">
                                <dt>Desfecho</dt>
                                <dd><span class="status-badge <?= esc(getClasseDesfecho($atendimento['desfecho'])) ?>"><?= esc(getDescricaoStatus($atendimento['desfecho'])) ?></span></dd>

                                <?php if ($atendimento['desfecho'] !== 'evasao'): ?>
                                    <dt>Médico responsável</dt>
                                    <dd><?= esc($atendimento['medico'] ?? '—') ?></dd>
                                <?php endif; ?>

                                <?php if (!empty($atendimento['cid_principal'])): ?>
                                    <dt>CID-10 principal</dt>
                                    <dd><strong><?= esc(formataCid($atendimento['cid_principal'])) ?></strong> <?= esc($atendimento['cid_descricao']) ?></dd>
                                <?php endif; ?>

                                <?php if ($atendimento['desfecho'] === 'obito'): ?>
                                    <dt>Óbito</dt>
                                    <dd><?= esc(formatDataHora($atendimento['data_hora_obito'])) ?><?= !empty($atendimento['declaracao_obito']) ? ' · DO nº ' . esc($atendimento['declaracao_obito']) : ' · declaração emitida por outro serviço' ?></dd>
                                <?php elseif ($atendimento['desfecho'] === 'alta_pedido'): ?>
                                    <dt>Alta a pedido</dt>
                                    <dd>Termo de responsabilidade assinado</dd>
                                <?php endif; ?>

                                <dt>Encerramento</dt>
                                <dd><?= esc(formatDataHora($atendimento['data_hora_encerramento'])) ?></dd>

                                <dt><?= $atendimento['desfecho'] === 'evasao' ? 'Como a saída foi constatada' : 'Resumo final' ?></dt>
                                <dd><?= nl2br(esc($atendimento['resumo_final'])) ?></dd>

                                <dt>Observações finais</dt>
                                <dd><?= esc($atendimento['observacoes_finais'] ?: '—') ?></dd>
                            </dl>
                        <?php else: ?>
                            <p class="texto-ajuda mb-0">Atendimento ainda não encerrado.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($reclassificacoes)): ?>
                    <h3 class="secao-form">Reclassificações</h3>
                    <div class="linha-tempo">
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
                <?php endif; ?>

                <?php if (!empty($acompanhantes) || !empty($notificacoes)): ?>
                    <div class="row g-4 mt-0">
                        <?php if (!empty($acompanhantes)): ?>
                            <div class="col-12 col-lg-6">
                                <h3 class="secao-form">Acompanhantes</h3>
                                <ul class="list-unstyled lista-anexos mb-0">
                                    <?php foreach ($acompanhantes as $acompanhante): ?>
                                        <li>
                                            <i class="fa fa-user-o" aria-hidden="true"></i>
                                            <div>
                                                <?= esc($acompanhante['nome']) ?><?= !empty($acompanhante['parentesco']) ? ' (' . esc($acompanhante['parentesco']) . ')' : '' ?>
                                                <span class="texto-secundario">entrada <?= esc(formatDataHora($acompanhante['data_hora_entrada'])) ?><?= !empty($acompanhante['data_hora_saida']) ? ' · saída ' . esc(formatDataHora($acompanhante['data_hora_saida'])) : ' · presente' ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($notificacoes)): ?>
                            <div class="col-12 col-lg-6">
                                <h3 class="secao-form">Notificações compulsórias</h3>
                                <ul class="list-unstyled lista-anexos mb-0">
                                    <?php foreach ($notificacoes as $notificacao): ?>
                                        <li>
                                            <i class="fa fa-bell-o" aria-hidden="true"></i>
                                            <div>
                                                <?= esc($notificacao['agravo']) ?> <span class="status-badge <?= esc(getClasseStatus($notificacao['situacao'])) ?> ms-1"><?= esc(getDescricaoStatus($notificacao['situacao'])) ?></span>
                                                <span class="texto-secundario">registrada em <?= esc(formatDataHora($notificacao['data_hora'])) ?> por <?= esc($notificacao['usuario']) ?><?= !empty($notificacao['numero_sinan']) ? ' · Sinan nº ' . esc($notificacao['numero_sinan']) : '' ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Enfermagem -->
        <div class="tab-pane fade" id="aba-enfermagem" role="tabpanel" aria-labelledby="aba-enfermagem-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Enfermagem</h3>
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
                            <?php $news2Sinal = calculaNews2(array_merge($sinal, ['gestante' => $atendimento['gestante']]), $atendimento['data_nascimento'], $sinal['data_hora']); ?>
                            <tr>
                                <td class="text-nowrap fw-semibold"><?= esc(formatDataHora($sinal['data_hora'])) ?></td>
                                <td class="text-nowrap"><?= esc($sinal['pressao_arterial']) ?></td>
                                <td><?= esc($sinal['frequencia_cardiaca']) ?></td>
                                <td><?= esc($sinal['frequencia_respiratoria']) ?></td>
                                <td><?= esc(formatValor($sinal['temperatura'], 1)) ?></td>
                                <td class="text-nowrap"><?= esc($sinal['saturacao']) ?>%<?= $sinal['oxigenio_suplementar'] == 1 ? ' O₂' : '' ?></td>
                                <td><?= esc($sinal['nivel_consciencia']) ?></td>
                                <td><?= $sinal['dor'] !== null ? esc($sinal['dor']) . '/10' : '—' ?></td>
                                <td><?= $sinal['glicemia'] !== null ? esc($sinal['glicemia']) : '—' ?></td>
                                <td><?= $news2Sinal['escore'] !== null ? badgeNews2($news2Sinal) : '—' ?></td>
                                <td><?= esc($sinal['usuario']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!empty($atendimento['id_triagem'])): ?>
                            <tr class="linha-triagem">
                                <td class="text-nowrap fw-semibold"><?= esc(formatDataHora($atendimento['data_hora_triagem'])) ?> <span class="tag tag-neutra ms-1">Triagem</span></td>
                                <td class="text-nowrap"><?= esc($atendimento['pressao_arterial']) ?></td>
                                <td><?= esc($atendimento['frequencia_cardiaca']) ?></td>
                                <td><?= esc($atendimento['frequencia_respiratoria']) ?></td>
                                <td><?= esc(formatValor($atendimento['temperatura'], 1)) ?></td>
                                <td class="text-nowrap"><?= esc($atendimento['saturacao']) ?>%<?= $atendimento['oxigenio_suplementar'] == 1 ? ' O₂' : '' ?></td>
                                <td><?= esc($atendimento['nivel_consciencia'] ?? '—') ?></td>
                                <td><?= $atendimento['dor'] !== null ? esc($atendimento['dor']) . '/10' : '—' ?></td>
                                <td><?= $atendimento['glicemia'] !== null ? esc($atendimento['glicemia']) : '—' ?></td>
                                <td><?= $news2['escore'] !== null ? badgeNews2($news2) : '—' ?></td>
                                <td><?= esc($atendimento['enfermeiro']) ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($evolucoesEnfermagem)): ?>
                <div class="admin-panel-body linha-tempo border-top">
                    <?php foreach ($evolucoesEnfermagem as $registro): ?>
                        <article>
                            <p class="linha-tempo-cabecalho"><strong><?= esc(formatDataHora($registro['data_hora'])) ?></strong> · <?= esc($registro['usuario']) ?><?= !empty($registro['registro_profissional']) ? ' · ' . esc($registro['registro_profissional']) : '' ?></p>
                            <dl class="lista-dados mb-0">
                                <?php foreach (['avaliacao' => 'Avaliação', 'diagnostico' => 'Diagnóstico de enfermagem', 'planejamento' => 'Planejamento', 'implementacao' => 'Cuidados realizados', 'evolucao' => 'Evolução'] as $campo => $rotulo): ?>
                                    <?php if (!empty($registro[$campo])): ?>
                                        <dt><?= esc($rotulo) ?></dt>
                                        <dd><?= nl2br(esc($registro[$campo])) ?></dd>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </dl>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0 border-top"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum registro de enfermagem.</p>
            <?php endif; ?>
        </div>

        <!-- Evoluções -->
        <div class="tab-pane fade" id="aba-evolucoes" role="tabpanel" aria-labelledby="aba-evolucoes-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Evoluções</h3>
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
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma evolução registrada.</p>
            <?php endif; ?>
        </div>

        <!-- Exames -->
        <div class="tab-pane fade" id="aba-exames" role="tabpanel" aria-labelledby="aba-exames-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Exames</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Solicitação</th>
                            <th>Situação</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($exames)): ?>
                            <?php foreach ($exames as $exame): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($exame['descricao']) ?></td>
                                    <td class="text-nowrap">
                                        <?= esc(formatDataHora($exame['data_hora_solicitacao'])) ?>
                                        <span class="texto-secundario"><?= esc($exame['solicitante']) ?></span>
                                    </td>
                                    <td><span class="status-badge <?= esc(getClasseStatus($exame['status'])) ?>"><?= esc(getDescricaoStatus($exame['status'])) ?></span></td>
                                    <td><?= !empty($exame['resultado']) ? nl2br(esc($exame['resultado'])) : '<span class="text-muted">Aguardando</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum exame solicitado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medicamentos -->
        <div class="tab-pane fade" id="aba-medicamentos" role="tabpanel" aria-labelledby="aba-medicamentos-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Medicamentos</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Posologia</th>
                            <th>Prescritor</th>
                            <th>Doses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($medicamentos)): ?>
                            <?php foreach ($medicamentos as $item): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= esc($item['medicamento']) ?></span>
                                        <?php if (!empty($item['apresentacao'])): ?>
                                            <span class="texto-secundario"><?= esc($item['apresentacao']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['situacao'] === 'suspenso'): ?>
                                            <span class="status-badge status-suspenso mt-1">Suspenso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item['dose']) ?> · <?= esc($item['via']) ?> · <?= esc($item['frequencia']) ?></td>
                                    <td><?= esc($item['prescritor']) ?></td>
                                    <td>
                                        <?php if (!empty($doses[$item['id_item_prescricao']])): ?>
                                            <ul class="lista-doses">
                                                <?php foreach ($doses[$item['id_item_prescricao']] as $dose): ?>
                                                    <li>
                                                        <span class="text-nowrap"><?= esc(formatHora($dose['data_hora_prevista'])) ?></span>
                                                        <?php if ($atendimento['status'] === 'encerrado' && $dose['situacao'] === 'pendente'): ?>
                                                            <span class="status-badge status-indefinido">Não checada</span>
                                                        <?php else: ?>
                                                            <?= badgeDose($dose) ?>
                                                        <?php endif; ?>
                                                        <?php if (!empty($dose['data_hora'])): ?>
                                                            <span class="texto-ajuda"><?= esc($dose['checado_por']) ?> às <?= esc(formatHora($dose['data_hora'])) ?><?= !empty($dose['motivo']) ? ' · ' . esc($dose['motivo']) : '' ?></span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted">Sem registro de administração</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum medicamento registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Protocolos gerenciados -->
        <div class="tab-pane fade" id="aba-protocolos" role="tabpanel" aria-labelledby="aba-protocolos-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Protocolos gerenciados</h3>
            <?php if (!empty($protocolos)): ?>
                <div class="admin-panel-body protocolos-prontuario">
                    <?= $this->include('templates/protocolos_paciente') ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum protocolo aberto.</p>
            <?php endif; ?>
        </div>

        <!-- Documentos -->
        <div class="tab-pane fade" id="aba-documentos" role="tabpanel" aria-labelledby="aba-documentos-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Documentos emitidos</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Emitido por</th>
                            <th>Data</th>
                            <th class="text-end nao-imprimir">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documentos)): ?>
                            <?php foreach ($documentos as $documento): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= esc(getTiposDocumento()[$documento['tipo']]) ?></span>
                                        <?php if ($documento['tipo'] === 'atestado'): ?>
                                            <span class="texto-secundario"><?= esc($documento['dias_afastamento']) ?> dia(s)</span>
                                        <?php elseif ($documento['tipo'] === 'encaminhamento'): ?>
                                            <span class="texto-secundario"><?= esc($documento['destino']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($documento['usuario']) ?></td>
                                    <td class="text-nowrap"><?= esc(formatDataHora($documento['data_hora'])) ?></td>
                                    <td class="acoes-tabela nao-imprimir">
                                        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/imprimir/' . $documento['id_documento']) ?>">Imprimir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum documento emitido.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Passagens de plantão -->
        <div class="tab-pane fade" id="aba-plantao" role="tabpanel" aria-labelledby="aba-plantao-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Passagens de plantão</h3>
            <?php if (!empty($passagens)): ?>
                <div class="admin-panel-body linha-tempo">
                    <?php foreach ($passagens as $passagem): ?>
                        <article>
                            <p class="linha-tempo-cabecalho">
                                <strong><?= esc(formatDataHora($passagem['data_hora'])) ?></strong> · <?= esc($passagem['autor']) ?> (<?= esc($passagem['perfil_autor']) ?>)
                                <?= !empty($passagem['recebedor']) ? '· recebida por ' . esc($passagem['recebedor']) . ' às ' . esc(formatHora($passagem['data_hora_recebimento'])) : '· aguardando recebimento' ?>
                            </p>
                            <dl class="lista-dados mb-0">
                                <dt>Situação</dt>
                                <dd><?= nl2br(esc($passagem['situacao'])) ?></dd>
                                <?php if (!empty($passagem['historico'])): ?>
                                    <dt>Breve histórico</dt>
                                    <dd><?= nl2br(esc($passagem['historico'])) ?></dd>
                                <?php endif; ?>
                                <dt>Avaliação</dt>
                                <dd><?= nl2br(esc($passagem['avaliacao'])) ?></dd>
                                <dt>Recomendação</dt>
                                <dd><?= nl2br(esc($passagem['recomendacao'])) ?></dd>
                            </dl>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma passagem de plantão registrada.</p>
            <?php endif; ?>
        </div>

        <!-- Anexos -->
        <div class="tab-pane fade" id="aba-anexos" role="tabpanel" aria-labelledby="aba-anexos-tab" tabindex="0">
            <h3 class="secao-form titulo-impressao">Anexos</h3>
            <?php if (!empty($anexos)): ?>
                <div class="admin-panel-body">
                    <ul class="list-unstyled lista-anexos mb-0">
                        <?php foreach ($anexos as $anexo): ?>
                            <li>
                                <i class="fa fa-file-o" aria-hidden="true"></i>
                                <div>
                                    <a href="<?= base_url('anexos/download/' . $anexo['id_anexo']) ?>"><?= esc($anexo['nome_original']) ?></a>
                                    <span class="texto-secundario"><?= esc(formatDataHora($anexo['data_hora'])) ?> · <?= esc($anexo['usuario']) ?><?= !empty($anexo['exame']) ? ' · ' . esc($anexo['exame']) : '' ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum arquivo anexado.</p>
            <?php endif; ?>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
