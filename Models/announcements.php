
<?php


require_once '../Models/announcements.php';
require_once 'dbConnect.php';
class Announcements {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAnnouncements() {
        $sql = "SELECT a.message, a.created_at, u.username AS admin_name
                FROM announcements a
                JOIN users u ON a.created_by = u.id
                ORDER BY a.id DESC";

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
?>