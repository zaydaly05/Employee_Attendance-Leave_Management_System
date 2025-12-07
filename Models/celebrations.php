
<?php


// require_once '../Models/announcements.php';
require_once 'dbConnect.php';
class Celebrations {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

  

    public function getCelebrations() {

    $sql = "SELECT c.id, c.message, c.created_by, c.created_at, c.status, 
                   u.name AS admin_name
            FROM celebrations c
            JOIN users u ON c.created_by = c.id
            ORDER BY c.id DESC";
       
        
    $result = $this->conn->query($sql);
    $result->execute();
    if (!$result) {
        echo "<p>Error loading celebrations: " . htmlspecialchars($this->conn->error) . "</p>";
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