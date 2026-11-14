<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<div class="row">
    <div class="col-12 col-xl-8">
        <?= form_open("pacientes/naoIdentificado") ?>

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Cadastro rápido</h2>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label for="sexo" class="form-label">Sexo aparente</label>
                            <select class="form-select <?= !empty($erros['sexo']) ? 'is-invalid' : '' ?>" id="sexo" name="sexo">
                                <option value="" <?= setValue("sexo", $dados) == "" ? "selected" : "" ?>>Não determinado</option>
                                <option value="F" <?= setValue("sexo", $dados) == "F" ? "selected" : "" ?>>Feminino</option>
                                <option value="M" <?= setValue("sexo", $dados) == "M" ? "selected" : "" ?>>Masculino</option>
                            </select>
                            <?= setMsgErrorCampo("sexo", $erros) ?>
                        </div>
                        <div class="col-12">
                            <label for="caracteristicas" class="form-label campo-obrigatorio">Características para reconhecimento</label>
                            <textarea class="form-control <?= !empty($erros['caracteristicas']) ? 'is-invalid' : '' ?>" id="caracteristicas" name="caracteristicas" rows="3" maxlength="255" required
                                placeholder="Idade aparente, roupas, tatuagens, quem trouxe"><?= setValue("caracteristicas", $dados) ?></textarea>
                            <?= setMsgErrorCampo("caracteristicas", $erros) ?>
                        </div>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('pacientes') ?>">Cancelar</a>
                    <button class="btn btn-brand ms-auto" type="submit">Cadastrar e ir para a admissão</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
