<?php
/** Заявки на практические занятия. */
class Application
{
    const NEW = 1, CONFIRMED = 2, DONE = 3, CANCELED = 4;
    const TRANSITIONS = [
        self::NEW => [self::CONFIRMED, self::CANCELED],
        self::CONFIRMED => [self::DONE, self::CANCELED],
    ];

    const DURATIONS = [45, 90, 120];

    /** Справочники в виде [id, label] для списков выбора. */
    public static function dict(string $table): array
    {
        $label = ['categories' => "CONCAT(name, ' — ', description)", 'slots' => 'time_slot',
                  'transmissions' => 'name', 'statuses' => 'name'][$table] ?? null;
        return $label ? Database::all("SELECT id, $label AS label FROM $table ORDER BY id") : [];
    }

    public static function durations(): array
    {
        return array_map(fn($m) => ['id' => $m, 'label' => $m . ' мин'], self::DURATIONS);
    }

    public static function slotTaken(string $date, int $slot, int $cat, int $exceptId = 0): bool
    {
        return (bool)Database::value(
            'SELECT COUNT(*) FROM applications WHERE lesson_date=? AND slot_id=? AND category_id=? AND status_id=? AND id<>?',
            [$date, $slot, $cat, self::CONFIRMED, $exceptId]
        );
    }

    /** Возвращает ошибки по полям; при отсутствии ошибок создаёт заявку со статусом «Новая». */
    public static function create(int $userId, array $d): array
    {
        $e = [];
        $cat = (int)($d['category_id'] ?? 0);
        $slot = (int)($d['slot_id'] ?? 0);
        $tr = (int)($d['transmission_id'] ?? 0);
        $date = Validator::parseDate($d['lesson_date'] ?? '');
        $dur = (int)($d['duration_minutes'] ?? 0);
        if (!in_array($dur, self::DURATIONS, true)) $e['duration_minutes'] = 'Выберите длительность';
        if (!Database::one('SELECT id FROM categories WHERE id=?', [$cat])) $e['category_id'] = 'Выберите категорию';
        if (!Database::one('SELECT id FROM slots WHERE id=?', [$slot])) $e['slot_id'] = 'Выберите время';
        if (!Database::one('SELECT id FROM transmissions WHERE id=?', [$tr])) $e['transmission_id'] = 'Выберите тип КПП';
        if (!$date) $e['lesson_date'] = 'Введите дату в формате ДД.ММ.ГГГГ';
        elseif ($date < date('Y-m-d')) $e['lesson_date'] = 'Нельзя записаться на прошедшую дату';
        if (!$e && self::slotTaken($date, $slot, $cat)) {
            $e['slot_id'] = 'Это время уже занято подтверждённой заявкой — выберите другое';
        }
        if (!$e) {
            Database::query(
                'INSERT INTO applications (user_id, instructor_id, category_id, lesson_date, slot_id, duration_minutes, transmission_id, status_id)
                 VALUES (?,?,?,?,?,?,?,?)',
                [$userId, Database::value('SELECT instructor_id FROM users WHERE id=?', [$userId]) ?: null,
                 $cat, $date, $slot, $dur, $tr, self::NEW]
            );
        }
        return $e;
    }

    private const SELECT = 'SELECT a.id, a.user_id, a.lesson_date, a.status_id, a.duration_minutes, s.time_slot, c.name AS category,
            t.name AS transmission, st.name AS status, u.phone, r.id AS review_id, r.rating,
            CONCAT_WS(\' \', u.last_name, u.first_name, u.middle_name) AS full_name,
            CONCAT_WS(\' \', i.last_name, i.first_name, i.middle_name) AS instructor
        FROM applications a
        JOIN slots s ON s.id=a.slot_id JOIN categories c ON c.id=a.category_id
        JOIN transmissions t ON t.id=a.transmission_id JOIN statuses st ON st.id=a.status_id
        JOIN users u ON u.id=a.user_id LEFT JOIN instructors i ON i.id=a.instructor_id LEFT JOIN reviews r ON r.application_id=a.id';

    private static function where(array $f): array
    {
        $w = []; $p = [];
        if (!empty($f['user_id'])) { $w[] = 'a.user_id=?'; $p[] = $f['user_id']; }
        if (!empty($f['status_id'])) { $w[] = 'a.status_id=?'; $p[] = $f['status_id']; }
        if (!empty($f['date'])) { $w[] = 'a.lesson_date=?'; $p[] = $f['date']; }
        return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
    }

    public static function list(array $f, int $limit = 1000, int $offset = 0): array
    {
        [$w, $p] = self::where($f);
        return Database::all(self::SELECT . $w . ' ORDER BY a.lesson_date DESC, s.time_slot, a.id DESC LIMIT '
            . (int)$limit . ' OFFSET ' . (int)$offset, $p);
    }

    public static function count(array $f): int
    {
        [$w, $p] = self::where($f);
        return (int)Database::value('SELECT COUNT(*) FROM applications a' . $w, $p);
    }

    public static function countOnDate(string $date): int
    {
        return (int)Database::value('SELECT COUNT(*) FROM applications WHERE lesson_date=? AND status_id<>?', [$date, self::CANCELED]);
    }

    /** Смена статуса по допустимым переходам. Возвращает текст ошибки или null. */
    public static function changeStatus(int $id, int $new): ?string
    {
        $a = Database::one('SELECT * FROM applications WHERE id=?', [$id]);
        if (!$a) return 'Заявка не найдена';
        if (!in_array($new, self::TRANSITIONS[$a['status_id']] ?? [], true)) return 'Недопустимый переход статуса';
        if ($new === self::CONFIRMED && self::slotTaken($a['lesson_date'], $a['slot_id'], $a['category_id'], $id)) {
            return 'Слот уже занят другой подтверждённой заявкой';
        }
        Database::query('UPDATE applications SET status_id=? WHERE id=?', [$new, $id]);
        return null;
    }
}
