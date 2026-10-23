<?php
class User
{
    public static function loginExists(string $login): bool
    {
        return (bool)Database::value('SELECT COUNT(*) FROM users WHERE login = ?', [$login])
            || strcasecmp($login, config('admin')['login']) === 0;
    }

    public static function findByLogin(string $login): ?array
    {
        return Database::one('SELECT * FROM users WHERE login = ?', [$login]);
    }

    public static function create(array $d): int
    {
        Database::query(
            'INSERT INTO users (login, password_hash, full_name, birth_date, phone, email) VALUES (?,?,?,?,?,?)',
            [$d['login'], password_hash($d['password'], PASSWORD_DEFAULT), trim($d['full_name']),
             Validator::parseDate($d['birth_date']), $d['phone'], $d['email']]
        );
        return (int)Database::pdo()->lastInsertId();
    }
}
