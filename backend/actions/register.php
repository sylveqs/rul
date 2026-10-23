<?php
require __DIR__ . '/../bootstrap.php';
requirePost('frontend/register.php');
$d = $_POST;
$errors = Validator::register($d);
if ($errors) { unset($d['password']); backWith('frontend/register.php', $errors, $d); }
User::create($d);
flash('success', 'Аккаунт создан. Теперь войдите');
redirect('frontend/login.php');
