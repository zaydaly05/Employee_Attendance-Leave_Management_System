
<?php


// require_once '../Models/announcements.php';
require_once 'dbConnect.php';
class Announcements {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

  

    public function getAnnouncements() {

    $sql = "SELECT a.id, a.message, a.created_by, a.created_at, a.status, 
                   u.name AS admin_name
            FROM announcements a
            JOIN users u ON a.created_by = u.id
            ORDER BY a.id DESC";
       
        
    $result = $this->conn->query($sql);
    $result->execute();
    if (!$result) {
        echo "<p>Error loading announcements: " . htmlspecialchars($this->conn->error) . "</p>";
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

}
?>

  <!-- // public function getAnnouncements() {
    //     $sql = "SELECT a.message, a.created_at, u.username AS admin_name
    //             FROM announcements a
    //             JOIN users u ON a.created_by = u.id
    //             ORDER BY a.id DESC";

    //     return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    // } -->