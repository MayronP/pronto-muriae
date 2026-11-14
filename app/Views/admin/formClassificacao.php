<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <?= form_open("admin/classificacoes/store") ?>

            <input type="hidden" name="id_classificacao" value="<?= setValue("id_classificacao", $dados) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <h2>Tempo-alvo</h2>
                    <span class="badge-risco <?= esc(getClasseRisco($dados['cor'])) ?>"><?= esc($dados['nome']) ?></span>
                </div>

                <div class="admin-panel-body">
                    <label for="tempo_alvo_minutos" class="form-label campo-obrigatorio">Espera máxima pelo médico</label>
                    <div class="input-group has-validation" style="max-width: 14rem;">
                        <input type="number" class="form-control <?= !empty($erros['tempo_alvo_minutos']) ? 'is-invalid' : '' ?>" id="tempo_alvo_minutos" name="tempo_alvo_minutos"
                            min="0" max="240" value="<?= setValue("tempo_alvo_minutos", $dados) ?>" required>
                        <span class="input-group-text">min</span>
                    </div>
                    <?= setMsgErrorCampo("tempo_alvo_minutos", $erros) ?>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('admin/classificacoes') ?>">Cancelar</a>
                    <button class="btn btn-brand ms-auto" type="submit">Gravar</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
