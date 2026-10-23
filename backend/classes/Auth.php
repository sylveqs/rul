<?php
class Auth
{
    public static function login(string $login, string $password): bool
    {
        $adm = config('admin');
        if ($login === $adm['login']) {
            if (!hash_equals($adm['password'], $password)) return false;
            $_SESSION['user'] = ['id' => 0, 'login' => $login, 'name' => 'Диспетчер', 'role' => 'admin'];
            return true;
        }
        $u = User::findByLogin($login);
        if (!$u || !password_verify($password, $u['password_hash'])) return false;
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'login' => $u['login'], 'name' => $u['full_name'], 'role' => 'user'];
        return true;
    }

    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function isAdmin(): bool { return (self::user()['role'] ?? '') === 'admin'; }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function requireUser(): array
    {
        $u = self::user();
        if (!$u) { flash('error', 'Войдите, чтобы продолжить'); redirect('frontend/login.php'); }
        return $u;
    }

    public static function requireAdmin(): array
    {
        $u = self::requireUser();
        if ($u['role'] !== 'admin') { flash('error', 'Доступ только для администратора'); redirect('index.php'); }
        return $u;
    }
}
