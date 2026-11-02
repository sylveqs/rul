<?php
require __DIR__ . '/../bootstrap.php';
requirePost('frontend/application_create.php');
$u = Auth::requireUser();
if ($u['role'] === 'admin') redirect('frontend/admin.php');
$errors = Application::create($u['id'], $_POST);
if ($errors) backWith('frontend/application_create.php', $errors, $_POST);
flash('success', 'Заявка отправлена администратору');
redirect('frontend/applications.php');
