<?php
require_once 'db.php';

class Role {
    private $conn;
    private $table = "roles";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->conn->query($sql);
    }
}
?>
