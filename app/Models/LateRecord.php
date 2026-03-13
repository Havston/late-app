<?php

class LateRecord
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    /* ======================
       CREATE
    ====================== */

    public function create($schoolId, $name, $text, $date)
    {
        $stmt = $this->db->prepare("
            INSERT INTO late_records
            (school_id, student_name, text, created_at)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            (int)$schoolId,
            $name,
            $text,
            $date
        ]);
    }


    /* ======================
       GET BY SCHOOL
    ====================== */

    public function getBySchool($schoolId, $search = '')
    {
        $schoolId = (int)$schoolId;

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
                $schoolId,
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

            $stmt->execute([$schoolId]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ======================
       GET BY ID
    ====================== */

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


    /* ======================
       DELETE
    ====================== */

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


    /* ======================
       UPDATE
    ====================== */

    public function update($id, $name, $text, $date, $schoolId)
    {
        $stmt = $this->db->prepare("
            UPDATE late_records
            SET
                student_name = ?,
                text = ?,
                created_at = ?
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


    /* ======================
       COUNT TODAY
    ====================== */

    public function countToday($schoolId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
            AND DATE(created_at) = CURDATE()
        ");

        $stmt->execute([(int)$schoolId]);

        return (int)$stmt->fetchColumn();
    }


    /* ======================
       STATS
    ====================== */

    public function getStats($schoolId)
    {
        $schoolId = (int)$schoolId;

        $stats = [];


        // всего

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
        ");

        $stmt->execute([$schoolId]);

        $stats['total'] = (int)$stmt->fetchColumn();


        // сегодня

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM late_records
            WHERE school_id = ?
            AND DATE(created_at) = CURDATE()
        ");

        $stmt->execute([$schoolId]);

        $stats['today'] = (int)$stmt->fetchColumn();


        // топ учеников

        $stmt = $this->db->prepare("
            SELECT student_name, COUNT(*) as count
            FROM late_records
            WHERE school_id = ?
            GROUP BY student_name
            ORDER BY count DESC
            LIMIT 5
        ");

        $stmt->execute([$schoolId]);

        $stats['top_students'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $stats;
    }

}