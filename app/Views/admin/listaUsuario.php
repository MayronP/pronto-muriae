<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <div>
            <h2>Usuários do sistema <span class="text-muted fw-normal">(<?= esc((string) count($usuarios)) ?>)</span></h2>
            <p><?= esc((string) $resumoUsuarios['ativos']) ?> ativos · <?= esc((string) $resumoUsuarios['inativos']) ?> inativos · perfis: <?= esc($resumoUsuarios['nomesPerfis']) ?></p>
        </div>
        <div class="admin-panel-acoes">
            <a class="btn btn-brand btn-sm" href="<?= base_url('admin/usuarios/form/insert/0') ?>"><i class="fa fa-plus me-1" aria-hidden="true"></i>Novo usuário</a>
        </div>
    </div>

    <div class="admin-panel-filtros">
        <form class="row g-2" action="<?= base_url('admin/usuarios') ?>" method="get">
            <div class="col-12 col-md-6 col-lg-4">
                <label for="busca_usuario" class="visually-hidden">Buscar usuário</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="search" class="form-control form-control-sm" id="busca_usuario" name="busca" placeholder="Nome, login ou e-mail" value="<?= esc($busca) ?>">
                </div>
            </div>
            <div class="col-8 col-sm-6 col-md-3">
                <label for="perfil" class="visually-hidden">Perfil</label>
                <select class="form-select form-select-sm" id="perfil" name="id_perfil">
                    <option value="">Todos os perfis</option>
                    <?php foreach ($perfis as $perfil): ?>
                        <option value="<?= esc($perfil['id_perfil']) ?>" <?= $perfil['id_perfil'] == $idPerfil ? 'selected' : '' ?>><?= esc($perfil['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <span class="fw-semibold"><?= esc($usuario['nome']) ?></span>
                                <span class="texto-secundario"><?= esc($usuario['login']) ?></span>
                            </td>
                            <td><?= esc($usuario['email']) ?></td>
                            <td><?= esc($usuario['perfil']) ?></td>
                            <td><span class="status-badge <?= $usuario['ativo'] == 1 ? 'status-ativo' : 'status-inativo' ?>"><?= esc(getSituacaoUsuario($usuario['ativo'])) ?></span></td>
                            <td class="acoes-tabela">
                                <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('admin/usuarios/form/update/' . $usuario['id_usuario']) ?>">Editar</a>
                                <?php if ($usuario['id_usuario'] != session()->get('usuarioId')): ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <?= form_open("admin/usuarios/situacao") ?>
                                                    <input type="hidden" name="id_usuario" value="<?= esc($usuario['id_usuario']) ?>">
                                                    <?php if ($usuario['ativo'] == 1): ?>
                                                        <button class="dropdown-item text-danger" type="submit"><i class="fa fa-ban" aria-hidden="true"></i>Desativar acesso</button>
                                                    <?php else: ?>
                                                        <button class="dropdown-item" type="submit"><i class="fa fa-check" aria-hidden="true"></i>Reativar acesso</button>
                                                    <?php endif; ?>
                                                <?= form_close() ?>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="5"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum usuário localizado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
