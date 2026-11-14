<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= form_open("pacientes/store") ?>

    <input type="hidden" name="id_paciente" value="<?= setValue("id_paciente", $dados) ?>">
    <input type="hidden" name="action" value="<?= esc($action) ?>">

    <section class="admin-panel">
        <div class="admin-panel-header">
            <h2><?= $action === 'update' ? esc(nomePaciente($dados)) : 'Novo paciente' ?></h2>
            <span class="texto-ajuda"><span class="campo-obrigatorio"></span> campos obrigatórios</span>
        </div>

        <div class="admin-panel-body">

            <?php if (!empty($dados['nao_identificado'])): ?>
                <h3 class="secao-form">Paciente não identificado</h3>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="caracteristicas" class="form-label campo-obrigatorio">Características para reconhecimento</label>
                        <textarea class="form-control <?= !empty($erros['caracteristicas']) ? 'is-invalid' : '' ?>" id="caracteristicas" name="caracteristicas" rows="2" maxlength="255"><?= setValue("caracteristicas", $dados) ?></textarea>
                        <?= setMsgErrorCampo("caracteristicas", $erros) ?>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="identidade_confirmada" name="identidade_confirmada" value="1"
                                <?= setValue("identidade_confirmada", $dados) == "1" ? "checked" : "" ?>>
                            <label class="form-check-label" for="identidade_confirmada">
                                Identidade confirmada: informe abaixo o nome real e a data de nascimento.
                            </label>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <h3 class="secao-form">Identificação</h3>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="nome_completo" class="form-label campo-obrigatorio">Nome completo (civil)</label>
                    <input type="text" class="form-control <?= !empty($erros['nome_completo']) ? 'is-invalid' : '' ?>" id="nome_completo" name="nome_completo"
                        value="<?= setValue("nome_completo", $dados) ?>" maxlength="150" required>
                    <?= setMsgErrorCampo("nome_completo", $erros) ?>
                </div>
                <div class="col-12 col-lg-6">
                    <label for="nome_social" class="form-label">Nome social</label>
                    <input type="text" class="form-control <?= !empty($erros['nome_social']) ? 'is-invalid' : '' ?>" id="nome_social" name="nome_social"
                        value="<?= setValue("nome_social", $dados) ?>" maxlength="150">
                    <?= setMsgErrorCampo("nome_social", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="data_nascimento" class="form-label <?= empty($dados['nao_identificado']) ? 'campo-obrigatorio' : '' ?>">Data de nascimento</label>
                    <input type="date" class="form-control <?= !empty($erros['data_nascimento']) ? 'is-invalid' : '' ?>" id="data_nascimento" name="data_nascimento"
                        value="<?= setValue("data_nascimento", $dados) ?>" max="<?= date("Y-m-d") ?>" <?= empty($dados['nao_identificado']) ? 'required' : '' ?>>
                    <?= setMsgErrorCampo("data_nascimento", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select class="form-select <?= !empty($erros['sexo']) ? 'is-invalid' : '' ?>" id="sexo" name="sexo">
                        <option value="" <?= setValue("sexo", $dados) == "" ? "selected" : "" ?>>Selecione</option>
                        <option value="F" <?= setValue("sexo", $dados) == "F" ? "selected" : "" ?>>Feminino</option>
                        <option value="M" <?= setValue("sexo", $dados) == "M" ? "selected" : "" ?>>Masculino</option>
                        <option value="O" <?= setValue("sexo", $dados) == "O" ? "selected" : "" ?>>Outro</option>
                        <option value="N" <?= setValue("sexo", $dados) == "N" ? "selected" : "" ?>>Prefere não informar</option>
                    </select>
                    <?= setMsgErrorCampo("sexo", $erros) ?>
                </div>
                <div class="col-12 col-lg-6">
                    <label for="nome_mae" class="form-label">Nome da mãe</label>
                    <input type="text" class="form-control <?= !empty($erros['nome_mae']) ? 'is-invalid' : '' ?>" id="nome_mae" name="nome_mae"
                        value="<?= setValue("nome_mae", $dados) ?>" maxlength="150">
                    <?= setMsgErrorCampo("nome_mae", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control <?= !empty($erros['cpf']) ? 'is-invalid' : '' ?>" id="cpf" name="cpf"
                        placeholder="000.000.000-00" value="<?= setValue("cpf", $dados) ?>" maxlength="14">
                    <?= setMsgErrorCampo("cpf", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="cns" class="form-label">Cartão Nacional de Saúde</label>
                    <input type="text" class="form-control <?= !empty($erros['cns']) ? 'is-invalid' : '' ?>" id="cns" name="cns" inputmode="numeric"
                        placeholder="15 dígitos" value="<?= setValue("cns", $dados) ?>" maxlength="18">
                    <?= setMsgErrorCampo("cns", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control <?= !empty($erros['telefone']) ? 'is-invalid' : '' ?>" id="telefone" name="telefone"
                        placeholder="(00) 00000-0000" value="<?= setValue("telefone", $dados) ?>" maxlength="20">
                    <?= setMsgErrorCampo("telefone", $erros) ?>
                </div>
            </div>

            <h3 class="secao-form">Alergias</h3>
            <label for="alergias" class="form-label">Alergias conhecidas</label>
            <textarea class="form-control <?= !empty($erros['alergias']) ? 'is-invalid' : '' ?>" id="alergias" name="alergias" rows="2" maxlength="500"
                placeholder="Medicamentos, alimentos ou outros"><?= setValue("alergias", $dados) ?></textarea>
            <?= setMsgErrorCampo("alergias", $erros) ?>

            <h3 class="secao-form">Endereço</h3>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="endereco" class="form-label">Logradouro</label>
                    <input type="text" class="form-control <?= !empty($erros['endereco']) ? 'is-invalid' : '' ?>" id="endereco" name="endereco"
                        value="<?= setValue("endereco", $dados) ?>" maxlength="180">
                    <?= setMsgErrorCampo("endereco", $erros) ?>
                </div>
                <div class="col-12 col-sm-4 col-lg-2">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control <?= !empty($erros['numero']) ? 'is-invalid' : '' ?>" id="numero" name="numero"
                        value="<?= setValue("numero", $dados) ?>" maxlength="20">
                    <?= setMsgErrorCampo("numero", $erros) ?>
                </div>
                <div class="col-12 col-sm-8 col-lg-4">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control <?= !empty($erros['bairro']) ? 'is-invalid' : '' ?>" id="bairro" name="bairro"
                        value="<?= setValue("bairro", $dados) ?>" maxlength="100">
                    <?= setMsgErrorCampo("bairro", $erros) ?>
                </div>
                <div class="col-12 col-md-9">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control <?= !empty($erros['cidade']) ? 'is-invalid' : '' ?>" id="cidade" name="cidade"
                        value="<?= setValue("cidade", $dados) ?>" maxlength="100">
                    <?= setMsgErrorCampo("cidade", $erros) ?>
                </div>
                <div class="col-12 col-md-3">
                    <label for="estado" class="form-label">UF</label>
                    <input type="text" class="form-control <?= !empty($erros['estado']) ? 'is-invalid' : '' ?>" id="estado" name="estado" maxlength="2"
                        placeholder="MG" value="<?= setValue("estado", $dados) ?>">
                    <?= setMsgErrorCampo("estado", $erros) ?>
                </div>
            </div>

            <h3 class="secao-form">Contato de referência</h3>
            <div class="row g-3">
                <div class="col-12 col-md-7">
                    <label for="nome_contato" class="form-label">Nome do contato</label>
                    <input type="text" class="form-control <?= !empty($erros['nome_contato']) ? 'is-invalid' : '' ?>" id="nome_contato" name="nome_contato"
                        value="<?= setValue("nome_contato", $dados) ?>" maxlength="150">
                    <?= setMsgErrorCampo("nome_contato", $erros) ?>
                </div>
                <div class="col-12 col-md-5">
                    <label for="telefone_contato" class="form-label">Telefone do contato</label>
                    <input type="tel" class="form-control <?= !empty($erros['telefone_contato']) ? 'is-invalid' : '' ?>" id="telefone_contato" name="telefone_contato"
                        value="<?= setValue("telefone_contato", $dados) ?>" maxlength="20">
                    <?= setMsgErrorCampo("telefone_contato", $erros) ?>
                </div>
            </div>
        </div>

        <div class="admin-panel-footer">
            <a class="btn btn-outline-secondary" href="<?= base_url('pacientes') ?>">Cancelar</a>
            <button class="btn btn-brand ms-auto" type="submit"><?= $action === 'update' ? 'Salvar alterações' : 'Salvar e ir para a admissão' ?></button>
        </div>
    </section>

<?= form_close() ?>

<?= $this->endSection() ?>
