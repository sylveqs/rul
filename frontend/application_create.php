<?php
require __DIR__ . '/../backend/bootstrap.php';
$u = Auth::requireUser();
if ($u['role'] === 'admin') redirect('frontend/admin.php');
$title = 'Запись на занятие';
$errors = takeErrors();
$old = takeOld();
require __DIR__ . '/partials/header.php';
?>
<h1>Запись на занятие</h1>
<form class="card" method="post" action="<?= url('backend/actions/application_create.php') ?>" novalidate>
  <?php
  selectField('category_id', 'Категория', Application::dict('categories'), 'code', $errors, $old);
  field('lesson_date', 'Дата занятия', $errors, $old, 'text', 'placeholder="ДД.ММ.ГГГГ" inputmode="numeric"');
  selectField('slot_id', 'Время', Application::dict('slots'), 'time_slot', $errors, $old);
  selectField('transmission_id', 'Коробка передач', Application::dict('transmissions'), 'name', $errors, $old);
  ?>
  <button class="btn btn-block" type="submit">Записаться</button>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
