<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= form_open("admin/usuarios/store") ?>

    <input type="hidden" name="id_usuario" value="<?= setValue("id_usuario", $dados) ?>">
    <input type="hidden" name="action" value="<?= esc($action) ?>">

    <section class="admin-panel">
        <div class="admin-panel-header">
            <h2><?= $action === 'update' ? esc($dados['nome'] ?: 'Usuário') : 'Novo usuário' ?></h2>
            <span class="texto-ajuda"><span class="campo-obrigatorio"></span> campos obrigatórios</span>
        </div>

        <div class="admin-panel-body">
            <h3 class="secao-form">Dados do usuário</h3>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label for="nome" class="form-label campo-obrigatorio">Nome</label>
                    <input type="text" class="form-control <?= !empty($erros['nome']) ? 'is-invalid' : '' ?>" id="nome" name="nome"
                        value="<?= setValue("nome", $dados) ?>" maxlength="150" required>
                    <?= setMsgErrorCampo("nome", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="login" class="form-label campo-obrigatorio">Login</label>
                    <input type="text" class="form-control <?= !empty($erros['login']) ? 'is-invalid' : '' ?>" id="login" name="login"
                        value="<?= setValue("login", $dados) ?>" maxlength="80" autocomplete="off" required>
                    <?= setMsgErrorCampo("login", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="id_perfil" class="form-label campo-obrigatorio">Perfil</label>
                    <select class="form-select <?= !empty($erros['id_perfil']) ? 'is-invalid' : '' ?>" id="id_perfil" name="id_perfil" required>
                        <option value="">Selecione</option>
                        <?php foreach ($perfis as $perfil): ?>
                            <option value="<?= esc($perfil['id_perfil']) ?>" <?= setValue("id_perfil", $dados) == $perfil['id_perfil'] ? "selected" : "" ?>><?= esc($perfil['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= setMsgErrorCampo("id_perfil", $erros) ?>
                </div>
                <div class="col-12 col-lg-6">
                    <label for="email" class="form-label campo-obrigatorio">E-mail</label>
                    <input type="email" class="form-control <?= !empty($erros['email']) ? 'is-invalid' : '' ?>" id="email" name="email"
                        value="<?= setValue("email", $dados) ?>" maxlength="150" required>
                    <?= setMsgErrorCampo("email", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="registro_profissional" class="form-label">Registro profissional</label>
                    <input type="text" class="form-control <?= !empty($erros['registro_profissional']) ? 'is-invalid' : '' ?>" id="registro_profissional" name="registro_profissional"
                        value="<?= setValue("registro_profissional", $dados) ?>" maxlength="30" placeholder="Ex.: CRM-MG 12345">
                    <?= setMsgErrorCampo("registro_profissional", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="ativo" class="form-label campo-obrigatorio">Situação</label>
                    <select class="form-select <?= !empty($erros['ativo']) ? 'is-invalid' : '' ?>" id="ativo" name="ativo" required>
                        <option value="1" <?= setValue("ativo", $dados) == "1" ? "selected" : "" ?>>Ativo</option>
                        <option value="0" <?= setValue("ativo", $dados) == "0" ? "selected" : "" ?>>Inativo</option>
                    </select>
                    <?= setMsgErrorCampo("ativo", $erros) ?>
                </div>
            </div>

            <h3 class="secao-form">Senha de acesso</h3>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="senha" class="form-label <?= $action === "insert" ? "campo-obrigatorio" : "" ?>">Senha</label>
                    <input type="password" class="form-control <?= !empty($erros['senha']) ? 'is-invalid' : '' ?>" id="senha" name="senha"
                        minlength="6" maxlength="72" autocomplete="new-password" <?= $action === "insert" ? "required" : "" ?>>
                    <?= setMsgErrorCampo("senha", $erros) ?>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="confirma_senha" class="form-label">Confirmação da senha</label>
                    <input type="password" class="form-control <?= !empty($erros['confirma_senha']) ? 'is-invalid' : '' ?>" id="confirma_senha" name="confirma_senha"
                        maxlength="72" autocomplete="new-password">
                    <?= setMsgErrorCampo("confirma_senha", $erros) ?>
                </div>
            </div>
            <p class="texto-ajuda mt-2 mb-0"><?= $action === "update" ? 'Deixe em branco para manter a senha atual. ' : '' ?>Mínimo de 6 caracteres.</p>
        </div>

        <div class="admin-panel-footer">
            <a class="btn btn-outline-secondary" href="<?= base_url('admin/usuarios') ?>">Cancelar</a>
            <button class="btn btn-brand ms-auto" type="submit">Salvar usuário</button>
        </div>
    </section>

<?= form_close() ?>

<?= $this->endSection() ?>
