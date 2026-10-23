<?php
/** Обёртка над PDO: одно соединение, только подготовленные запросы. */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (!self::$pdo) {
            $c = config('db');
            self::$pdo = new PDO(
                "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES => false]
            );
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function one(string $sql, array $p = []): ?array { return self::query($sql, $p)->fetch() ?: null; }
    public static function all(string $sql, array $p = []): array { return self::query($sql, $p)->fetchAll(); }
    public static function value(string $sql, array $p = []) { return self::query($sql, $p)->fetchColumn(); }
}
