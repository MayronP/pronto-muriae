<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <?php if (session()->get('usuarioPerfilId') == PERFIL_MEDICO && in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
    <?php elseif (session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM && in_array($atendimento['status'], ['aguardando_medico', 'em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Enfermagem</a>
    <?php else: ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes/historico/' . $atendimento['id_paciente']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Histórico do paciente</a>
    <?php endif; ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Prontuário</a>
    <?php if ($atendimento['status'] === 'encerrado'): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/sumario/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-print me-1" aria-hidden="true"></i>Sumário do atendimento</a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-5">
        <?= form_open("documentos/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <h2>Emitir documento</h2>
                </div>

                <div class="admin-panel-body">
                    <div class="mb-3">
                        <label for="tipo" class="form-label campo-obrigatorio">Documento</label>
                        <select class="form-select <?= !empty($erros['tipo']) ? 'is-invalid' : '' ?>" id="tipo" name="tipo" required>
                            <?php foreach ($tipos as $valor => $descricao): ?>
                                <option value="<?= esc($valor) ?>" <?= setValue("tipo", $dados) == $valor ? "selected" : "" ?>><?= esc($descricao) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= setMsgErrorCampo("tipo", $erros) ?>
                    </div>

                    <?php if (isset($tipos['receita'])): ?>
                        <div data-mostra-quando="tipo=receita">
                            <label for="receita" class="form-label campo-obrigatorio">Medicamentos e orientações</label>
                            <textarea class="form-control <?= !empty($erros['receita']) ? 'is-invalid' : '' ?>" id="receita" name="receita" rows="7"
                                placeholder="Ex.: 1. Dipirona 500 mg, 1 comprimido de 6/6 h, se dor ou febre, por até 3 dias."><?= setValue("receita", $dados) ?></textarea>
                            <?= setMsgErrorCampo("receita", $erros) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($tipos['atestado'])): ?>
                        <div data-mostra-quando="tipo=atestado">
                            <div class="row g-3">
                                <div class="col-12 col-sm-5">
                                    <label for="dias_afastamento" class="form-label campo-obrigatorio">Dias de afastamento</label>
                                    <input type="number" class="form-control <?= !empty($erros['dias_afastamento']) ? 'is-invalid' : '' ?>" id="dias_afastamento" name="dias_afastamento"
                                        min="1" max="60" value="<?= setValue("dias_afastamento", $dados) ?>">
                                    <?= setMsgErrorCampo("dias_afastamento", $erros) ?>
                                </div>
                                <div class="col-12 col-sm-7">
                                    <label for="cid" class="form-label">CID-10 (opcional)</label>
                                    <input type="text" class="form-control <?= !empty($erros['cid']) ? 'is-invalid' : '' ?>" id="cid" name="cid" list="lista_cid_atestado"
                                        value="<?= setValue("cid", $dados) ?>" placeholder="Código ou doença" autocomplete="off"
                                        data-busca-cid="<?= base_url('cid10/buscar') ?>" data-cid-descricao="#cid_atestado_descricao">
                                    <datalist id="lista_cid_atestado"></datalist>
                                    <?= setMsgErrorCampo("cid", $erros) ?>
                                </div>
                            </div>
                            <p class="texto-ajuda mt-1" id="cid_atestado_descricao"><?= !empty($cid) ? esc(formataCid($cid['codigo']) . ' — ' . $cid['descricao']) : '' ?></p>
                            <div class="form-check mb-3">
                                <input class="form-check-input <?= !empty($erros['autoriza_cid']) ? 'is-invalid' : '' ?>" type="checkbox" id="autoriza_cid" name="autoriza_cid" value="1"
                                    <?= setValue("autoriza_cid", $dados) == "1" ? "checked" : "" ?>>
                                <label class="form-check-label" for="autoriza_cid">O paciente autorizou incluir o CID no atestado.</label>
                                <?= setMsgErrorCampo("autoriza_cid", $erros) ?>
                            </div>
                            <label for="observacao_atestado" class="form-label">Observação</label>
                            <textarea class="form-control" id="observacao_atestado" name="observacao_atestado" rows="2"><?= setValue("observacao_atestado", $dados) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div data-mostra-quando="tipo=comparecimento">
                        <label for="observacao_comparecimento" class="form-label">Observação</label>
                        <textarea class="form-control" id="observacao_comparecimento" name="observacao_comparecimento" rows="2"
                            placeholder="Ex.: esteve acompanhado da mãe, Joana Souza."><?= setValue("observacao_comparecimento", $dados) ?></textarea>
                    </div>

                    <?php if (isset($tipos['encaminhamento'])): ?>
                        <div data-mostra-quando="tipo=encaminhamento">
                            <div class="mb-3">
                                <label for="destino" class="form-label campo-obrigatorio">Encaminhado para</label>
                                <input type="text" class="form-control <?= !empty($erros['destino']) ? 'is-invalid' : '' ?>" id="destino" name="destino"
                                    value="<?= setValue("destino", $dados) ?>" maxlength="150" placeholder="Ex.: Ambulatório de cardiologia">
                                <?= setMsgErrorCampo("destino", $erros) ?>
                            </div>
                            <label for="motivo_encaminhamento" class="form-label campo-obrigatorio">Motivo e resumo clínico</label>
                            <textarea class="form-control <?= !empty($erros['motivo_encaminhamento']) ? 'is-invalid' : '' ?>" id="motivo_encaminhamento" name="motivo_encaminhamento" rows="5"><?= setValue("motivo_encaminhamento", $dados) ?></textarea>
                            <?= setMsgErrorCampo("motivo_encaminhamento", $erros) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="admin-panel-footer">
                    <span class="texto-ajuda me-auto">Emitido por <?= esc(session()->get('usuarioNome')) ?></span>
                    <button class="btn btn-brand" type="submit">Emitir e imprimir</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>

    <div class="col-12 col-xl-7">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Documentos emitidos <span class="text-muted fw-normal">(<?= esc((string) count($documentos)) ?>)</span></h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Emitido por</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documentos)): ?>
                            <?php foreach ($documentos as $documento): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= esc(getTiposDocumento()[$documento['tipo']]) ?></span>
                                        <?php if ($documento['tipo'] === 'atestado'): ?>
                                            <span class="texto-secundario"><?= esc($documento['dias_afastamento']) ?> dia(s)<?= !empty($documento['cid']) ? ' · CID ' . esc(formataCid($documento['cid'])) : '' ?></span>
                                        <?php elseif ($documento['tipo'] === 'encaminhamento'): ?>
                                            <span class="texto-secundario"><?= esc($documento['destino']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($documento['usuario']) ?></td>
                                    <td class="text-nowrap"><?= esc(formatDataHora($documento['data_hora'])) ?></td>
                                    <td class="acoes-tabela">
                                        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('documentos/imprimir/' . $documento['id_documento']) ?>"><i class="fa fa-print me-1" aria-hidden="true"></i>Imprimir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum documento emitido neste atendimento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
