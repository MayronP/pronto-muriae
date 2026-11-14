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

<?php if (!empty($disponiveis)): ?>
    <?= form_open("protocolos/abrir") ?>

        <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

        <section class="admin-panel mb-3">
            <div class="admin-panel-header">
                <div>
                    <h2>Abrir protocolo</h2>
                </div>
            </div>
            <div class="admin-panel-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="id_protocolo" class="form-label campo-obrigatorio">Protocolo</label>
                        <select class="form-select <?= !empty($erros['id_protocolo']) ? 'is-invalid' : '' ?>" id="id_protocolo" name="id_protocolo" required>
                            <option value="">Selecione</option>
                            <?php foreach ($disponiveis as $protocolo): ?>
                                <option value="<?= esc($protocolo['id_protocolo']) ?>" <?= setValue("id_protocolo", $dados) == $protocolo['id_protocolo'] ? "selected" : "" ?>><?= esc($protocolo['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= setMsgErrorCampo("id_protocolo", $erros) ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="inicio_sintomas" class="form-label">Início dos sintomas</label>
                        <input type="datetime-local" class="form-control <?= !empty($erros['inicio_sintomas']) ? 'is-invalid' : '' ?>" id="inicio_sintomas" name="inicio_sintomas"
                            value="<?= setValue("inicio_sintomas", $dados) ?>" max="<?= date("Y-m-d\TH:i") ?>">
                        <?= setMsgErrorCampo("inicio_sintomas", $erros) ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <button class="btn btn-brand" type="submit">Abrir protocolo</button>
                    </div>
                </div>
            </div>
        </section>

    <?= form_close() ?>
<?php endif; ?>

<?php if (!empty($protocolos)): ?>
    <?= $this->include('templates/protocolos_paciente') ?>
<?php else: ?>
    <section class="admin-panel">
        <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum protocolo aberto para este atendimento.</p>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>
