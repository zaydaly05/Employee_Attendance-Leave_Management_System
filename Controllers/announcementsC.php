
<?php
require_once '../Models/announcements.php';
require_once 'dbConnect.php';

$annModel = new Announcements($conn);
$announcements = $annModel->getAnnouncements();


class AnnouncementsController {


    private $model;
    private $conn;

    public function __construct($model) {
        $this->model = $model;
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function index() {
        $announcements = $this->model->getAnnouncements();
        include "../view/userDashboard.php";
    }
    public function dashboard() {
    require_once '../Models/announcements.php';

    $annModel = new Announcements($this->conn);
    $announcements = $annModel->getAnnouncements();

    include "../Views/userDashboard.php";

}
}

?>