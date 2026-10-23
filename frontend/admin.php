<?php
require __DIR__ . '/../backend/bootstrap.php';
Auth::requireAdmin();
$title = 'Панель администратора';

$statuses = Application::dict('statuses');
$statusId = (int)($_GET['status'] ?? 0);
$dateRaw = trim($_GET['date'] ?? '');
$date = $dateRaw !== '' ? Validator::parseDate($dateRaw) : null;
$dateError = $dateRaw !== '' && !$date ? 'Дата в формате ДД.ММ.ГГГГ' : null;

$filter = ['status_id' => $statusId, 'date' => $date];
$perPage = config('per_page');
$total = Application::count($filter);
$pages = max(1, (int)ceil($total / $perPage));
$page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
$apps = Application::list($filter, $perPage, ($page - 1) * $perPage);

$today = Application::countOnDate(date('Y-m-d'));
$tomorrow = Application::countOnDate(date('Y-m-d', strtotime('+1 day')));
$changed = $_SESSION['changed_id'] ?? null;
unset($_SESSION['changed_id']);

$query = fn(array $extra = []) => http_build_query(array_filter(
    array_merge(['status' => $statusId, 'date' => $dateRaw, 'page' => $page], $extra)));
$btnText = [Application::CONFIRMED => 'Подтвердить', Application::DONE => 'Занятие проведено', Application::CANCELED => 'Отменить'];
require __DIR__ . '/partials/header.php';
?>
<h1>Заявки</h1>
<div class="summary">
  <div class="card"><b><?= $today ?></b><span>занятий сегодня</span></div>
  <div class="card"><b><?= $tomorrow ?></b><span>занятий завтра</span></div>
</div>

<form class="card filters" method="get">
  <div class="field"><label for="status">Статус</label>
    <select class="input" id="status" name="status">
      <option value="0">Все</option>
      <?php foreach ($statuses as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $statusId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select></div>
  <div class="field"><label for="date">Дата</label>
    <input class="input<?= $dateError ? ' is-invalid' : '' ?>" id="date" name="date" value="<?= e($dateRaw) ?>" placeholder="ДД.ММ.ГГГГ">
    <?php if ($dateError): ?><div class="error"><?= e($dateError) ?></div><?php endif; ?></div>
  <button class="btn btn-sm" type="submit">Показать</button>
  <a class="btn btn-sm btn-ghost" href="admin.php">Сбросить</a>
</form>

<?php if (!$apps): ?><div class="card empty">Заявок по выбранным условиям нет.</div><?php endif; ?>
<?php foreach ($apps as $i => $a): ?>
  <article class="card app<?= $changed == $a['id'] ? ' changed' : '' ?>" style="--i:<?= $i ?>">
    <div class="app-head">
      <strong>№<?= (int)$a['id'] ?> · <?= e(date('d.m.Y', strtotime($a['lesson_date']))) ?>, <?= e($a['time_slot']) ?></strong>
      <span class="badge s<?= (int)$a['status_id'] ?>"><?= e($a['status']) ?></span>
    </div>
    <div><?= e($a['full_name']) ?> · <?= e($a['phone']) ?></div>
    <div class="muted">Категория <?= e($a['category']) ?> · <?= e($a['transmission']) ?> КПП</div>
    <?php if (!empty(Application::TRANSITIONS[$a['status_id']])): ?>
      <div class="actions">
        <?php foreach (Application::TRANSITIONS[$a['status_id']] as $to): ?>
          <form method="post" action="<?= url('backend/actions/admin_status.php') ?>">
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <input type="hidden" name="status_id" value="<?= $to ?>">
            <input type="hidden" name="qs" value="<?= e($query()) ?>">
            <button class="btn btn-sm <?= $to === Application::CANCELED ? 'btn-danger' : '' ?>"><?= e($btnText[$to]) ?></button>
          </form>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </article>
<?php endforeach; ?>

<?php if ($pages > 1): ?>
  <nav class="pager" aria-label="Страницы">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
      <a class="<?= $p === $page ? 'active' : '' ?>" href="?<?= e($query(['page' => $p])) ?>"><?= $p ?></a>
    <?php endfor; ?>
  </nav>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
