<?php
require __DIR__ . '/backend/bootstrap.php';
$title = 'Главная';
$stats = Review::stats();
$slides = [
    ['slide1.jpg', 'Учебные автомобили'],
    ['slide2.jpg', 'Автодром'],
    ['slide3.jpg', 'Занятия с инструктором'],
    ['slide4.jpg', 'Теория'],
];
require __DIR__ . '/frontend/partials/header.php';
?>
<section class="hero">
  <h1>Научитесь водить уверенно</h1>
  <p>Запишитесь на практическое занятие онлайн: выберите категорию, дату и удобное время.</p>
</section>

<section class="slider" data-slider aria-label="Фотографии автошколы">
  <div class="slides">
    <?php foreach ($slides as [$file, $caption]): ?>
      <figure class="slide">
        <img src="<?= $assets ?>/img/<?= $file ?>" alt="<?= e($caption) ?>" width="1200" height="800">
        <figcaption><?= e($caption) ?></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
  <button class="slider-btn prev" data-prev aria-label="Назад">‹</button>
  <button class="slider-btn next" data-next aria-label="Вперёд">›</button>
  <div class="dots"></div>
</section>

<section class="rating card">
  <?php if ($stats['cnt'] > 0): ?>
    <div class="rating-num"><?= e($stats['avg']) ?></div>
    <div>
      <div class="stars" style="--p:<?= (float)$stats['avg'] / 5 * 100 ?>%" aria-hidden="true">★★★★★</div>
      <small>Средняя оценка по <?= (int)$stats['cnt'] ?> отзывам курсантов</small>
    </div>
  <?php else: ?>
    <div><strong>Отзывов пока нет</strong><br><small>Первую оценку можно оставить после проведённого занятия.</small></div>
  <?php endif; ?>
</section>

<?php if (!Auth::user()): ?>
  <a class="btn btn-block" href="<?= url('frontend/register.php') ?>">Создать аккаунт</a>
<?php elseif (!Auth::isAdmin()): ?>
  <a class="btn btn-block" href="<?= url('frontend/application_create.php') ?>">Записаться на занятие</a>
<?php endif; ?>
<?php require __DIR__ . '/frontend/partials/footer.php'; ?>
