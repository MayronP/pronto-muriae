<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <?php if (session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM && in_array($atendimento['status'], ['aguardando_medico', 'em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Enfermagem</a>
    <?php elseif (session()->get('usuarioPerfilId') == PERFIL_MEDICO && in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
    <?php else: ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('fila') ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Fila</a>
    <?php endif; ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Prontuário</a>
</div>

<div class="row g-3">
    <?php if ($podeRegistrar): ?>
        <div class="col-12 col-xl-5">
            <?= form_open("acompanhantes/store") ?>

                <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

                <section class="admin-panel">
                    <div class="admin-panel-header">
                        <div>
                            <h2>Registrar acompanhante</h2>
                        </div>
                    </div>
                    <div class="admin-panel-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nome" class="form-label campo-obrigatorio">Nome</label>
                                <input type="text" class="form-control <?= !empty($erros['nome']) ? 'is-invalid' : '' ?>" id="nome" name="nome"
                                    value="<?= setValue("nome", $dados) ?>" maxlength="150" required>
                                <?= setMsgErrorCampo("nome", $erros) ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="parentesco" class="form-label">Parentesco</label>
                                <input type="text" class="form-control <?= !empty($erros['parentesco']) ? 'is-invalid' : '' ?>" id="parentesco" name="parentesco"
                                    value="<?= setValue("parentesco", $dados) ?>" maxlength="50" placeholder="Ex.: filha">
                                <?= setMsgErrorCampo("parentesco", $erros) ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="text" class="form-control <?= !empty($erros['documento']) ? 'is-invalid' : '' ?>" id="documento" name="documento"
                                    value="<?= setValue("documento", $dados) ?>" maxlength="30" placeholder="RG ou CPF">
                                <?= setMsgErrorCampo("documento", $erros) ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control <?= !empty($erros['telefone']) ? 'is-invalid' : '' ?>" id="telefone" name="telefone"
                                    value="<?= setValue("telefone", $dados) ?>" maxlength="20">
                                <?= setMsgErrorCampo("telefone", $erros) ?>
                            </div>
                        </div>
                    </div>
                    <div class="admin-panel-footer">
                        <button class="btn btn-brand ms-auto" type="submit">Registrar entrada</button>
                    </div>
                </section>

            <?= form_close() ?>
        </div>
    <?php endif; ?>

    <div class="col-12 <?= $podeRegistrar ? 'col-xl-7' : '' ?>">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Acompanhantes <span class="text-muted fw-normal">(<?= esc((string) count($acompanhantes)) ?>)</span></h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Contato</th>
                            <th>Entrada</th>
                            <th>Saída</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($acompanhantes)): ?>
                            <?php foreach ($acompanhantes as $acompanhante): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= esc($acompanhante['nome']) ?></span>
                                        <span class="texto-secundario"><?= esc($acompanhante['parentesco'] ?: 'Parentesco não informado') ?></span>
                                    </td>
                                    <td>
                                        <?= esc($acompanhante['telefone'] ?: '—') ?>
                                        <?php if (!empty($acompanhante['documento'])): ?>
                                            <span class="texto-secundario"><?= esc($acompanhante['documento']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap"><?= esc(formatDataHora($acompanhante['data_hora_entrada'])) ?></td>
                                    <td class="text-nowrap">
                                        <?php if (!empty($acompanhante['data_hora_saida'])): ?>
                                            <?= esc(formatDataHora($acompanhante['data_hora_saida'])) ?>
                                        <?php else: ?>
                                            <span class="status-badge status-andamento">Presente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="acoes-tabela">
                                        <?php if ($podeRegistrar && empty($acompanhante['data_hora_saida'])): ?>
                                            <?= form_open("acompanhantes/saida", ["class" => "d-inline"]) ?>
                                                <input type="hidden" name="id_acompanhante" value="<?= esc($acompanhante['id_acompanhante']) ?>">
                                                <button class="btn btn-outline-secondary btn-sm" type="submit">Registrar saída</button>
                                            <?= form_close() ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="estado-vazio" colspan="5"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum acompanhante registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
