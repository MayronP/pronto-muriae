<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <?= form_open("admin/protocolos/store") ?>

            <input type="hidden" name="id_etapa" value="<?= setValue("id_etapa", $dados) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2><?= esc($dados['descricao']) ?></h2>
                        <p>Protocolo <?= esc($dados['protocolo']) ?></p>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <label for="meta_minutos" class="form-label campo-obrigatorio">Meta</label>
                    <div class="input-group has-validation" style="max-width: 14rem;">
                        <input type="number" class="form-control <?= !empty($erros['meta_minutos']) ? 'is-invalid' : '' ?>" id="meta_minutos" name="meta_minutos"
                            min="1" max="1440" value="<?= setValue("meta_minutos", $dados) ?>" required>
                        <span class="input-group-text">min</span>
                    </div>
                    <?= setMsgErrorCampo("meta_minutos", $erros) ?>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('admin/protocolos') ?>">Cancelar</a>
                    <button class="btn btn-brand ms-auto" type="submit">Gravar</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
