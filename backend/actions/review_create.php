<?php
require __DIR__ . '/../bootstrap.php';
requirePost('frontend/applications.php');
$u = Auth::requireUser();
$errors = Validator::review($_POST);
if ($errors) { flash('error', reset($errors)); redirect('frontend/applications.php'); }
$err = Review::create($u['id'], (int)($_POST['application_id'] ?? 0), (int)$_POST['rating'], $_POST['comment'] ?? '');
$err ? flash('error', $err) : flash('success', 'Спасибо за отзыв!');
redirect('frontend/applications.php');
