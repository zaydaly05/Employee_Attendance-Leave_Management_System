<?php

require_once __DIR__ . '/../Models/dbConnect.php';

class HistoryC
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // Get attendance history for logged-in user
    public function getUserHistory(int $userId): array
    {
        // Get attendance history
        $stmt = $this->conn->prepare("
            SELECT 
                date as work_date,
                status,
                'attendance' as type
            FROM attendance
            WHERE employee_id = ?
            ORDER BY date DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attendance = [];
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }

        // Get approved half leaves
        $stmt2 = $this->conn->prepare("
            SELECT 
                start_date as work_date,
                leave_type as status,
                'leave' as type
            FROM leaves
            WHERE user_id = ? AND status = 'Approved' AND leave_type LIKE '%half%'
            ORDER BY start_date DESC
        ");
        $stmt2->bind_param("i", $userId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $leaves = [];
        while ($row = $result2->fetch_assoc()) {
            $leaves[] = $row;
        }

        // Merge and sort by date
        $history = array_merge($attendance, $leaves);
        usort($history, function($a, $b) {
            return strtotime($b['work_date']) - strtotime($a['work_date']);
        });

        return $history;
    }
}
