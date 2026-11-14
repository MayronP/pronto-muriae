<div class="banner-paciente">
    <div class="banner-principal">
        <div class="banner-identificacao">
            <span class="avatar-paciente" aria-hidden="true"><?= esc(getIniciais(nomePaciente($paciente))) ?></span>
            <div>
                <p class="banner-nome"><?= esc(nomePaciente($paciente)) ?></p>
                <p class="banner-meta">
                    <?php if (!empty($paciente['data_nascimento'])): ?>
                        <span><?= esc(calculaIdade($paciente['data_nascimento'])) ?> (<?= esc(formatData($paciente['data_nascimento'])) ?>)</span>
                    <?php endif; ?>
                    <?php if (getDescricaoSexo($paciente['sexo'] ?? null) !== ''): ?>
                        <span><?= esc(getDescricaoSexo($paciente['sexo'])) ?></span>
                    <?php endif; ?>
                    <span>CPF <?= esc($paciente['cpf'] ?: 'não informado') ?></span>
                    <?php if (!empty($paciente['cns'])): ?>
                        <span>CNS <?= esc($paciente['cns']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($paciente['telefone'])): ?>
                        <span><?= esc($paciente['telefone']) ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php if (!empty($paciente['nome_mae'])): ?>
            <div class="text-end">
                <span class="dado-rotulo d-block">Mãe</span>
                <?= esc($paciente['nome_mae']) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php $alertas = alertasPaciente($paciente); ?>
    <?php if ($alertas !== ''): ?>
        <div class="banner-alertas"><?= $alertas ?></div>
    <?php endif; ?>
</div>
