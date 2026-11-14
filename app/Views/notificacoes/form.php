<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <?php if (session()->get('usuarioPerfilId') == PERFIL_MEDICO && in_array($atendimento['status'], ['em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
    <?php elseif (session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM && in_array($atendimento['status'], ['aguardando_medico', 'em_atendimento', 'em_observacao'])): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Enfermagem</a>
    <?php else: ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('fila') ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Fila</a>
    <?php endif; ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Prontuário</a>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-5">
        <?= form_open("notificacoes/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Suspeita de notificação compulsória</h2>
                    </div>
                </div>
                <div class="admin-panel-body">
                    <div class="mb-3">
                        <label for="agravo" class="form-label campo-obrigatorio">Doença ou agravo</label>
                        <input type="text" class="form-control <?= !empty($erros['agravo']) ? 'is-invalid' : '' ?>" id="agravo" name="agravo"
                            value="<?= setValue("agravo", $dados) ?>" maxlength="150" required placeholder="Como consta na Lista Nacional de Notificação Compulsória">
                        <?= setMsgErrorCampo("agravo", $erros) ?>
                    </div>
                    <label class="form-label campo-obrigatorio">Prazo da notificação</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="tipo_imediata" name="tipo" value="imediata" <?= setValue("tipo", $dados) === "imediata" ? "checked" : "" ?> required>
                        <label class="form-check-label" for="tipo_imediata">Imediata: em até 24 horas</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="tipo_semanal" name="tipo" value="semanal" <?= setValue("tipo", $dados) === "semanal" ? "checked" : "" ?>>
                        <label class="form-check-label" for="tipo_semanal">Semanal: em até 7 dias</label>
                    </div>
                    <?= setMsgErrorCampo("tipo", $erros) ?>
                </div>
                <div class="admin-panel-footer">
                    <button class="btn btn-brand ms-auto" type="submit">Registrar suspeita</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>

    <div class="col-12 col-xl-7">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Notificações do atendimento <span class="text-muted fw-normal">(<?= esc((string) count($notificacoes)) ?>)</span></h2>
            </div>
            <?= $this->include('notificacoes/tabela') ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
