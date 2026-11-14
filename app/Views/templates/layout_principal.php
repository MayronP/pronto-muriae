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

<body class="admin-body">

    <!-- ##### Menu lateral ##### -->
    <aside class="app-sidebar">
        <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="menuLateral" aria-label="Menu principal" data-bs-theme="dark">
            <div class="offcanvas-header">
                <span class="offcanvas-title fw-semibold">Menu</span>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral" aria-label="Fechar menu"></button>
            </div>
            <div class="offcanvas-body flex-column p-0">
                <a class="app-marca" href="<?= base_url('dashboard') ?>">
                    <img src="<?= base_url('assets/img/muriae.png') ?>" alt="">
                    <span><strong>Muriaé Pronto</strong><small>Pronto Atendimento Hospitalar</small></span>
                </a>

                <nav class="app-menu">
                    <span class="app-menu-titulo">Atendimento</span>
                    <a class="nav-link <?= $paginaAtiva === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a>
                    <a class="nav-link <?= $paginaAtiva === 'pacientes' ? 'active' : '' ?>" href="<?= base_url('pacientes') ?>"><i class="fa fa-users" aria-hidden="true"></i> Pacientes</a>
                    <?php if (session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM): ?>
                        <a class="nav-link <?= $paginaAtiva === 'triagem' ? 'active' : '' ?>" href="<?= base_url('triagem') ?>"><i class="fa fa-heartbeat" aria-hidden="true"></i> Triagem</a>
                    <?php endif; ?>
                    <a class="nav-link <?= $paginaAtiva === 'fila' ? 'active' : '' ?>" href="<?= base_url('fila') ?>"><i class="fa fa-list-ol" aria-hidden="true"></i> Fila</a>
                    <a class="nav-link <?= $paginaAtiva === 'atendimento' ? 'active' : '' ?>" href="<?= base_url('fila?status=em_atendimento') ?>"><i class="fa fa-user-md" aria-hidden="true"></i> Em atendimento</a>
                    <a class="nav-link <?= $paginaAtiva === 'observacao' ? 'active' : '' ?>" href="<?= base_url('observacao') ?>"><i class="fa fa-bed" aria-hidden="true"></i> Observação</a>
                    <?php if (in_array(session()->get('usuarioPerfilId'), [PERFIL_ENFERMAGEM, PERFIL_MEDICO])): ?>
                        <a class="nav-link <?= $paginaAtiva === 'plantao' ? 'active' : '' ?>" href="<?= base_url('plantao') ?>"><i class="fa fa-exchange" aria-hidden="true"></i> Passagem de plantão</a>
                    <?php endif; ?>
                    <?php if (session()->get('usuarioPerfilId') == PERFIL_ENFERMAGEM): ?>
                        <a class="nav-link <?= $paginaAtiva === 'notificacoes' ? 'active' : '' ?>" href="<?= base_url('notificacoes') ?>"><i class="fa fa-bell-o" aria-hidden="true"></i> Notificações</a>
                    <?php endif; ?>

                    <?php if (session()->get('usuarioPerfilId') == PERFIL_ADMINISTRADOR): ?>
                        <span class="app-menu-titulo">Gestão</span>
                        <a class="nav-link <?= url_is('admin/usuarios*') ? 'active' : '' ?>" href="<?= base_url('admin/usuarios') ?>"><i class="fa fa-id-badge" aria-hidden="true"></i> Usuários</a>
                        <a class="nav-link <?= url_is('admin/classificacoes*') ? 'active' : '' ?>" href="<?= base_url('admin/classificacoes') ?>"><i class="fa fa-clock-o" aria-hidden="true"></i> Classificação de risco</a>
                        <a class="nav-link <?= url_is('admin/protocolos*') ? 'active' : '' ?>" href="<?= base_url('admin/protocolos') ?>"><i class="fa fa-check-square-o" aria-hidden="true"></i> Protocolos</a>
                        <a class="nav-link <?= $paginaAtiva === 'notificacoes' ? 'active' : '' ?>" href="<?= base_url('notificacoes') ?>"><i class="fa fa-bell-o" aria-hidden="true"></i> Notificações</a>
                        <a class="nav-link <?= url_is('admin/relatorios*') ? 'active' : '' ?>" href="<?= base_url('admin/relatorios') ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> Relatórios</a>
                        <a class="nav-link <?= url_is('admin/auditoria*') ? 'active' : '' ?>" href="<?= base_url('admin/auditoria') ?>"><i class="fa fa-shield" aria-hidden="true"></i> Auditoria</a>
                    <?php endif; ?>

                    <span class="app-menu-titulo">Sala de espera</span>
                    <a class="nav-link" href="<?= base_url('painel') ?>" target="_blank" rel="noopener"><i class="fa fa-television" aria-hidden="true"></i> Painel da TV</a>
                </nav>
            </div>
        </div>
    </aside>
    <!-- ##### /Menu lateral ##### -->

    <div class="app-principal">

        <!-- ##### Barra superior ##### -->
        <header class="app-topo">
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral" aria-label="Abrir menu">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>

            <div class="app-topo-contexto me-auto">
                <strong>Pronto Atendimento</strong>
                <?= esc(dataAtualPorExtenso()) ?>
            </div>

            <?php if (in_array(session()->get('usuarioPerfilId'), [PERFIL_ENFERMAGEM, PERFIL_MEDICO])): ?>
                <div class="dropdown local-atendimento">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Local usado nas chamadas do painel">
                        <i class="fa fa-map-marker me-1" aria-hidden="true"></i><span class="d-none d-md-inline"><?= esc(getLocalAtendimento()) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Local das suas chamadas no painel</h6></li>
                        <?php foreach (getLocaisAtendimento() as $local): ?>
                            <li>
                                <?= form_open("painel/local") ?>
                                    <input type="hidden" name="local" value="<?= esc($local) ?>">
                                    <button class="dropdown-item <?= $local === getLocalAtendimento() ? 'active' : '' ?>" type="submit"><?= esc($local) ?></button>
                                <?= form_close() ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="dropdown">
                <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-1 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="usuario-iniciais" aria-hidden="true"><?= esc(getIniciais(session()->get('usuarioNome'))) ?></span>
                    <span class="usuario-logado d-none d-sm-block">
                        <strong><?= esc(session()->get('usuarioNome')) ?></strong>
                        <small><?= esc(session()->get('usuarioPerfil')) ?></small>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <?= form_open('logout') ?>
                            <button class="dropdown-item" type="submit"><i class="fa fa-sign-out" aria-hidden="true"></i>Sair</button>
                        <?= form_close() ?>
                    </li>
                </ul>
            </div>
        </header>
        <!-- ##### /Barra superior ##### -->

        <main class="app-conteudo">
            <div class="cabecalho-pagina">
                <div>
                    <h1><?= esc($titulo) ?></h1>
                    <?php if ($subtitulo !== ''): ?>
                        <p><?= esc($subtitulo) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?= mensagem() ?>

            <?= $this->renderSection("main") ?>
        </main>

        <footer class="app-rodape">
            &copy; <?= date("Y") ?> Muriaé Pronto — Pronto Atendimento Hospitalar
        </footer>
    </div>

    <!-- Bootstrap 5 (inclui Popper) -->
    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <!-- Theme -->
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>

</body>

</html>
