<?php
session_start();
$GLOBALS['config'] = require __DIR__ . '/config.php';
spl_autoload_register(function ($c) {
    $f = __DIR__ . '/classes/' . $c . '.php';
    if (is_file($f)) require $f;
});

$dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$dir = preg_replace('~/(frontend|backend/actions)$~', '', $dir);
define('BASE', $dir);

function config(string $k) { return $GLOBALS['config'][$k]; }
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $p): string { return BASE . '/' . ltrim($p, '/'); }
function redirect(string $p): void { header('Location: ' . url($p)); exit; }
function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }
function takeFlash(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function backWith(string $page, array $errors, array $old = []): void
{
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    redirect($page);
}
function takeErrors(): array { $v = $_SESSION['errors'] ?? []; unset($_SESSION['errors']); return $v; }
function takeOld(): array { $v = $_SESSION['old'] ?? []; unset($_SESSION['old']); return $v; }
function requirePost(string $back): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($back);
}
