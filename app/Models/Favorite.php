<?php

namespace App\Models;

use App\Core\Model;

class Favorite extends Model
{
    protected static string $table = 'favorites';

    public static function isFavorite(int $userId, int $documentId): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND document_id = ?');
        $stmt->execute([$userId, $documentId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function toggle(int $userId, int $documentId): bool
    {
        if (self::isFavorite($userId, $documentId)) {
            $stmt = self::db()->prepare('DELETE FROM favorites WHERE user_id = ? AND document_id = ?');
            $stmt->execute([$userId, $documentId]);
            return false;
        }

        self::create([
            'user_id' => $userId,
            'document_id' => $documentId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public static function forUser(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT d.* FROM favorites f JOIN documents d ON d.id = f.document_id
             WHERE f.user_id = ? ORDER BY f.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
