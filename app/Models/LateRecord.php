<?php

class LateRecord
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($schoolId, $studentName, $className, $reason, $date)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO late_records 
            (school_id, student_name, class_name, reason, late_date)
            VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            (int)$schoolId,
            $studentName,
            $className,
            $reason,
            $date
        ]);
    }

    public function getBySchool($schoolId, $search = '')
    {
        $schoolId = (int)$schoolId;

        if ($search !== '') {

            $stmt = $this->db->prepare("
                SELECT * FROM late_records
                WHERE school_id = ?
                AND student_name LIKE ?
                ORDER BY late_date DESC
                LIMIT 500
            ");

            $stmt->execute([
                $schoolId,
                '%' . $search . '%'
            ]);

        } else {

            $stmt = $this->db->prepare("
                SELECT * FROM late_records
                WHERE school_id = ?
                ORDER BY late_date DESC
                LIMIT 500
            ");

            $stmt->execute([$schoolId]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT lr.*, u.name 
             FROM late_records lr
             JOIN users u ON lr.user_id = u.id
             ORDER BY lr.late_date DESC
             LIMIT 500"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countToday($schoolId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) 
             FROM late_records 
             WHERE school_id = ? 
             AND late_date = CURDATE()"
        );

        $stmt->execute([(int)$schoolId]);

        return (int)$stmt->fetchColumn();
    }

    public function delete($id, $schoolId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM late_records
            WHERE id = ? AND school_id = ?
        ");

        return $stmt->execute([
            (int)$id,
            (int)$schoolId
        ]);
    }

    public function getById($id, $schoolId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM late_records
            WHERE id = ? AND school_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            (int)$id,
            (int)$schoolId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $student, $class, $reason, $date, $schoolId)
    {
        $stmt = $this->db->prepare("
            UPDATE late_records
            SET student_name = ?, class_name = ?, reason = ?, late_date = ?
            WHERE id = ? AND school_id = ?
        ");

        return $stmt->execute([
            $student,
            $class,
            $reason,
            $date,
            (int)$id,
            (int)$schoolId
        ]);
    }

    public function getStats($schoolId)
    {
        $schoolId = (int)$schoolId;
        $stats = [];

        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM late_records
            WHERE school_id = ?
        ");
        $stmt->execute([$schoolId]);
        $stats['total'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM late_records
            WHERE school_id = ?
            AND late_date = CURDATE()
        ");
        $stmt->execute([$schoolId]);
        $stats['today'] = (int)$stmt->fetchColumn();

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

        $stmt = $this->db->prepare("
            SELECT class_name, COUNT(*) as count
            FROM late_records
            WHERE school_id = ?
            GROUP BY class_name
            ORDER BY count DESC
        ");
        $stmt->execute([$schoolId]);
        $stats['classes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }
}