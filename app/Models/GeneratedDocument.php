<?php

namespace App\Models;

use App\Core\Model;

class GeneratedDocument extends Model
{
    protected static string $table = 'generated_documents';

    public static function recentForUser(int $userId, int $limit = 10): array
    {
        $stmt = self::db()->prepare(
            'SELECT g.*, d.name AS document_name, d.slug AS document_slug
             FROM generated_documents g JOIN documents d ON d.id = g.document_id
             WHERE g.user_id = ? ORDER BY g.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function allWithDetails(int $limit = 100): array
    {
        $stmt = self::db()->prepare(
            'SELECT g.*, d.name AS document_name, u.name AS user_name
             FROM generated_documents g
             JOIN documents d ON d.id = g.document_id
             LEFT JOIN users u ON u.id = g.user_id
             ORDER BY g.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
