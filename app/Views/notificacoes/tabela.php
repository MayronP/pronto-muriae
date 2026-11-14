<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Doença ou agravo</th>
                <?php if (isset($situacao)): ?>
                    <th>Paciente</th>
                <?php endif; ?>
                <th>Registrada</th>
                <th>Situação</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($notificacoes)): ?>
                <?php foreach ($notificacoes as $notificacao): ?>
                    <tr class="<?= $notificacao['situacao'] === 'pendente' && strtotime($notificacao['prazo']) < time() ? 'linha-atrasada' : '' ?>">
                        <td>
                            <span class="fw-semibold"><?= esc($notificacao['agravo']) ?></span>
                            <span class="texto-secundario"><?= $notificacao['tipo'] === 'imediata' ? 'Imediata (24 h)' : 'Semanal (7 dias)' ?></span>
                        </td>
                        <?php if (isset($situacao)): ?>
                            <td>
                                <?= esc(nomePaciente($notificacao)) ?>
                                <span class="texto-secundario"><?= esc($notificacao['numero']) ?></span>
                            </td>
                        <?php endif; ?>
                        <td class="text-nowrap">
                            <?= esc(formatDataHora($notificacao['data_hora'])) ?>
                            <span class="texto-secundario"><?= esc($notificacao['usuario']) ?></span>
                        </td>
                        <td>
                            <?php if ($notificacao['situacao'] === 'notificada'): ?>
                                <span class="status-badge status-concluido">Notificada</span>
                                <span class="texto-secundario"><?= esc(formatDataHora($notificacao['data_hora_notificacao'])) ?> · <?= esc($notificacao['notificador']) ?><?= !empty($notificacao['numero_sinan']) ? ' · nº ' . esc($notificacao['numero_sinan']) : '' ?></span>
                            <?php elseif (strtotime($notificacao['prazo']) < time()): ?>
                                <span class="status-badge status-suspenso">Prazo vencido</span>
                                <span class="texto-secundario">prazo era <?= esc(formatDataHora($notificacao['prazo'])) ?></span>
                            <?php else: ?>
                                <span class="status-badge status-aguardando">Pendente</span>
                                <span class="texto-secundario">prazo até <?= esc(formatDataHora($notificacao['prazo'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="acoes-tabela">
                            <?php if ($podeNotificar && $notificacao['situacao'] === 'pendente'): ?>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-outline-brand btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">Registrar envio</button>
                                    <div class="dropdown-menu dropdown-menu-end p-3 menu-formulario">
                                        <?= form_open("notificacoes/notificar") ?>
                                            <input type="hidden" name="id_notificacao" value="<?= esc($notificacao['id_notificacao']) ?>">
                                            <input type="hidden" name="origem" value="<?= isset($situacao) ? 'lista' : 'atendimento' ?>">
                                            <label class="form-label" for="sinan_<?= esc($notificacao['id_notificacao']) ?>">Número da notificação no Sinan</label>
                                            <input type="text" class="form-control form-control-sm mb-2" id="sinan_<?= esc($notificacao['id_notificacao']) ?>" name="numero_sinan" maxlength="30">
                                            <button class="btn btn-brand btn-sm w-100" type="submit">Marcar como notificada</button>
                                        <?= form_close() ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($situacao)): ?>
                                <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $notificacao['id_atendimento']) ?>">Prontuário</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td class="estado-vazio" colspan="<?= isset($situacao) ? 5 : 4 ?>"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma notificação.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
