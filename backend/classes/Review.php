<?php
class Review
{
    /** Возвращает текст ошибки или null при успехе. */
    public static function create(int $userId, int $appId, int $rating, string $comment): ?string
    {
        $a = Database::one('SELECT * FROM applications WHERE id=? AND user_id=?', [$appId, $userId]);
        if (!$a) return 'Заявка не найдена';
        if ((int)$a['status_id'] !== Application::DONE) return 'Отзыв можно оставить только после проведённого занятия';
        if (Database::value('SELECT COUNT(*) FROM reviews WHERE application_id=?', [$appId])) return 'Отзыв уже оставлен';
        Database::query('INSERT INTO reviews (application_id, user_id, rating, comment) VALUES (?,?,?,?)',
            [$appId, $userId, $rating, trim($comment)]);
        return null;
    }

    public static function stats(): array
    {
        return Database::one('SELECT ROUND(AVG(rating),1) AS avg, COUNT(*) AS cnt FROM reviews') ?: ['avg' => null, 'cnt' => 0];
    }
}
