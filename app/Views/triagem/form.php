<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<?= form_open("triagem/store") ?>

    <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

    <section class="admin-panel">
        <div class="admin-panel-header">
            <h2>Acolhimento com classificação de risco</h2>
            <span class="texto-ajuda"><span class="campo-obrigatorio"></span> campos obrigatórios</span>
        </div>

        <div class="admin-panel-body">
            <div class="row g-4">
                <div class="col-12 col-xl-6">
                    <h3 class="secao-form">Avaliação inicial</h3>
                    <div class="mb-3">
                        <label for="queixa_principal" class="form-label campo-obrigatorio">Queixa principal</label>
                        <textarea class="form-control <?= !empty($erros['queixa_principal']) ? 'is-invalid' : '' ?>" id="queixa_principal" name="queixa_principal" rows="3"
                            placeholder="Motivo da procura pelo atendimento"
                            required><?= setValue("queixa_principal", $dados) ?></textarea>
                        <?= setMsgErrorCampo("queixa_principal", $erros) ?>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes_iniciais" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes_iniciais" name="observacoes_iniciais" rows="3"
                            placeholder="Informações relevantes observadas na avaliação."><?= setValue("observacoes_iniciais", $dados) ?></textarea>
                    </div>
                    <div>
                        <label for="alergias" class="form-label">Alergias</label>
                        <textarea class="form-control <?= !empty($erros['alergias']) ? 'is-invalid' : '' ?>" id="alergias" name="alergias" rows="2" maxlength="500"
                            placeholder="Medicamentos, alimentos ou outros"><?= setValue("alergias", $dados) ?></textarea>
                        <?= setMsgErrorCampo("alergias", $erros) ?>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <h3 class="secao-form">Sinais vitais</h3>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="pressao_arterial" class="form-label campo-obrigatorio">Pressão arterial</label>
                            <div class="input-group has-validation">
                                <input type="text" class="form-control <?= !empty($erros['pressao_arterial']) ? 'is-invalid' : '' ?>" id="pressao_arterial" name="pressao_arterial"
                                    placeholder="120/80" value="<?= setValue("pressao_arterial", $dados) ?>" required>
                                <span class="input-group-text">mmHg</span>
                            </div>
                            <?= setMsgErrorCampo("pressao_arterial", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="temperatura" class="form-label campo-obrigatorio">Temperatura</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['temperatura']) ? 'is-invalid' : '' ?>" id="temperatura" name="temperatura"
                                    step="0.1" min="30" max="45" placeholder="36,5" value="<?= setValue("temperatura", $dados) ?>" required>
                                <span class="input-group-text">°C</span>
                            </div>
                            <?= setMsgErrorCampo("temperatura", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="frequencia_cardiaca" class="form-label campo-obrigatorio">Freq. cardíaca</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['frequencia_cardiaca']) ? 'is-invalid' : '' ?>" id="frequencia_cardiaca" name="frequencia_cardiaca"
                                    min="1" max="300" value="<?= setValue("frequencia_cardiaca", $dados) ?>" required>
                                <span class="input-group-text">bpm</span>
                            </div>
                            <?= setMsgErrorCampo("frequencia_cardiaca", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="frequencia_respiratoria" class="form-label campo-obrigatorio">Freq. respiratória</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['frequencia_respiratoria']) ? 'is-invalid' : '' ?>" id="frequencia_respiratoria" name="frequencia_respiratoria"
                                    min="1" max="100" value="<?= setValue("frequencia_respiratoria", $dados) ?>" required>
                                <span class="input-group-text">irpm</span>
                            </div>
                            <?= setMsgErrorCampo("frequencia_respiratoria", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="saturacao" class="form-label campo-obrigatorio">Saturação</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['saturacao']) ? 'is-invalid' : '' ?>" id="saturacao" name="saturacao" min="0" max="100"
                                    value="<?= setValue("saturacao", $dados) ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <?= setMsgErrorCampo("saturacao", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="oxigenio_suplementar" class="form-label campo-obrigatorio">O₂ suplementar</label>
                            <select class="form-select <?= !empty($erros['oxigenio_suplementar']) ? 'is-invalid' : '' ?>" id="oxigenio_suplementar" name="oxigenio_suplementar" required>
                                <option value="0" <?= setValue("oxigenio_suplementar", $dados) == "0" ? "selected" : "" ?>>Não (ar ambiente)</option>
                                <option value="1" <?= setValue("oxigenio_suplementar", $dados) == "1" ? "selected" : "" ?>>Sim</option>
                            </select>
                            <?= setMsgErrorCampo("oxigenio_suplementar", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="nivel_consciencia" class="form-label campo-obrigatorio">Consciência (ACVPU)</label>
                            <select class="form-select <?= !empty($erros['nivel_consciencia']) ? 'is-invalid' : '' ?>" id="nivel_consciencia" name="nivel_consciencia" required>
                                <?php foreach (["A", "C", "V", "P", "U"] as $nivel): ?>
                                    <option value="<?= $nivel ?>" <?= setValue("nivel_consciencia", $dados) == $nivel ? "selected" : "" ?>><?= $nivel ?> — <?= esc(getDescricaoConsciencia($nivel)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= setMsgErrorCampo("nivel_consciencia", $erros) ?>
                        </div>
                        <div class="col-6">
                            <label for="gestante" class="form-label campo-obrigatorio">Gestante</label>
                            <select class="form-select <?= !empty($erros['gestante']) ? 'is-invalid' : '' ?>" id="gestante" name="gestante" required>
                                <option value="0" <?= setValue("gestante", $dados) == "0" ? "selected" : "" ?>>Não</option>
                                <option value="1" <?= setValue("gestante", $dados) == "1" ? "selected" : "" ?>>Sim</option>
                            </select>
                            <?= setMsgErrorCampo("gestante", $erros) ?>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="dor" class="form-label">Dor (0 a 10)</label>
                            <select class="form-select <?= !empty($erros['dor']) ? 'is-invalid' : '' ?>" id="dor" name="dor">
                                <option value="">Não avaliada</option>
                                <?php for ($nota = 0; $nota <= 10; $nota++): ?>
                                    <option value="<?= $nota ?>" <?= setValue("dor", $dados) === (string) $nota ? "selected" : "" ?>><?= $nota ?><?= $nota === 0 ? ' — sem dor' : ($nota === 10 ? ' — pior dor possível' : '') ?></option>
                                <?php endfor; ?>
                            </select>
                            <?= setMsgErrorCampo("dor", $erros) ?>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="glicemia" class="form-label">Glicemia capilar</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['glicemia']) ? 'is-invalid' : '' ?>" id="glicemia" name="glicemia"
                                    min="1" max="2000" value="<?= setValue("glicemia", $dados) ?>">
                                <span class="input-group-text">mg/dL</span>
                            </div>
                            <?= setMsgErrorCampo("glicemia", $erros) ?>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="peso" class="form-label">Peso</label>
                            <div class="input-group has-validation">
                                <input type="number" class="form-control <?= !empty($erros['peso']) ? 'is-invalid' : '' ?>" id="peso" name="peso"
                                    step="0.1" min="0.1" max="400" value="<?= setValue("peso", $dados) ?>">
                                <span class="input-group-text">kg</span>
                            </div>
                            <?= setMsgErrorCampo("peso", $erros) ?>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="secao-form">Classificação de risco <span class="campo-obrigatorio"></span></h3>
            <div class="grupo-classificacao">
                <?php foreach ($classificacoes as $classificacao): ?>
                    <div class="opcao-classificacao">
                        <input type="radio" id="risco_<?= esc($classificacao['cor']) ?>" name="id_classificacao" value="<?= esc($classificacao['id_classificacao']) ?>"
                            <?= setValue("id_classificacao", $dados) == $classificacao['id_classificacao'] ? "checked" : "" ?> required>
                        <label class="<?= esc(getClasseRisco($classificacao['cor'])) ?>" for="risco_<?= esc($classificacao['cor']) ?>">
                            <?= esc($classificacao['nome']) ?>
                            <small><?= $classificacao['tempo_alvo_minutos'] == 0 ? 'atendimento imediato' : 'médico em até ' . esc($classificacao['tempo_alvo_minutos']) . ' min' ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?= setMsgErrorCampo("id_classificacao", $erros) ?>
        </div>

        <div class="admin-panel-footer">
            <span class="texto-ajuda me-auto">Registrado por <strong><?= esc(session()->get('usuarioNome')) ?></strong> (<?= esc(session()->get('usuarioPerfil')) ?>).</span>
            <a class="btn btn-outline-secondary" href="<?= base_url('triagem') ?>">Cancelar</a>
            <button class="btn btn-brand" type="submit">Salvar classificação</button>
        </div>
    </section>

<?= form_close() ?>

<?= $this->endSection() ?>
