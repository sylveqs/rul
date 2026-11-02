<?php
/** Инструкторы. Каждый курсант закреплён за одним инструктором. */
class Instructor
{
    /** Id инструктора с наименьшим числом курсантов (для автоназначения). */
    public static function leastLoaded(): ?int
    {
        $id = Database::value('SELECT i.id FROM instructors i LEFT JOIN users u ON u.instructor_id = i.id
            GROUP BY i.id ORDER BY COUNT(u.id), i.id LIMIT 1');
        return $id === false ? null : (int)$id;
    }
}
