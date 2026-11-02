<?php
/** Шапка: меню, flash-сообщения. Перед подключением страница задаёт $title. */
$user = Auth::user();
$flashes = takeFlash();
$errors = $errors ?? [];
$old = $old ?? [];

function field(string $name, string $label, array $errors, array $old, string $type = 'text', string $extra = ''): void
{
    $bad = isset($errors[$name]) ? ' is-invalid' : '';
    $val = $type === 'password' ? '' : e($old[$name] ?? '');
    echo "<div class=\"field\"><label for=\"$name\">" . e($label) . "</label>"
       . "<input class=\"input$bad\" id=\"$name\" name=\"$name\" type=\"$type\" value=\"$val\" required $extra>";
    if (isset($errors[$name])) echo '<div class="error">' . e($errors[$name]) . '</div>';
    echo '</div>';
}

function selectField(string $name, string $label, array $items, string $textKey, array $errors, array $old): void
{
    $bad = isset($errors[$name]) ? ' is-invalid' : '';
    echo "<div class=\"field\"><label for=\"$name\">" . e($label) . "</label><select class=\"input$bad\" id=\"$name\" name=\"$name\" required>"
       . '<option value="">Выберите…</option>';
    foreach ($items as $i) {
        $sel = ($old[$name] ?? '') == $i['id'] ? ' selected' : '';
        echo '<option value="' . (int)$i['id'] . "\"$sel>" . e($i[$textKey]) . '</option>';
    }
    echo '</select>';
    if (isset($errors[$name])) echo '<div class="error">' . e($errors[$name]) . '</div>';
    echo '</div>';
}
$assets = url('frontend/assets');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Руль') ?> — автошкола «Руль»</title>
<link rel="stylesheet" href="<?= $assets ?>/css/style.css">
</head>
<body>
<header class="site-header">
  <a class="logo" href="<?= url('index.php') ?>"><img src="<?= $assets ?>/img/logo.svg" alt="" width="28" height="28">Руль</a>
  <button class="burger" aria-label="Меню" aria-expanded="false" data-burger><span></span><span></span><span></span></button>
  <nav class="menu" id="menu">
    <?php if (!$user): ?>
      <a href="<?= url('frontend/login.php') ?>">Вход</a>
      <a class="btn btn-sm" href="<?= url('frontend/register.php') ?>">Регистрация</a>
    <?php elseif ($user['role'] === 'admin'): ?>
      <a href="<?= url('frontend/admin.php') ?>">Панель</a>
      <a class="btn btn-sm btn-dark" href="<?= url('backend/actions/logout.php') ?>">Выйти</a>
    <?php else: ?>
      <a href="<?= url('frontend/applications.php') ?>">Мои занятия</a>
      <a href="<?= url('frontend/application_create.php') ?>">Записаться</a>
      <a class="btn btn-sm btn-dark" href="<?= url('backend/actions/logout.php') ?>">Выйти</a>
    <?php endif; ?>
  </nav>
</header>
<div class="road-line"></div>
<div id="toasts" data-flash="<?= e(json_encode($flashes, JSON_UNESCAPED_UNICODE)) ?>"></div>
<main class="page">
