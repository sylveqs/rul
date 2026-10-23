<?php
/** Правила валидации полей. */
class Validator
{
    public static function parseDate(string $s): ?string
    {
        if (!preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', trim($s), $m) || !checkdate((int)$m[2], (int)$m[1], (int)$m[3])) {
            return null;
        }
        return "$m[3]-$m[2]-$m[1]";
    }

    public static function register(array $d): array
    {
        $e = [];
        foreach (['login', 'password', 'full_name', 'birth_date', 'phone', 'email'] as $f) {
            if (trim($d[$f] ?? '') === '') $e[$f] = 'Заполните это поле';
        }
        if (!isset($e['login']) && !preg_match('/^[A-Za-z0-9]{6,}$/', $d['login'])) {
            $e['login'] = 'Только латиница и цифры, не менее 6 символов';
        } elseif (!isset($e['login']) && User::loginExists($d['login'])) {
            $e['login'] = 'Такой логин уже занят';
        }
        if (!isset($e['password']) && mb_strlen($d['password']) < 8) $e['password'] = 'Пароль — не менее 8 символов';
        if (!isset($e['full_name']) && !preg_match('/^[А-Яа-яЁё\s]+$/u', $d['full_name'])) {
            $e['full_name'] = 'Только кириллица и пробелы';
        }
        if (!isset($e['birth_date'])) {
            $sql = self::parseDate($d['birth_date']);
            if (!$sql) $e['birth_date'] = 'Введите дату в формате ДД.ММ.ГГГГ';
            elseif ($sql > date('Y-m-d', strtotime('-16 years'))) $e['birth_date'] = 'Регистрация с 16 лет';
        }
        if (!isset($e['phone']) && !preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $d['phone'])) {
            $e['phone'] = 'Формат: 8(XXX)XXX-XX-XX';
        }
        if (!isset($e['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e['email'] = 'Некорректный e-mail';
        return $e;
    }

    public static function review(array $d): array
    {
        $e = [];
        $r = $d['rating'] ?? '';
        if (!ctype_digit((string)$r) || $r < 1 || $r > 5) $e['rating'] = 'Оценка — число от 1 до 5';
        if (mb_strlen($d['comment'] ?? '') > 500) $e['comment'] = 'Не более 500 символов';
        return $e;
    }
}
