<?php

class LateRecord
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function create($schoolId, $name, $text, $date)
    {
        $stmt = $this->db->prepare("
            INSERT INTO late_records
            (school_id, student_name, text, late_date)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            (int)$schoolId,
            $name,
            $text,
            $date
        ]);
    }


    public function getBySchool($schoolId, $search = '')
    {
        if ($search !== '') {

            $stmt = $this->db->prepare("
                SELECT *
                FROM late_records
                WHERE school_id = ?
                AND student_name LIKE ?
                ORDER BY created_at DESC
                LIMIT 500
            ");

            $stmt->execute([
                (int)$schoolId,
                '%' . $search . '%'
            ]);

        } else {

            $stmt = $this->db->prepare("
                SELECT *
                FROM late_records
                WHERE school_id = ?
                ORDER BY created_at DESC
                LIMIT 500
            ");

            $stmt->execute([(int)$schoolId]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id, $schoolId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM late_records
            WHERE id = ?
            AND school_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            (int)$id,
            (int)$schoolId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function delete($id, $schoolId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM late_records
            WHERE id = ?
            AND school_id = ?
        ");

        return $stmt->execute([
            (int)$id,
            (int)$schoolId
        ]);
    }


    public function update($id, $name, $text, $date, $schoolId)
    {
        $stmt = $this->db->prepare("
            UPDATE late_records
            SET
                student_name = ?,
                text = ?,
                late_date = ?
            WHERE id = ?
            AND school_id = ?
        ");

        return $stmt->execute([
            $name,
            $text,
            $date,
            (int)$id,
            (int)$schoolId
        ]);
    }


    public function countToday($schoolId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
            AND late_date = CURDATE()
        ");

        $stmt->execute([(int)$schoolId]);

        return (int)$stmt->fetchColumn();
    }


    public function getStats($schoolId)
    {
        $stats = [];

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
        ");
        $stmt->execute([(int)$schoolId]);
        $stats['total'] = (int)$stmt->fetchColumn();


        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
            AND late_date = CURDATE()
        ");
        $stmt->execute([(int)$schoolId]);
        $stats['today'] = (int)$stmt->fetchColumn();


        $stmt = $this->db->prepare("
            SELECT student_name, COUNT(*) as count
            FROM late_records
            WHERE school_id = ?
            GROUP BY student_name
            ORDER BY count DESC
            LIMIT 5
        ");
        $stmt->execute([(int)$schoolId]);

        $stats['top_students'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }
}