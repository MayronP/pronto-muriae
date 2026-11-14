<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="row">
    <div class="col-12 col-xl-8">
        <?= form_open("evasao/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Saída sem atendimento</h2>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <label for="resumo_final" class="form-label campo-obrigatorio">Como a saída foi constatada</label>
                    <textarea class="form-control <?= !empty($erros['resumo_final']) ? 'is-invalid' : '' ?>" id="resumo_final" name="resumo_final" rows="3" required
                        placeholder="Ex.: chamado no painel e não localizado na sala de espera"><?= setValue("resumo_final", $dados) ?></textarea>
                    <?= setMsgErrorCampo("resumo_final", $erros) ?>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url($atendimento['status'] === 'aguardando_triagem' && session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM ? 'triagem' : 'fila') ?>">Cancelar</a>
                    <button class="btn btn-danger ms-auto" type="submit">Registrar saída</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
