<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<section class="admin-panel">

    <div class="admin-panel-header">
        <h2>Pacientes <span class="text-muted fw-normal">(<?= esc((string) count($pacientes)) ?>)</span></h2>
        <?php if ($podeAdmitir): ?>
            <div class="admin-panel-acoes">
                <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes/naoIdentificado') ?>">Paciente não identificado</a>
                <a class="btn btn-brand btn-sm" href="<?= base_url('pacientes/form/insert/0') ?>"><i class="fa fa-plus me-1" aria-hidden="true"></i>Novo paciente</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-panel-filtros">
        <form class="row g-2" action="<?= base_url('pacientes') ?>" method="get" role="search">
            <div class="col-12 col-md-7 col-lg-5">
                <label for="busca" class="visually-hidden">Buscar paciente</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="search" class="form-control form-control-sm" id="busca" name="busca"
                        placeholder="Nome, nome social, CPF ou cartão SUS" value="<?= esc($busca) ?>">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Buscar</button>
            </div>
            <?php if ($busca !== ''): ?>
                <div class="col-auto">
                    <a class="btn btn-link btn-sm" href="<?= base_url('pacientes') ?>">Limpar</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>CPF</th>
                    <th>Nascimento</th>
                    <th>Telefone</th>
                    <th>Último atendimento</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pacientes)): ?>
                    <?php foreach ($pacientes as $paciente): ?>
                        <?php $podeAdmitirPaciente = $podeAdmitir && empty($paciente['situacao']); ?>
                        <tr>
                            <td>
                                <span class="fw-semibold text-nowrap"><?= esc(nomePaciente($paciente)) ?></span>
                                <?php if (!empty($paciente['alergias'])): ?>
                                    <span class="tag tag-alerta ms-1" title="Alergias: <?= esc($paciente['alergias']) ?>">Alergia</span>
                                <?php endif; ?>
                                <?php if (!empty($paciente['nao_identificado'])): ?>
                                    <span class="tag tag-roxa ms-1">Não identificado</span>
                                <?php endif; ?>
                                <?php if (!empty($paciente['nome_social'])): ?>
                                    <span class="texto-secundario">Nome civil: <?= esc($paciente['nome_completo']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap"><?= esc($paciente['cpf'] ?: '—') ?></td>
                            <td class="text-nowrap">
                                <?= esc(formatData($paciente['data_nascimento'])) ?>
                                <?php if (!empty($paciente['data_nascimento'])): ?>
                                    <span class="text-muted">(<?= esc(calculaIdade($paciente['data_nascimento'])) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap"><?= esc($paciente['telefone'] ?: '—') ?></td>
                            <td class="text-nowrap"><?= esc(formatDataHora($paciente['ultimo_atendimento'])) ?></td>
                            <td>
                                <?php if (!empty($paciente['situacao'])): ?>
                                    <span class="status-badge <?= esc(getClasseStatus($paciente['situacao'])) ?>"><?= esc(getDescricaoStatus($paciente['situacao'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="acoes-tabela">
                                <?php if ($podeAdmitirPaciente): ?>
                                    <a class="btn btn-brand btn-sm" href="<?= base_url('admissao/form/' . $paciente['id_paciente']) ?>">Admitir</a>
                                <?php else: ?>
                                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('pacientes/historico/' . $paciente['id_paciente']) ?>">Histórico</a>
                                <?php endif; ?>
                                <?php if ($podeAdmitir): ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Mais ações">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Mais ações</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if ($podeAdmitirPaciente): ?>
                                                <li><a class="dropdown-item" href="<?= base_url('pacientes/historico/' . $paciente['id_paciente']) ?>"><i class="fa fa-history" aria-hidden="true"></i>Histórico</a></li>
                                            <?php endif; ?>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('pacientes/form/update/' . $paciente['id_paciente']) ?>">
                                                    <?php if (!empty($paciente['nao_identificado'])): ?>
                                                        <i class="fa fa-id-card-o" aria-hidden="true"></i>Identificar paciente
                                                    <?php else: ?>
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Editar cadastro
                                                    <?php endif; ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="7"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum paciente localizado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<?= $this->endSection() ?>
