<?= $this->extend('templates/layout_principal') ?>

<?= $this->section("main") ?>

<?= $this->include('templates/banner_atendimento') ?>

<div class="barra-acoes-paciente">
    <?php if ($podePrescrever): ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/form/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-arrow-left me-1" aria-hidden="true"></i>Voltar ao atendimento</a>
    <?php else: ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('enfermagem/index/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-heartbeat me-1" aria-hidden="true"></i>Enfermagem</a>
    <?php endif; ?>
    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('atendimentos/detalhes/' . $atendimento['id_atendimento']) ?>"><i class="fa fa-file-text-o me-1" aria-hidden="true"></i>Prontuário</a>
</div>

<section class="admin-panel mb-3">
    <div class="admin-panel-header">
        <h2>Prescrição <span class="text-muted fw-normal">(<?= esc((string) count($medicamentos)) ?>)</span></h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Posologia</th>
                    <th>Prescritor</th>
                    <th>Doses</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($medicamentos)): ?>
                    <?php foreach ($medicamentos as $item): ?>
                        <tr class="<?= $item['situacao'] == 'suspenso' ? 'linha-inativa' : '' ?>">
                            <td>
                                <span class="fw-semibold"><?= esc($item['medicamento']) ?></span>
                                <?php if (!empty($item['apresentacao'])): ?>
                                    <span class="texto-secundario"><?= esc($item['apresentacao']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($item['dose']) ?> · <?= esc($item['via']) ?> · <?= esc($item['frequencia']) ?>
                                <?php if (!empty($item['observacoes'])): ?>
                                    <span class="texto-secundario"><?= esc($item['observacoes']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($item['prescritor']) ?>
                                <span class="texto-secundario"><?= esc(formatDataHora($item['data_hora_prescricao'])) ?></span>
                            </td>
                            <td class="text-nowrap">
                                <?php if ($item['situacao'] == 'suspenso'): ?>
                                    <span class="status-badge status-suspenso">Suspenso</span>
                                <?php elseif ($item['se_necessario'] == 1): ?>
                                    <span class="status-badge status-indefinido">Se necessário</span>
                                    <span class="texto-secundario"><?= esc((string) $item['doses_feitas']) ?> administração(ões)</span>
                                <?php elseif ($item['doses_pendentes'] == 0 && $item['doses_feitas'] == 0): ?>
                                    <span class="status-badge status-suspenso">Não administrado</span>
                                    <span class="texto-secundario"><?= esc((string) $item['doses_feitas']) ?> de <?= esc((string) $item['doses_total']) ?> administrada(s)</span>
                                <?php elseif ($item['doses_pendentes'] == 0 && $item['doses_feitas'] > 0): ?>
                                    <span class="status-badge status-concluido">Concluído</span>
                                    <span class="texto-secundario"><?= esc((string) $item['doses_feitas']) ?> de <?= esc((string) $item['doses_total']) ?> administrada(s)</span>
                                <?php else: ?>
                                    <span class="status-badge status-andamento">Em curso</span>
                                    <span class="texto-secundario"><?= esc((string) $item['doses_feitas']) ?> de <?= esc((string) $item['doses_total']) ?> administrada(s)</span>
                                <?php endif; ?>
                            </td>
                            <td class="acoes-tabela">
                                <?php if ($item['situacao'] == 'prescrito' && $item['se_necessario'] == 1): ?>
                                    <?= form_open("medicamentos/sos", ["class" => "d-inline"]) ?>
                                        <input type="hidden" name="id_item_prescricao" value="<?= esc($item['id_item_prescricao']) ?>">
                                        <button class="btn btn-outline-brand btn-sm" type="submit">Registrar administração</button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                                <?php if ($podePrescrever && $item['situacao'] == 'prescrito'): ?>
                                    <?= form_open("medicamentos/situacao", ["class" => "d-inline"]) ?>
                                        <input type="hidden" name="id_item_prescricao" value="<?= esc($item['id_item_prescricao']) ?>">
                                        <input type="hidden" name="situacao" value="suspenso">
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">Suspender</button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="5"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhum medicamento prescrito neste atendimento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-panel mb-3">
    <div class="admin-panel-header">
        <div>
            <h2>Aprazamento e checagem <span class="text-muted fw-normal">(<?= esc((string) count($doses)) ?> doses)</span></h2>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>Medicamento</th>
                    <th>Situação</th>
                    <th>Checagem</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($doses)): ?>
                    <?php foreach ($doses as $dose): ?>
                        <tr class="<?= $dose['pode_checar'] && strtotime($dose['data_hora_prevista']) <= time() ? 'linha-atrasada' : '' ?>">
                            <td class="text-nowrap fw-semibold">
                                <?= esc(formatHora($dose['data_hora_prevista'])) ?>
                                <span class="texto-secundario"><?= esc(formatData($dose['data_hora_prevista'])) ?></span>
                            </td>
                            <td>
                                <?= esc($dose['medicamento']) ?>
                                <span class="texto-secundario"><?= esc($dose['dose']) ?> · <?= esc($dose['via']) ?></span>
                            </td>
                            <td><?= badgeDose($dose) ?></td>
                            <td>
                                <?php if (!empty($dose['data_hora'])): ?>
                                    <?= esc($dose['checado_por']) ?>
                                    <span class="texto-secundario"><?= esc(formatDataHora($dose['data_hora'])) ?><?= !empty($dose['motivo']) ? ' · ' . esc($dose['motivo']) : '' ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="acoes-tabela">
                                <?php if ($dose['pode_checar']): ?>
                                    <?= form_open("medicamentos/checar", ["class" => "d-inline"]) ?>
                                        <input type="hidden" name="id_administracao" value="<?= esc($dose['id_administracao']) ?>">
                                        <input type="hidden" name="situacao" value="administrado">
                                        <button class="btn btn-brand btn-sm" type="submit">Checar</button>
                                    <?= form_close() ?>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-outline-secondary btn-sm btn-mais" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Não administrado">
                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i><span class="visually-hidden">Não administrado</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-3 menu-formulario">
                                            <?= form_open("medicamentos/checar") ?>
                                                <input type="hidden" name="id_administracao" value="<?= esc($dose['id_administracao']) ?>">
                                                <input type="hidden" name="situacao" value="nao_administrado">
                                                <label class="form-label" for="motivo_<?= esc($dose['id_administracao']) ?>">Dose não administrada</label>
                                                <select class="form-select form-select-sm mb-2" id="motivo_<?= esc($dose['id_administracao']) ?>" name="motivo" required>
                                                    <option value="">Motivo</option>
                                                    <?php foreach (getMotivosNaoAdministracao() as $motivo): ?>
                                                        <option value="<?= esc($motivo) ?>"><?= esc($motivo) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button class="btn btn-outline-danger btn-sm w-100" type="submit">Registrar</button>
                                            <?= form_close() ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="estado-vazio" colspan="5"><i class="fa fa-inbox" aria-hidden="true"></i>Nenhuma dose aprazada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($podePrescrever): ?>
    <?= form_open("medicamentos/prescrever") ?>

        <input type="hidden" name="id_atendimento" value="<?= esc($atendimento['id_atendimento']) ?>">

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Nova prescrição</h2>
                <span class="texto-ajuda">Prescritor: <?= esc(session()->get('usuarioNome')) ?></span>
            </div>

            <div class="admin-panel-body">
                <?php if (!empty($alertas)): ?>
                    <div class="alerta-caixa alerta-alergia mb-3">
                        <p class="alerta-paciente alerta-alergia mb-2"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><span><strong>Alerta de prescrição</strong></span></p>
                        <ul class="mb-2">
                            <?php foreach ($alertas as $alerta): ?>
                                <li><?= esc($alerta) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirma_alerta" name="confirma_alerta" value="1">
                            <label class="form-check-label" for="confirma_alerta">Revisei o alerta e mantenho a prescrição.</label>
                        </div>
                        <?= setMsgErrorCampo("confirma_alerta", $erros) ?>
                    </div>
                <?php elseif (!empty($atendimento['alergias'])): ?>
                    <p class="alerta-paciente alerta-alergia alerta-caixa mb-3"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        <span><strong>Confira antes de prescrever.</strong> Alergias: <?= esc($atendimento['alergias']) ?></span></p>
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <label for="medicamento" class="form-label campo-obrigatorio">Medicamento</label>
                        <input type="text" class="form-control <?= !empty($erros['medicamento']) ? 'is-invalid' : '' ?>" id="medicamento" name="medicamento" list="lista_medicamentos"
                            value="<?= setValue("medicamento", $dados) ?>" maxlength="180" autocomplete="off" required>
                        <datalist id="lista_medicamentos">
                            <?php foreach ($catalogo as $nome): ?>
                                <option value="<?= esc($nome) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                        <?= setMsgErrorCampo("medicamento", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="apresentacao" class="form-label">Apresentação</label>
                        <input type="text" class="form-control <?= !empty($erros['apresentacao']) ? 'is-invalid' : '' ?>" id="apresentacao" name="apresentacao"
                            placeholder="Ex.: Ampola 1 g/2 mL" value="<?= setValue("apresentacao", $dados) ?>" maxlength="150">
                        <?= setMsgErrorCampo("apresentacao", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label for="dose" class="form-label campo-obrigatorio">Dose</label>
                        <input type="text" class="form-control <?= !empty($erros['dose']) ? 'is-invalid' : '' ?>" id="dose" name="dose"
                            placeholder="Ex.: 500 mg" value="<?= setValue("dose", $dados) ?>" maxlength="80" required>
                        <?= setMsgErrorCampo("dose", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="via" class="form-label campo-obrigatorio">Via</label>
                        <select class="form-select <?= !empty($erros['via']) ? 'is-invalid' : '' ?>" id="via" name="via" required>
                            <option value=""              <?= (setValue("via", $dados) == ""              ? "selected" : "") ?>>Selecione</option>
                            <option value="Oral"          <?= (setValue("via", $dados) == "Oral"          ? "selected" : "") ?>>Oral</option>
                            <option value="Intravenosa"   <?= (setValue("via", $dados) == "Intravenosa"   ? "selected" : "") ?>>Intravenosa</option>
                            <option value="Intramuscular" <?= (setValue("via", $dados) == "Intramuscular" ? "selected" : "") ?>>Intramuscular</option>
                            <option value="Subcutânea"    <?= (setValue("via", $dados) == "Subcutânea"    ? "selected" : "") ?>>Subcutânea</option>
                        </select>
                        <?= setMsgErrorCampo("via", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="frequencia" class="form-label campo-obrigatorio">Frequência</label>
                        <select class="form-select <?= !empty($erros['frequencia']) ? 'is-invalid' : '' ?>" id="frequencia" name="frequencia" required>
                            <?php foreach (getFrequencias() as $chave => $frequencia): ?>
                                <option value="<?= esc($chave) ?>" <?= setValue("frequencia", $dados) == $chave ? "selected" : "" ?>><?= esc($frequencia) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= setMsgErrorCampo("frequencia", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="primeira_dose" class="form-label campo-obrigatorio">Primeira dose</label>
                        <input type="datetime-local" class="form-control <?= !empty($erros['primeira_dose']) ? 'is-invalid' : '' ?>" id="primeira_dose" name="primeira_dose"
                            value="<?= setValue("primeira_dose", $dados) ?>" required>
                        <?= setMsgErrorCampo("primeira_dose", $erros) ?>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="situacao" class="form-label campo-obrigatorio">Administração da 1ª dose</label>
                        <select class="form-select <?= !empty($erros['situacao']) ? 'is-invalid' : '' ?>" id="situacao" name="situacao" required>
                            <option value="prescrito" <?= setValue("situacao", $dados) === "prescrito" ? "selected" : "" ?>>A administrar pela enfermagem</option>
                            <option value="administrado" <?= setValue("situacao", $dados) === "administrado" ? "selected" : "" ?>>Já administrada por mim</option>
                        </select>
                        <?= setMsgErrorCampo("situacao", $erros) ?>
                    </div>
                    <div class="col-12 col-lg-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <input type="text" class="form-control <?= !empty($erros['observacoes']) ? 'is-invalid' : '' ?>" id="observacoes" name="observacoes" value="<?= setValue("observacoes", $dados) ?>" placeholder="Ex.: diluir em 100 mL de SF">
                        <?= setMsgErrorCampo("observacoes", $erros) ?>
                    </div>
                </div>
            </div>

            <div class="admin-panel-footer">
                <button class="btn btn-brand ms-auto" type="submit">Prescrever</button>
            </div>
        </section>

    <?= form_close() ?>
<?php endif; ?>

<?= $this->endSection() ?>
