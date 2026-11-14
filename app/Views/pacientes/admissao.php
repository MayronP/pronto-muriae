<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_paciente') ?>

<div class="row">
    <div class="col-12 col-xl-8">
        <?= form_open("admissao/store") ?>

            <input type="hidden" name="id_paciente" value="<?= esc($paciente['id_paciente']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <h2>Admissão no pronto atendimento</h2>
                    <span class="texto-ajuda">Atendimento <strong><?= esc($numeroAtendimento) ?></strong></span>
                </div>

                <div class="admin-panel-body">
                    <?= setMsgErrorCampo("numero", $erros) ?>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="data_chegada" class="form-label campo-obrigatorio">Data de chegada</label>
                            <input type="date" class="form-control <?= !empty($erros['data_chegada']) ? 'is-invalid' : '' ?>" id="data_chegada" name="data_chegada"
                                value="<?= setValue("data_chegada", $dados) ?>" max="<?= date("Y-m-d") ?>" required>
                            <?= setMsgErrorCampo("data_chegada", $erros) ?>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="hora_chegada" class="form-label campo-obrigatorio">Horário de chegada</label>
                            <input type="time" class="form-control <?= !empty($erros['hora_chegada']) ? 'is-invalid' : '' ?>" id="hora_chegada" name="hora_chegada"
                                value="<?= setValue("hora_chegada", $dados) ?>" required>
                            <?= setMsgErrorCampo("hora_chegada", $erros) ?>
                            <?= setMsgErrorCampo("data_hora_chegada", $erros) ?>
                        </div>
                        <div class="col-12">
                            <label for="observacao_inicial" class="form-label">Observação da chegada</label>
                            <textarea class="form-control" id="observacao_inicial" name="observacao_inicial" rows="3"
                                placeholder="Ex.: trazido pelo SAMU"><?= setValue("observacao_inicial", $dados) ?></textarea>
                        </div>
                    </div>

                    <h3 class="secao-form">Acompanhante</h3>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="acompanhante_nome" class="form-label">Nome</label>
                            <input type="text" class="form-control <?= !empty($erros['acompanhante_nome']) ? 'is-invalid' : '' ?>" id="acompanhante_nome" name="acompanhante_nome"
                                value="<?= setValue("acompanhante_nome", $dados) ?>" maxlength="150">
                            <?= setMsgErrorCampo("acompanhante_nome", $erros) ?>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="acompanhante_parentesco" class="form-label">Parentesco</label>
                            <input type="text" class="form-control <?= !empty($erros['acompanhante_parentesco']) ? 'is-invalid' : '' ?>" id="acompanhante_parentesco" name="acompanhante_parentesco"
                                value="<?= setValue("acompanhante_parentesco", $dados) ?>" maxlength="50">
                            <?= setMsgErrorCampo("acompanhante_parentesco", $erros) ?>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="acompanhante_telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control <?= !empty($erros['acompanhante_telefone']) ? 'is-invalid' : '' ?>" id="acompanhante_telefone" name="acompanhante_telefone"
                                value="<?= setValue("acompanhante_telefone", $dados) ?>" maxlength="20">
                            <?= setMsgErrorCampo("acompanhante_telefone", $erros) ?>
                        </div>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('pacientes') ?>">Cancelar</a>
                    <button class="btn btn-brand ms-auto" type="submit">Registrar admissão</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
