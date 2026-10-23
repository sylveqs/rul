<?php
require __DIR__ . '/../backend/bootstrap.php';
$u = Auth::requireUser();
if ($u['role'] === 'admin') redirect('frontend/admin.php');
$title = 'Мои занятия';
$apps = Application::list(['user_id' => $u['id']]);
require __DIR__ . '/partials/header.php';
?>
<h1>Мои занятия</h1>
<?php if (!$apps): ?>
  <div class="card empty">Пока нет ни одной заявки.<br><a class="btn" href="<?= url('frontend/application_create.php') ?>">Записаться на занятие</a></div>
<?php endif; ?>
<?php foreach ($apps as $i => $a): ?>
  <article class="card app" style="--i:<?= $i ?>">
    <div class="app-head">
      <strong><?= e(date('d.m.Y', strtotime($a['lesson_date']))) ?>, <?= e($a['time_slot']) ?></strong>
      <span class="badge s<?= (int)$a['status_id'] ?>"><?= e($a['status']) ?></span>
    </div>
    <div class="muted">Категория <?= e($a['category']) ?> · <?= e($a['transmission']) ?> КПП</div>
    <?php if ($a['review_id']): ?>
      <div class="stars" style="--p:<?= (int)$a['rating'] * 20 ?>%" title="Ваша оценка: <?= (int)$a['rating'] ?>">★★★★★</div>
    <?php elseif ((int)$a['status_id'] === Application::DONE): ?>
      <form class="review" method="post" action="<?= url('backend/actions/review_create.php') ?>">
        <input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">
        <label>Оценка инструктора
          <select class="input" name="rating" required>
            <?php for ($r = 5; $r >= 1; $r--): ?><option value="<?= $r ?>"><?= $r ?></option><?php endfor; ?>
          </select>
        </label>
        <textarea class="input" name="comment" maxlength="500" rows="2" placeholder="Отзыв об инструкторе"></textarea>
        <button class="btn btn-sm" type="submit">Оставить отзыв</button>
      </form>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
