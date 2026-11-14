<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <?= form_open("plantao/store") ?>

            <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

            <section class="admin-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Nova passagem de plantão (SBAR)</h2>
                        <p>Queixa: <?= esc($atendimento['queixa_principal']) ?></p>
                    </div>
                </div>

                <div class="admin-panel-body">
                    <div class="mb-3">
                        <label for="situacao" class="form-label campo-obrigatorio">S — Situação</label>
                        <textarea class="form-control <?= !empty($erros['situacao']) ? 'is-invalid' : '' ?>" id="situacao" name="situacao" rows="2" required
                            placeholder="O que está acontecendo agora"><?= setValue("situacao", $dados) ?></textarea>
                        <?= setMsgErrorCampo("situacao", $erros) ?>
                    </div>
                    <div class="mb-3">
                        <label for="historico" class="form-label">B — Breve histórico</label>
                        <textarea class="form-control" id="historico" name="historico" rows="2"
                            placeholder="Antecedentes, exames e medicações"><?= setValue("historico", $dados) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="avaliacao" class="form-label campo-obrigatorio">A — Avaliação</label>
                        <textarea class="form-control <?= !empty($erros['avaliacao']) ? 'is-invalid' : '' ?>" id="avaliacao" name="avaliacao" rows="2" required
                            placeholder="Estável, melhorando ou piorando"><?= setValue("avaliacao", $dados) ?></textarea>
                        <?= setMsgErrorCampo("avaliacao", $erros) ?>
                    </div>
                    <div>
                        <label for="recomendacao" class="form-label campo-obrigatorio">R — Recomendação</label>
                        <textarea class="form-control <?= !empty($erros['recomendacao']) ? 'is-invalid' : '' ?>" id="recomendacao" name="recomendacao" rows="2" required
                            placeholder="O que fica pendente para quem assume"><?= setValue("recomendacao", $dados) ?></textarea>
                        <?= setMsgErrorCampo("recomendacao", $erros) ?>
                    </div>
                </div>

                <div class="admin-panel-footer">
                    <a class="btn btn-outline-secondary" href="<?= base_url('plantao') ?>">Voltar</a>
                    <button class="btn btn-brand ms-auto" type="submit">Registrar passagem</button>
                </div>
            </section>

        <?= form_close() ?>
    </div>

    <div class="col-12 col-xl-5">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Passagens anteriores <span class="text-muted fw-normal">(<?= esc((string) count($passagens)) ?>)</span></h2>
            </div>
            <?php if (!empty($passagens)): ?>
                <div class="admin-panel-body linha-tempo">
                    <?php foreach ($passagens as $passagem): ?>
                        <article>
                            <p class="linha-tempo-cabecalho">
                                <strong><?= esc(formatDataHora($passagem['data_hora'])) ?></strong> · <?= esc($passagem['autor']) ?> (<?= esc($passagem['perfil_autor']) ?>)
                                <?= !empty($passagem['recebedor']) ? '· recebida por ' . esc($passagem['recebedor']) : '· aguardando recebimento' ?>
                            </p>
                            <p class="mb-1 small"><strong>S:</strong> <?= esc($passagem['situacao']) ?></p>
                            <?php if (!empty($passagem['historico'])): ?>
                                <p class="mb-1 small"><strong>B:</strong> <?= esc($passagem['historico']) ?></p>
                            <?php endif; ?>
                            <p class="mb-1 small"><strong>A:</strong> <?= esc($passagem['avaliacao']) ?></p>
                            <p class="mb-0 small"><strong>R:</strong> <?= esc($passagem['recomendacao']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="estado-vazio mb-0"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma passagem registrada para este paciente.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?= $this->endSection() ?>
