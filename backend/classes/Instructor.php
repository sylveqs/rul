<?php

class Instructor
{

    public static function leastLoaded(): ?int
    {
        $id = Database::value('SELECT i.id FROM instructors i LEFT JOIN users u ON u.instructor_id = i.id
            GROUP BY i.id ORDER BY COUNT(u.id), i.id LIMIT 1');
        return $id === false ? null : (int)$id;
    }
}
