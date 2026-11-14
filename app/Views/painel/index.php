<?= $this->extend('templates/layout_publico') ?>

<?= $this->section("head") ?>
    <meta http-equiv="refresh" content="10">
<?= $this->endSection() ?>

<?= $this->section("classeBody") ?>painel-tv<?= $this->endSection() ?>

<?= $this->section("main") ?>

<header class="painel-cabecalho">
    <div class="d-flex align-items-center gap-3">
        <img src="<?= base_url('assets/img/muriae.png') ?>" alt="">
        <div>
            <h1 class="h3 mb-0">Muriaé Pronto</h1>
            <span class="opacity-75">Pronto Atendimento Hospitalar</span>
        </div>
    </div>
    <div class="painel-relogio" aria-label="Horário"><?= date("H:i") ?></div>
</header>

<main>
    <?php if (!empty($chamadas)): ?>
        <section class="painel-chamada-atual" aria-live="polite">
            <p class="rotulo mb-0">Chamando</p>
            <p class="painel-nome"><?= esc(getNomePainel($chamadas[0])) ?></p>
            <p class="painel-local mb-2"><i class="fa fa-arrow-right me-2" aria-hidden="true"></i><?= esc($chamadas[0]['local']) ?></p>
            <p class="text-muted mb-0">Chamado às <?= esc(formatHora($chamadas[0]['data_hora'])) ?></p>
        </section>

        <?php if (count($chamadas) > 1): ?>
            <h2 class="h5 mt-4 mb-0 px-4 ms-2">Chamadas anteriores</h2>
            <div class="painel-lista">
                <?php foreach (array_slice($chamadas, 1) as $chamada): ?>
                    <div>
                        <strong><?= esc(getNomePainel($chamada)) ?></strong>
                        <span><?= esc($chamada['local']) ?> · <?= esc(formatHora($chamada['data_hora'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <section class="painel-chamada-atual">
            <p class="rotulo mb-0">Bem-vindo</p>
            <p class="painel-nome">Aguarde ser chamado</p>
            <p class="text-muted mb-0">Seu nome aparecerá nesta tela com o local de atendimento.</p>
        </section>
    <?php endif; ?>

    <section class="px-4 mx-2 pb-4">
        <h2 class="h5 mt-4">O atendimento segue a gravidade, não a ordem de chegada</h2>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($classificacoes as $classificacao): ?>
                <span class="badge-risco <?= esc(getClasseRisco($classificacao['cor'])) ?>">
                    <?= esc($classificacao['nome']) ?> · <?= $classificacao['tempo_alvo_minutos'] == 0 ? 'imediato' : 'até ' . esc($classificacao['tempo_alvo_minutos']) . ' min' ?>
                </span>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?= $this->endSection() ?>
