<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static $pdo = null;

    public static function connection()
    {
        if (static::$pdo) {
            return static::$pdo;
        }

        try {
            $dsn = 'pgsql:host=localhost;dbname=products_2023';
            static::$pdo = new PDO($dsn, 'postgres', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);

            return static::$pdo;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}


