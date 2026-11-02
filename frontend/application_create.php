<?php
require __DIR__ . '/../backend/bootstrap.php';
$u = Auth::requireUser();
if ($u['role'] === 'admin') redirect('frontend/admin.php');
$title = 'Запись на занятие';
$errors = takeErrors();
$old = takeOld() + ['duration_minutes' => 90];
require __DIR__ . '/partials/header.php';
?>
<h1>Запись на занятие</h1>
<form class="card" method="post" action="<?= url('backend/actions/application_create.php') ?>" novalidate>
  <?php
  selectField('category_id', 'Категория', Application::dict('categories'), 'label', $errors, $old);
  field('lesson_date', 'Дата занятия', $errors, $old, 'text', 'placeholder="ДД.ММ.ГГГГ" inputmode="numeric"');
  selectField('slot_id', 'Время', Application::dict('slots'), 'label', $errors, $old);
  selectField('transmission_id', 'Коробка передач', Application::dict('transmissions'), 'label', $errors, $old);
  selectField('duration_minutes', 'Длительность', Application::durations(), 'label', $errors, $old);
  ?>
  <button class="btn btn-block" type="submit">Записаться</button>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
