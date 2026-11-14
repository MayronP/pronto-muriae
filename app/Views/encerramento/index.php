<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<?= form_open("atendimentos/encerrar") ?>

    <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Desfecho do atendimento</h2>
            </div>
        </div>

        <div class="admin-panel-body">
            <h3 class="secao-form">Destino do paciente <span class="campo-obrigatorio"></span></h3>
            <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-3">
                <?php foreach (getDesfechos() as $valor => $desfecho): ?>
                    <div class="col opcao-desfecho">
                        <input type="radio" id="desfecho_<?= esc($valor) ?>" name="desfecho" value="<?= esc($valor) ?>" <?= setValue("desfecho", $dados) === $valor ? "checked" : "" ?> required>
                        <label for="desfecho_<?= esc($valor) ?>">
                            <i class="fa <?= esc($desfecho['icone']) ?>" aria-hidden="true"></i>
                            <strong><?= esc(getDescricaoStatus($valor)) ?></strong>
                            <small><?= esc($desfecho['descricao']) ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?= setMsgErrorCampo("desfecho", $erros) ?>

            <div class="row g-3 mt-1" data-mostra-quando="desfecho=obito">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="data_obito" class="form-label campo-obrigatorio">Data do óbito</label>
                    <input type="date" class="form-control <?= !empty($erros['data_obito']) ? 'is-invalid' : '' ?>" id="data_obito" name="data_obito"
                        value="<?= setValue("data_obito", $dados) ?>" max="<?= date("Y-m-d") ?>">
                    <?= setMsgErrorCampo("data_obito", $erros) ?>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="hora_obito" class="form-label campo-obrigatorio">Horário do óbito</label>
                    <input type="time" class="form-control <?= !empty($erros['hora_obito']) ? 'is-invalid' : '' ?>" id="hora_obito" name="hora_obito"
                        value="<?= setValue("hora_obito", $dados) ?>">
                    <?= setMsgErrorCampo("hora_obito", $erros) ?>
                </div>
                <div class="col-12 col-lg-6">
                    <label for="declaracao_obito" class="form-label">Número da declaração de óbito</label>
                    <input type="text" class="form-control <?= !empty($erros['declaracao_obito']) ? 'is-invalid' : '' ?>" id="declaracao_obito" name="declaracao_obito"
                        value="<?= setValue("declaracao_obito", $dados) ?>" maxlength="20">
                    <?= setMsgErrorCampo("declaracao_obito", $erros) ?>
                </div>
            </div>

            <div class="mt-3" data-mostra-quando="desfecho=alta_pedido">
                <div class="form-check">
                    <input class="form-check-input <?= !empty($erros['termo_alta_pedido']) ? 'is-invalid' : '' ?>" type="checkbox" id="termo_alta_pedido" name="termo_alta_pedido" value="1"
                        <?= setValue("termo_alta_pedido", $dados) == "1" ? "checked" : "" ?>>
                    <label class="form-check-label" for="termo_alta_pedido">O paciente ou o responsável assinou o termo de responsabilidade da alta a pedido.</label>
                </div>
                <?= setMsgErrorCampo("termo_alta_pedido", $erros) ?>
            </div>

            <h3 class="secao-form">Diagnóstico</h3>
            <div class="row g-3">
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="cid_principal" class="form-label campo-obrigatorio">CID-10 principal</label>
                    <input type="text" class="form-control <?= !empty($erros['cid_principal']) ? 'is-invalid' : '' ?>" id="cid_principal" name="cid_principal" list="lista_cid"
                        value="<?= setValue("cid_principal", $dados) ?>" placeholder="Código ou doença" autocomplete="off" required
                        data-busca-cid="<?= base_url('cid10/buscar') ?>" data-cid-descricao="#cid_descricao">
                    <datalist id="lista_cid"></datalist>
                    <?= setMsgErrorCampo("cid_principal", $erros) ?>
                </div>
                <div class="col-12 col-md-8 col-lg-9 d-flex align-items-end">
                    <p class="texto-ajuda mb-2" id="cid_descricao"><?= !empty($cid) ? esc(formataCid($cid['codigo']) . ' — ' . $cid['descricao']) : 'Digite o código (ex.: I21.9) ou parte do nome da doença.' ?></p>
                </div>
            </div>

            <h3 class="secao-form">Resumo</h3>
            <div class="mb-3">
                <label for="resumo_final" class="form-label campo-obrigatorio">Resumo do atendimento</label>
                <textarea class="form-control <?= !empty($erros['resumo_final']) ? 'is-invalid' : '' ?>" id="resumo_final" name="resumo_final" rows="5"
                    placeholder="Avaliação, condutas e condição do paciente no encerramento." required><?= setValue("resumo_final", $dados) ?></textarea>
                <?= setMsgErrorCampo("resumo_final", $erros) ?>
            </div>
            <div>
                <label for="observacoes_finais" class="form-label">Observações finais</label>
                <textarea class="form-control" id="observacoes_finais" name="observacoes_finais" rows="3"><?= setValue("observacoes_finais", $dados) ?></textarea>
            </div>
        </div>

        <div class="admin-panel-footer">
            <span class="texto-ajuda me-auto">Encerrado por <strong><?= esc(session()->get('usuarioNome')) ?></strong>.</span>
            <a class="btn btn-outline-secondary" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>">Voltar ao atendimento</a>
            <button class="btn btn-brand" type="submit">Encerrar atendimento</button>
        </div>
    </section>

<?= form_close() ?>

<?= $this->endSection() ?>
