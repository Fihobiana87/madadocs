<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = config('database');

        try {
            if ($config['driver'] === 'sqlite') {
                $dsn = 'sqlite:' . $config['sqlite_path'];
                $pdo = new PDO($dsn);
                $pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
                $pdo = new PDO($dsn, $config['user'], $config['pass']);
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            Logger::error('Connexion base de donnees impossible: ' . $e->getMessage());
            throw new \RuntimeException('database_unavailable');
        }

        return self::$instance = $pdo;
    }
}
