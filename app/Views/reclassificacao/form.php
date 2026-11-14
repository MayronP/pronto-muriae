<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<?php $referencia = $atendimento['data_hora_reclassificacao'] ?: $atendimento['data_hora_triagem']; ?>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <?= form_open("reclassificacao/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Nova classificação</h2>
                        <p>
                            Atual: <span class="legenda-risco texto-risco-<?= esc($atendimento['cor']) ?>"><?= esc($atendimento['classificacao']) ?></span>
                            desde <?= esc(formatHora($referencia)) ?>
                            <?php if ($atendimento['status'] === 'aguardando_medico'): ?>
                                · <?= badgeMeta($referencia, $atendimento['tempo_alvo_minutos']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <p class="texto-ajuda mb-3">
                        Sinais vitais da triagem: PA <?= esc($atendimento['pressao_arterial']) ?> · <?= esc(formatValor($atendimento['temperatura'], 1)) ?> °C ·
                        FC <?= esc($atendimento['frequencia_cardiaca']) ?> · FR <?= esc($atendimento['frequencia_respiratoria']) ?> · SpO₂ <?= esc($atendimento['saturacao']) ?>%
                        <?= badgeNews2(calculaNews2($atendimento, $atendimento['data_nascimento'])) ?>
                    </p>

                    <div class="grupo-classificacao">
                        <?php foreach ($classificacoes as $classificacao): ?>
                            <div class="opcao-classificacao">
                                <input type="radio" id="risco_<?= esc($classificacao['cor']) ?>" name="id_classificacao_nova" value="<?= esc($classificacao['id_classificacao']) ?>"
                                    <?= setValue("id_classificacao_nova", $dados) == $classificacao['id_classificacao'] ? "checked" : "" ?> required>
                                <label class="<?= esc(getClasseRisco($classificacao['cor'])) ?>" for="risco_<?= esc($classificacao['cor']) ?>">
                                    <?= esc($classificacao['nome']) ?>
                                    <small><?= $classificacao['tempo_alvo_minutos'] == 0 ? 'atendimento imediato' : 'médico em até ' . esc($classificacao['tempo_alvo_minutos']) . ' min' ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?= setMsgErrorCampo("id_classificacao_nova", $erros) ?>

                    <div class="mt-3">
                        <label for="motivo" class="form-label campo-obrigatorio">Motivo da reclassificação</label>
                        <textarea class="form-control <?= !empty($erros['motivo']) ? 'is-invalid' : '' ?>" id="motivo" name="motivo" rows="3" required
                            placeholder="O que mudou no quadro do paciente"><?= setValue("motivo", $dados) ?></textarea>
                        <?= setMsgErrorCampo("motivo", $erros) ?>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <?php if (session()->get('usuarioPerfilId') == PERFIL_MEDICO && $atendimento['status'] !== 'aguardando_medico'): ?>
                        <a class="btn btn-outline-secondary" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>">Voltar ao atendimento</a>
                    <?php else: ?>
                        <a class="btn btn-outline-secondary" href="<?= base_url($atendimento['status'] === 'em_observacao' ? 'observacao' : 'fila') ?>">Voltar</a>
                    <?php endif; ?>
                    <button class="btn btn-brand ms-auto" type="submit">Salvar reclassificação</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>

    <div class="col-12 col-xl-4">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Histórico da classificação</h2>
            </div>
            <div class="admin-panel-body linha-tempo">
                <?php $inicial = !empty($reclassificacoes) ? $reclassificacoes[0] : null; ?>
                <article>
                    <p class="linha-tempo-cabecalho"><strong><?= esc(formatDataHora($atendimento['data_hora_triagem'])) ?></strong> · triagem por <?= esc($atendimento['enfermeiro']) ?></p>
                    <span class="legenda-risco texto-risco-<?= esc($inicial ? $inicial['cor_anterior'] : $atendimento['cor']) ?>"><?= esc($inicial ? $inicial['classificacao_anterior'] : $atendimento['classificacao']) ?></span>
                </article>
                <?php foreach ($reclassificacoes as $reclassificacao): ?>
                    <article>
                        <p class="linha-tempo-cabecalho"><strong><?= esc(formatDataHora($reclassificacao['data_hora'])) ?></strong> · <?= esc($reclassificacao['usuario']) ?> (<?= esc($reclassificacao['perfil']) ?>)</p>
                        <span class="legenda-risco texto-risco-<?= esc($reclassificacao['cor_nova']) ?>"><?= esc($reclassificacao['classificacao_nova']) ?></span>
                        <p class="mb-0 mt-1 small"><?= esc($reclassificacao['motivo']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
