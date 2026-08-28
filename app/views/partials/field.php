<?php
/** @var array $field */
$id = 'f_' . $field['name'];
?>
<div class="field">
    <label for="<?= e($id) ?>"><?= e($field['label']) ?><?php if (empty($field['required'])): ?> <span class="text-muted">(facultatif)</span><?php endif; ?></label>

    <?php if ($field['type'] === 'textarea'): ?>
        <textarea class="input" id="<?= e($id) ?>" name="<?= e($field['name']) ?>" <?= !empty($field['required']) ? 'required' : '' ?> placeholder="<?= e($field['placeholder'] ?? '') ?>"></textarea>
    <?php else: ?>
        <input class="input" id="<?= e($id) ?>" name="<?= e($field['name']) ?>" type="<?= e($field['type']) ?>" <?= !empty($field['required']) ? 'required' : '' ?> placeholder="<?= e($field['placeholder'] ?? '') ?>">
    <?php endif; ?>
</div>
