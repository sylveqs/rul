<?php
require __DIR__ . '/../backend/bootstrap.php';
$title = 'Вход';
$errors = takeErrors();
$old = takeOld();
require __DIR__ . '/partials/header.php';
?>
<h1>Вход</h1>
<form class="card" method="post" action="<?= url('backend/actions/login.php') ?>" novalidate>
  <?php if (isset($errors['common'])): ?><div class="alert"><?= e($errors['common']) ?></div><?php endif; ?>
  <?php
  field('login', 'Логин', $errors, $old);
  field('password', 'Пароль', $errors, $old, 'password');
  ?>
  <button class="btn btn-block" type="submit">Войти</button>
  <p class="alt"><a href="<?= url('frontend/register.php') ?>">Еще не зарегистрированы? Регистрация</a></p>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
