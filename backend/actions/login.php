<?php
require __DIR__ . '/../bootstrap.php';
requirePost('frontend/login.php');
$login = trim($_POST['login'] ?? '');
$pass = $_POST['password'] ?? '';
$errors = [];
if ($login === '') $errors['login'] = 'Введите логин';
if ($pass === '') $errors['password'] = 'Введите пароль';
if (!$errors && !Auth::login($login, $pass)) $errors['common'] = 'Неверный логин или пароль';
if ($errors) backWith('frontend/login.php', $errors, ['login' => $login]);
flash('success', 'Добро пожаловать!');
redirect(Auth::isAdmin() ? 'frontend/admin.php' : 'frontend/applications.php');
