<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->renderSection("head") ?>

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

<body class="<?= $this->renderSection("classeBody") ?>">

    <?= $this->renderSection("main") ?>

    <!-- Bootstrap 5 (inclui Popper) -->
    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <!-- Theme -->
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
</body>

</html>
