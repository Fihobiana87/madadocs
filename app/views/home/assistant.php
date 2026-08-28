<section class="section">
    <div class="container">
        <div class="section-head" style="margin:0 auto var(--sp-6);text-align:center;max-width:60ch">
            <p class="eyebrow">Assistant MadaDocs</p>
            <h1>Une aide à la rédaction, quand vous en avez besoin</h1>
            <p class="text-muted">Décrivez votre situation ou collez un texte à améliorer. L’assistant reste facultatif : vous pouvez toujours rédiger vous-même.</p>
        </div>

        <?php if (!$aiAvailable): ?>
            <div class="alert alert-info" style="max-width:640px;margin:0 auto var(--sp-6)">
                <span>ℹ</span>
                <span>L’assistant IA n’est pas configuré pour le moment sur ce site. Vous pouvez tout de même créer vos documents normalement.</span>
            </div>
        <?php endif; ?>

        <div class="grid-3" style="grid-template-columns:1fr 1fr;max-width:960px;margin:0 auto">
            <div class="card" data-ai-panel>
                <h3>Générer un texte</h3>
                <p class="text-muted">Décrivez votre situation en une phrase.</p>
                <form action="<?= base_url('/assistant/generer') ?>" method="post" data-ai-form data-ai-result-target="#result-generer">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="situation">Votre situation</label>
                        <textarea class="input" id="situation" name="situation" rows="4" placeholder="Je veux demander un stage dans une entreprise informatique." required <?= $aiAvailable ? '' : 'disabled' ?>></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" <?= $aiAvailable ? '' : 'disabled' ?>>Générer</button>
                </form>
                <div id="result-generer" style="margin-top:var(--sp-4)"></div>
            </div>

            <div class="card" data-ai-panel>
                <h3>Améliorer un texte</h3>
                <p class="text-muted">Collez votre texte et choisissez un style.</p>
                <form action="<?= base_url('/assistant/ameliorer') ?>" method="post" data-ai-form data-ai-result-target="#result-ameliorer">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="text">Votre texte</label>
                        <textarea class="input" id="text" name="text" rows="4" placeholder="Collez ici la lettre ou le message à améliorer." required <?= $aiAvailable ? '' : 'disabled' ?>></textarea>
                    </div>
                    <div class="field">
                        <label for="style">Style souhaité</label>
                        <select class="input" id="style" name="style" <?= $aiAvailable ? '' : 'disabled' ?>>
                            <option value="simple">Simple</option>
                            <option value="professionnel" selected>Professionnel</option>
                            <option value="formel">Formel</option>
                            <option value="administratif">Administratif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" <?= $aiAvailable ? '' : 'disabled' ?>>Améliorer</button>
                </form>
                <div id="result-ameliorer" style="margin-top:var(--sp-4)"></div>
            </div>
        </div>
    </div>
</section>
