<?php foreach ($protocolos as $protocolo): ?>
    <?php
        $marco  = marcoProtocolo($protocolo);
        $aberto = $protocolo['situacao'] === 'aberto';
    ?>
    <section class="admin-panel mb-3">
        <div class="admin-panel-header">
            <div>
                <h2>Protocolo <?= esc($protocolo['protocolo']) ?></h2>
                <p>
                    Aberto em <?= esc(formatDataHora($protocolo['data_hora_abertura'])) ?> por <?= esc($protocolo['usuario']) ?> ·
                    metas contadas da <?= $protocolo['marco_inicial'] === 'chegada' ? 'chegada do paciente' : 'abertura do protocolo' ?> (<?= esc(formatHora($marco)) ?>)
                </p>
            </div>
            <span class="status-badge <?= esc(getClasseStatus($protocolo['situacao'])) ?>"><?= esc(getDescricaoStatus($protocolo['situacao'])) ?></span>
        </div>

        <?php if (!empty($protocolo['janela_minutos']) && !empty($protocolo['inicio_sintomas'])): ?>
            <?php $limiteJanela = date("Y-m-d H:i:s", strtotime($protocolo['inicio_sintomas']) + $protocolo['janela_minutos'] * 60); ?>
            <div class="admin-panel-filtros">
                Início dos sintomas: <strong><?= esc(formatDataHora($protocolo['inicio_sintomas'])) ?></strong> ·
                janela terapêutica até <strong><?= esc(formatDataHora($limiteJanela)) ?></strong>
                <?php if ($aberto): ?>
                    <?php if (strtotime($limiteJanela) > time()): ?>
                        <span class="texto-ok">· restam <?= esc(formataMinutos(minutosEntre(date("Y-m-d H:i:s"), $limiteJanela))) ?></span>
                    <?php else: ?>
                        <span class="texto-alerta">· janela encerrada</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>Meta</th>
                        <th>Realizada</th>
                        <th>Situação</th>
                        <?php if ($podeRegistrar && $aberto): ?>
                            <th class="text-end">Registrar</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etapas[$protocolo['id_protocolo']] ?? [] as $etapa): ?>
                        <?php
                            $registro = $registros[$protocolo['id_protocolo_atendimento']][$etapa['id_etapa']] ?? null;
                            $prazo    = date("Y-m-d H:i:s", strtotime($marco) + $etapa['meta_minutos'] * 60);
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($etapa['descricao']) ?></td>
                            <td class="text-nowrap">
                                até <?= esc($etapa['meta_minutos']) ?> min
                                <span class="texto-secundario">prazo <?= esc(formatHora($prazo)) ?></span>
                            </td>
                            <td class="text-nowrap">
                                <?php if ($registro): ?>
                                    <?= esc(formatHora($registro['data_hora'])) ?> · <?= esc(formataMinutos(minutosEntre($marco, $registro['data_hora']))) ?>
                                    <span class="texto-secundario"><?= esc($registro['usuario']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($registro): ?>
                                    <?php if (minutosEntre($marco, $registro['data_hora']) <= $etapa['meta_minutos']): ?>
                                        <span class="status-badge status-concluido">Dentro da meta</span>
                                    <?php else: ?>
                                        <span class="status-badge status-suspenso">Acima da meta</span>
                                    <?php endif; ?>
                                <?php elseif (!$aberto): ?>
                                    <span class="status-badge status-indefinido">Não registrada</span>
                                <?php elseif (strtotime($prazo) < time()): ?>
                                    <span class="status-badge status-suspenso">Prazo vencido</span>
                                <?php else: ?>
                                    <span class="status-badge status-aguardando">Faltam <?= esc(formataMinutos(minutosEntre(date("Y-m-d H:i:s"), $prazo))) ?></span>
                                <?php endif; ?>
                            </td>
                            <?php if ($podeRegistrar && $aberto): ?>
                                <td class="acoes-tabela">
                                    <?php if (!$registro): ?>
                                        <?= form_open("protocolos/registrar", ["class" => "form-etapa"]) ?>
                                            <input type="hidden" name="id_protocolo_atendimento" value="<?= esc($protocolo['id_protocolo_atendimento']) ?>">
                                            <input type="hidden" name="id_etapa" value="<?= esc($etapa['id_etapa']) ?>">
                                            <label class="visually-hidden" for="etapa_<?= esc($protocolo['id_protocolo_atendimento'] . '_' . $etapa['id_etapa']) ?>">Horário da etapa</label>
                                            <input type="datetime-local" class="form-control form-control-sm" id="etapa_<?= esc($protocolo['id_protocolo_atendimento'] . '_' . $etapa['id_etapa']) ?>" name="data_hora" value="<?= date("Y-m-d\TH:i") ?>" required>
                                            <button class="btn btn-outline-brand btn-sm" type="submit">Registrar</button>
                                        <?= form_close() ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-panel-footer">
            <span class="texto-ajuda me-auto">
                <?php if (!$aberto): ?>
                    <?= esc(getDescricaoStatus($protocolo['situacao'])) ?> em <?= esc(formatDataHora($protocolo['data_hora_encerramento'])) ?><?= !empty($protocolo['observacao']) ? ': ' . esc($protocolo['observacao']) : '' ?>
                <?php endif; ?>
            </span>
            <?php if ($podeRegistrar && $aberto): ?>
                <?= form_open("protocolos/encerrar", ["class" => "d-inline"]) ?>
                    <input type="hidden" name="id_protocolo_atendimento" value="<?= esc($protocolo['id_protocolo_atendimento']) ?>">
                    <input type="hidden" name="situacao" value="concluido">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Concluir protocolo</button>
                <?= form_close() ?>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Descartar</button>
                    <div class="dropdown-menu dropdown-menu-end p-3 menu-formulario">
                        <?= form_open("protocolos/encerrar") ?>
                            <input type="hidden" name="id_protocolo_atendimento" value="<?= esc($protocolo['id_protocolo_atendimento']) ?>">
                            <input type="hidden" name="situacao" value="descartado">
                            <label class="form-label" for="descarte_<?= esc($protocolo['id_protocolo_atendimento']) ?>">Por que o protocolo foi descartado?</label>
                            <input type="text" class="form-control form-control-sm mb-2" id="descarte_<?= esc($protocolo['id_protocolo_atendimento']) ?>" name="observacao" maxlength="255" required placeholder="Ex.: diagnóstico não confirmado">
                            <button class="btn btn-outline-danger btn-sm w-100" type="submit">Descartar protocolo</button>
                        <?= form_close() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>
