<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title -->
    <title><?= esc($titulo) ?> | Muriaé Pronto</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/muriae.png') ?>">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
    <!-- Theme -->
    <link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
</head>

<body class="pagina-impressao">

    <div class="barra-impressao nao-imprimir">
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url($voltar) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar</a>
        <button class="btn btn-brand btn-sm" type="button" data-imprimir><i class="fa fa-print me-1" aria-hidden="true"></i>Imprimir</button>
    </div>

    <div class="folha-mensagem nao-imprimir"><?= mensagem() ?></div>

    <main class="folha">
        <header class="folha-cabecalho">
            <img src="<?= base_url('assets/img/muriae.png') ?>" alt="">
            <div>
                <strong>Muriaé Pronto</strong>
                <span>Pronto Atendimento Hospitalar</span>
            </div>
            <h1><?= esc($titulo) ?></h1>
        </header>

        <?= $this->renderSection("main") ?>
    </main>

    <!-- Bootstrap 5 (inclui Popper) -->
    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>

    <!-- Theme -->
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>

</body>

</html>
