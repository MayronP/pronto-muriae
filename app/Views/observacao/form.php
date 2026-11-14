<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="row">
    <div class="col-12 col-xl-8">
        <?= form_open("observacao/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Encaminhar para observação</h2>
                        <p>Permanência no pronto atendimento até agora: <?= badgePermanencia($atendimento['data_hora_chegada']) ?> (limite de <?= PERMANENCIA_MAXIMA_HORAS ?> horas).</p>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-7">
                            <label for="leito_observacao" class="form-label campo-obrigatorio">Leito ou sala</label>
                            <input type="text" class="form-control <?= !empty($erros['leito_observacao']) ? 'is-invalid' : '' ?>" id="leito_observacao" name="leito_observacao"
                                placeholder="Ex.: Observação adulto - leito 3" value="<?= setValue("leito_observacao", $dados) ?>" maxlength="40" required>
                            <?= setMsgErrorCampo("leito_observacao", $erros) ?>
                        </div>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>">Voltar ao atendimento</a>
                    <button class="btn btn-brand ms-auto" type="submit">Encaminhar para observação</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
