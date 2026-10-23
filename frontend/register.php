<?php
require __DIR__ . '/../backend/bootstrap.php';
$title = 'Регистрация';
$errors = takeErrors();
$old = takeOld();
require __DIR__ . '/partials/header.php';
?>
<h1>Регистрация</h1>
<form class="card" method="post" action="<?= url('backend/actions/register.php') ?>" autocomplete="off" novalidate>
  <?php
  field('login', 'Логин', $errors, $old, 'text', 'pattern="[A-Za-z0-9]{6,}" placeholder="Латиница и цифры, от 6 символов"');
  field('password', 'Пароль', $errors, $old, 'password', 'minlength="8" placeholder="От 8 символов"');
  field('full_name', 'ФИО', $errors, $old, 'text', 'placeholder="Иванов Иван Иванович"');
  field('birth_date', 'Дата рождения', $errors, $old, 'text', 'placeholder="ДД.ММ.ГГГГ" inputmode="numeric"');
  field('phone', 'Телефон', $errors, $old, 'tel', 'placeholder="8(999)123-45-67"');
  field('email', 'Электронная почта', $errors, $old, 'email', 'placeholder="name@mail.ru"');
  ?>
  <button class="btn btn-block" type="submit">Создать пользователя</button>
  <p class="alt"><a href="<?= url('frontend/login.php') ?>">Уже есть аккаунт? Вход</a></p>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
