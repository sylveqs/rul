# Руль — портал записи на практические занятия в автошколе

Стек: PHP 8 (ООП, PDO), MySQL/MariaDB, чистые HTML/CSS/JS без внешних библиотек и CDN.

## Запуск
1. Импортировать `backend/database/dump.sql` (phpMyAdmin → Импорт).
2. При необходимости поправить доступ к БД в `backend/config.php`.
3. Положить проект в `htdocs`/`www` (OpenServer, XAMPP) или `php -S localhost:8000` в корне.
4. Администратор: `Admin` / `Rul2026`.

## Структура
`index.php` — главная (слайдер, средняя оценка); `frontend/` — страницы; `backend/classes` — классы;
`backend/actions` — обработчики форм; `backend/database` — дамп и ER-диаграмма.
