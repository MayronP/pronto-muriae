<?= $this->extend('templates/layout_publico') ?>

<?= $this->section("classeBody") ?>pagina-login<?= $this->endSection() ?>

<?= $this->section("main") ?>

<aside class="login-marca">
    <div>
        <img src="<?= base_url('assets/img/muriae.png') ?>" alt="">
        <h1>Muriaé Pronto</h1>
        <p class="mb-0">Sistema de Pronto Atendimento Hospitalar</p>
    </div>

    <svg class="login-ecg" viewBox="0 0 600 60" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 34 H150 Q160 26 170 34 H186 L194 42 L206 6 L218 56 L226 34 H254 Q272 22 290 34 H600" />
    </svg>

    <div>
        <div class="faixa-risco" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
        <small class="opacity-75">&copy; <?= date("Y") ?> Muriaé Pronto</small>
    </div>
</aside>

<main class="login-formulario">
    <div class="login-cartao">
        <img class="login-icone d-lg-none" src="<?= base_url('assets/img/muriae.png') ?>" alt="Muriaé Pronto">

        <h2>Entrar</h2>
        <p class="text-muted mb-4">Acesso restrito aos profissionais autorizados.</p>

        <?= mensagem() ?>

        <?= form_open("login") ?>
            <div class="mb-3">
                <label for="login" class="form-label">Login ou e-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
                    <input type="text" class="form-control <?= !empty($erros['login']) ? 'is-invalid' : '' ?>" id="login" name="login"
                        autocomplete="username" value="<?= setValue("login", $dados) ?>" required autofocus>
                </div>
                <?= setMsgErrorCampo("login", $erros) ?>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock" aria-hidden="true"></i></span>
                    <input type="password" class="form-control <?= !empty($erros['senha']) ? 'is-invalid' : '' ?>" id="senha" name="senha"
                        autocomplete="current-password" required>
                    <button class="btn btn-mostrar-senha" type="button" data-mostra-senha="senha" aria-label="Mostrar senha">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <?= setMsgErrorCampo("senha", $erros) ?>
            </div>

            <button class="btn btn-brand btn-entrar w-100" type="submit">Entrar<i class="fa fa-sign-in ms-2" aria-hidden="true"></i></button>
        <?= form_close() ?>
    </div>
</main>

<?= $this->endSection() ?>
