<section class="section" style="max-width:440px;margin:0 auto">
    <div class="container" style="padding:0">
        <p class="eyebrow">Compte gratuit</p>
        <h1>Créer un compte</h1>
        <p class="text-muted">Débloquez l’historique, les favoris et vos documents enregistrés. Créer un document reste possible sans compte.</p>

        <form method="post" action="<?= base_url('/inscription') ?>" class="card" novalidate>
            <?= csrf_field() ?>

            <div class="field">
                <label for="name">Nom complet</label>
                <input class="input <?= isset($errors['name']) ? 'has-error' : '' ?>" id="name" name="name" type="text" value="<?= e(old('name')) ?>" required autocomplete="name">
                <?php if (isset($errors['name'])): ?><p class="error-msg"><?= e($errors['name']) ?></p><?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input class="input <?= isset($errors['email']) ? 'has-error' : '' ?>" id="email" name="email" type="email" value="<?= e(old('email')) ?>" required autocomplete="email">
                <?php if (isset($errors['email'])): ?><p class="error-msg"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input class="input <?= isset($errors['password']) ? 'has-error' : '' ?>" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
                <p class="hint">8 caractères minimum.</p>
                <?php if (isset($errors['password'])): ?><p class="error-msg"><?= e($errors['password']) ?></p><?php endif; ?>
            </div>

            <div class="field">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input class="input <?= isset($errors['password_confirm']) ? 'has-error' : '' ?>" id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password">
                <?php if (isset($errors['password_confirm'])): ?><p class="error-msg"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Créer mon compte</button>
        </form>

        <p class="text-muted" style="text-align:center;margin-top:16px">
            Déjà un compte ? <a href="<?= base_url('/connexion') ?>">Connectez-vous</a>
        </p>
    </div>
</section>
