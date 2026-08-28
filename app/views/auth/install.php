<section class="section" style="max-width:460px;margin:0 auto">
    <div class="container" style="padding:0">
        <p class="eyebrow">Installation</p>
        <h1>Créer le compte administrateur</h1>
        <p class="text-muted">Cette page n’apparaît qu’une seule fois, tant qu’aucun compte administrateur n’existe. Choisissez un mot de passe fort : il ne pourra plus être créé par cette page ensuite.</p>

        <form method="post" action="<?= base_url('/admin/installation') ?>" class="card" novalidate>
            <?= csrf_field() ?>

            <div class="field">
                <label for="name">Votre nom</label>
                <input class="input <?= isset($errors['name']) ? 'has-error' : '' ?>" id="name" name="name" type="text" value="<?= e(old('name')) ?>" required>
            </div>

            <div class="field">
                <label for="email">Email administrateur</label>
                <input class="input <?= isset($errors['email']) ? 'has-error' : '' ?>" id="email" name="email" type="email" value="<?= e(old('email')) ?>" required>
                <?php if (isset($errors['email'])): ?><p class="error-msg"><?= e($errors['email']) ?></p><?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input class="input <?= isset($errors['password']) ? 'has-error' : '' ?>" id="password" name="password" type="password" minlength="10" required>
                <p class="hint">10 caractères minimum.</p>
                <?php if (isset($errors['password'])): ?><p class="error-msg"><?= e($errors['password']) ?></p><?php endif; ?>
            </div>

            <div class="field">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input class="input" id="password_confirm" name="password_confirm" type="password" required>
                <?php if (isset($errors['password_confirm'])): ?><p class="error-msg"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Créer le compte administrateur</button>
        </form>
    </div>
</section>
