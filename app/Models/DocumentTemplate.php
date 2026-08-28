<?php

namespace App\Models;

use App\Core\Model;

class DocumentTemplate extends Model
{
    protected static string $table = 'documents';

    public static function findBySlug(string $slug): ?array
    {
        return self::first('slug', $slug);
    }

    public static function activeByCategory(int $categoryId): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM documents WHERE category_id = ? AND is_active = 1 ORDER BY name ASC'
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function popular(int $limit = 6): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM documents WHERE is_active = 1 ORDER BY usage_count DESC, name ASC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(string $term): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM documents WHERE is_active = 1 AND (name LIKE ? OR description LIKE ? OR keywords LIKE ?) ORDER BY usage_count DESC'
        );
        $needle = '%' . $term . '%';
        $stmt->execute([$needle, $needle, $needle]);
        return $stmt->fetchAll();
    }

    public static function allWithCategory(): array
    {
        return self::db()->query(
            'SELECT d.*, c.name AS category_name, c.slug AS category_slug
             FROM documents d JOIN categories c ON c.id = d.category_id
             ORDER BY c.position ASC, d.name ASC'
        )->fetchAll();
    }

    public static function incrementUsage(int $id): void
    {
        self::db()->prepare('UPDATE documents SET usage_count = usage_count + 1 WHERE id = ?')->execute([$id]);
    }

    public static function fields(array $template): array
    {
        return json_decode($template['fields_schema'] ?? '[]', true) ?: [];
    }
}
