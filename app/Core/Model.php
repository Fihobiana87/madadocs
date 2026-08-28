<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table = '';

    protected static function db(): PDO
    {
        return Database::connection();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return self::db()->query('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy)->fetchAll();
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . " WHERE {$column} = ? ORDER BY {$orderBy}");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function first(string $column, mixed $value): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . " WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $assignments = implode(', ', array_map(fn ($col) => "{$col} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = self::db()->prepare('UPDATE ' . static::$table . " SET {$assignments} WHERE id = ?");
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM ' . static::$table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM ' . static::$table)->fetchColumn();
    }
}
