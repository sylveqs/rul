<?php
require __DIR__ . '/../bootstrap.php';
requirePost('frontend/admin.php');
Auth::requireAdmin();
$id = (int)($_POST['id'] ?? 0);
$err = Application::changeStatus($id, (int)($_POST['status_id'] ?? 0));
$err ? flash('error', $err) : flash('success', "Статус заявки №$id изменён");
$_SESSION['changed_id'] = $err ? null : $id;
redirect('frontend/admin.php' . (!empty($_POST['qs']) ? '?' . preg_replace('/[^\w=&%.\-]/', '', $_POST['qs']) : ''));
