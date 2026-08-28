<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): static
    {
        if (trim((string) ($this->data[$field] ?? '')) === '') {
            $this->errors[$field] = "{$label} est obligatoire.";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): static
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} n'est pas une adresse valide.";
        }
        return $this;
    }

    public function minLength(string $field, int $length, string $label): static
    {
        $value = $this->data[$field] ?? '';
        if (mb_strlen($value) > 0 && mb_strlen($value) < $length) {
            $this->errors[$field] = "{$label} doit contenir au moins {$length} caractères.";
        }
        return $this;
    }

    public function maxLength(string $field, int $length, string $label): static
    {
        $value = $this->data[$field] ?? '';
        if (mb_strlen($value) > $length) {
            $this->errors[$field] = "{$label} ne doit pas dépasser {$length} caractères.";
        }
        return $this;
    }

    public function matches(string $field, string $other, string $label): static
    {
        if (($this->data[$field] ?? '') !== ($this->data[$other] ?? '')) {
            $this->errors[$field] = "{$label} ne correspond pas.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
