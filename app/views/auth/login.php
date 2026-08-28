<section class="section" style="max-width:440px;margin:0 auto">
    <div class="container" style="padding:0">
        <p class="eyebrow">Bon retour</p>
        <h1>Connexion</h1>

        <form method="post" action="<?= base_url('/connexion') ?>" class="card" novalidate>
            <?= csrf_field() ?>

            <div class="field">
                <label for="email">Email</label>
                <input class="input <?= isset($errors['email']) ? 'has-error' : '' ?>" id="email" name="email" type="email" value="<?= e(old('email')) ?>" required autocomplete="email">
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input class="input <?= isset($errors['email']) ? 'has-error' : '' ?>" id="password" name="password" type="password" required autocomplete="current-password">
                <?php if (isset($errors['email'])): ?><p class="error-msg"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
        </form>

        <p class="text-muted" style="text-align:center;margin-top:16px">
            Pas encore de compte ? <a href="<?= base_url('/inscription') ?>">Inscrivez-vous</a>
        </p>
    </div>
</section>
