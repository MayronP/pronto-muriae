<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Exames do atendimento <span class="text-muted fw-normal">(<?= esc((string) count($exames)) ?>)</span></h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Situação</th>
                            <th>Resultado</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($exames)): ?>
                            <?php foreach ($exames as $exame): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= esc($exame['descricao']) ?></span>
                                        <span class="texto-secundario"><?= esc(formatDataHora($exame['data_hora_solicitacao'])) ?> · <?= esc($exame['solicitante']) ?></span>
                                    </td>
                                    <td><span class="status-badge <?= esc(getClasseStatus($exame['status'])) ?>"><?= esc(getDescricaoStatus($exame['status'])) ?></span></td>
                                    <td>
                                        <?php if (!empty($exame['resultado'])): ?>
                                            <?= nl2br(esc($exame['resultado'])) ?>
                                            <span class="texto-secundario"><?= esc(formatDataHora($exame['data_hora_resultado'])) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Aguardando</span>
                                        <?php endif; ?>
                                        <?php foreach ($anexosExame[$exame['id_exame']] ?? [] as $anexo): ?>
                                            <a class="d-block small mt-1" href="<?= base_url('anexos/download/' . $anexo['id_anexo']) ?>"><i class="fa fa-paperclip me-1" aria-hidden="true"></i><?= esc($anexo['nome_original']) ?></a>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="acoes-tabela">
                                        <?php if ($exame['status'] === 'solicitado'): ?>
                                            <a class="btn btn-outline-brand btn-sm" href="<?= base_url('exames/index/' . $atendimento['id_atendimento'] . '?exame=' . $exame['id_exame']) ?>#registro-resultado">Registrar resultado</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="4"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum exame solicitado neste atendimento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="col-12 col-xl-4">
        <section class="admin-panel mb-3">
            <div class="admin-panel-header">
                <h2>Solicitar exame</h2>
            </div>
            <?= form_open("exames/solicitar") ?>
                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">
                <div class="admin-panel-body">
                    <label for="descricao" class="form-label campo-obrigatorio">Exame</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" value="<?= setValue("descricao") ?>" placeholder="Nome do exame" maxlength="180" required>
                    <p class="texto-ajuda mt-1 mb-0">Solicitado por <?= esc(session()->get('usuarioNome')) ?>.</p>
                </div>
                <div class="admin-panel-footer">
                    <button class="btn btn-brand ms-auto" type="submit">Solicitar</button>
                </div>
            <?= form_close() ?>
        </section>

        <section class="admin-panel" id="registro-resultado">
            <div class="admin-panel-header">
                <h2>Registrar resultado</h2>
            </div>
            <?= form_open_multipart("exames/resultado") ?>
                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">
                <div class="admin-panel-body">
                    <div class="mb-3">
                        <label for="exame_resultado" class="form-label campo-obrigatorio">Exame solicitado</label>
                        <select class="form-select" id="exame_resultado" name="id_exame" required>
                            <option value="">Selecione</option>
                            <?php foreach ($exames as $exame): ?>
                                <?php if ($exame['status'] === 'solicitado'): ?>
                                    <option value="<?= esc($exame['id_exame']) ?>" <?= $exame['id_exame'] == $exameSelecionado ? 'selected' : '' ?>><?= esc($exame['descricao']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="resultado" class="form-label campo-obrigatorio">Resultado</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="4" required><?= setValue("resultado") ?></textarea>
                    </div>
                    <div>
                        <label for="anexo" class="form-label">Anexo</label>
                        <input type="file" class="form-control form-control-sm" id="anexo" name="anexo" accept=".pdf,.jpg,.jpeg,.png">
                        <p class="texto-ajuda mt-1 mb-0">PDF, JPG ou PNG até 2 MB.</p>
                    </div>
                </div>
                <div class="admin-panel-footer">
                    <button class="btn btn-brand ms-auto" type="submit">Salvar resultado</button>
                </div>
            <?= form_close() ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
