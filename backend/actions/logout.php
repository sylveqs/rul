<?php
require __DIR__ . '/../bootstrap.php';
Auth::logout();
flash('success', 'Вы вышли из аккаунта');
redirect('index.php');
