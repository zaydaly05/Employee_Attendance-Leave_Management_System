<?php
// adminC.php - Admin Controller
// This controller handles admin-related requests (announcements, user management, leave management)

require_once __DIR__ . '/../Models/dbConnect.php';
require_once __DIR__ . '/../Models/user.php';
require_once __DIR__ . '/../Models/announcements.php';
require_once __DIR__ . '/../Models/celebrations.php';

if (!class_exists('adminC')) {
class adminC {

    private $userModel;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
        $this->userModel = new User();
    }

    // Handle posting announcement

    public function handlePostAnnouncement()
    {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Method Not Allowed';
        return;
    }

    session_start();

    if (!isset($_POST['postAnnouncement'])) {
        $_SESSION['flash'] = 'Invalid form submission.';
        header("Location: /EALMS/admin");
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash'] = "Only admins can post announcements.";
        header("Location: /EALMS");
        exit();
    }

    $userId = (int)$_SESSION['user_id'];
    $announcement = trim($_POST['announcement'] ?? '');

    if ($announcement === '') {
        $_SESSION['flash'] = 'Announcement cannot be empty.';
        header("Location: /EALMS/admin");
        exit();
    }

    // SQL
    $sql = "INSERT INTO announcements (message, created_by, created_at, status)
            VALUES (?, ?, NOW(), 'active')";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("❌ SQL PREPARE FAILED: " . $this->conn->error);
    }

    if (!$stmt->bind_param("si", $announcement, $userId)) {
        die("❌ BIND PARAM FAILED: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("❌ EXECUTE FAILED: " . $stmt->error);
    }

    $_SESSION['flash'] = 'Announcement posted successfully!';
    header("Location: /EALMS/admin");
    exit();
    }
    public function getAnnouncements()
    {
    // 1. Fetch announcements with existing query (no JOIN)
    $sql = "SELECT message, created_at, created_by FROM announcements WHERE status = 'active' ORDER BY created_at DESC";
    $result = $this->conn->query($sql);

    $announcements = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // 2. For each announcement, fetch admin name from users table
            $adminId = (int)$row['created_by'];

            $stmt = $this->conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'admin' AND approved = 1 LIMIT 1");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $adminResult = $stmt->get_result();

            $adminName = "Unknown";

            if ($adminResult && $adminResult->num_rows > 0) {
                $adminRow = $adminResult->fetch_assoc();
                $adminName = $adminRow['name'];
            }

            // Add admin name to the announcement
            $row['admin_name'] = $adminName;

            $announcements[] = $row;
        }
    }

    return $announcements;
    }

        
    public function expireOldAnnouncements(){
    $sql = "UPDATE announcements
            SET status = 'inactive'
            WHERE status = 'active'
            AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    }


 


    // Handle posting celebration reminder
        
    public function handlePostCelebration()
    {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Method Not Allowed';
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    if (!isset($_POST['postCelebration'])) {
        $_SESSION['flash'] = 'Invalid form submission.';
        header("Location: /EALMS/admin");
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash'] = "Only admins can post celebrations.";
        header("Location: /EALMS");
        exit();
    }

    $userId = (int)$_SESSION['user_id'];
    $celebrationName = trim($_POST['celebration_name'] ?? '');
    $celebrationType = trim($_POST['celebration_type'] ?? '');
    $celebrationDate = trim($_POST['celebration_date'] ?? '');
    $celebrationMessage = trim($_POST['celebration_message'] ?? '');

    // Validation
    if ($celebrationName === '') {
        $_SESSION['flash'] = 'Celebration name is required.';
        header("Location: /EALMS/admin");
        exit();
    }

    if ($celebrationType === '') {
        $_SESSION['flash'] = 'Celebration type is required.';
        header("Location: /EALMS/admin");
        exit();
    }

    if ($celebrationDate === '') {
        $_SESSION['flash'] = 'Celebration date is required.';
        header("Location: /EALMS/admin");
        exit();
    }

    // Check if celebrations table has the new columns, if not, use message field
    $columnsResult = $this->conn->query("SHOW COLUMNS FROM celebrations");
    $columns = [];
    if ($columnsResult) {
        while ($col = $columnsResult->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
    }

    $hasName = in_array('name', $columns) || in_array('username', $columns) || in_array('user_name', $columns);
    $hasType = in_array('celebration_type', $columns) || in_array('type', $columns);
    $hasDate = in_array('celebration_date', $columns) || in_array('date', $columns);

    // Build SQL query based on available columns
    if ($hasName && $hasType && $hasDate) {
        // Use new structure with name, type, date columns
        $nameField = in_array('name', $columns) ? 'name' : (in_array('username', $columns) ? 'username' : 'user_name');
        $typeField = in_array('celebration_type', $columns) ? 'celebration_type' : 'type';
        $dateField = in_array('celebration_date', $columns) ? 'celebration_date' : 'date';
        
        $sql = "INSERT INTO celebrations ($nameField, $typeField, $dateField, message, created_by, created_at, status)
                VALUES (?, ?, ?, ?, ?, NOW(), 'active')";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("❌ SQL PREPARE FAILED: " . $this->conn->error);
        }
        
        $message = $celebrationMessage !== '' ? $celebrationMessage : "$celebrationName - $celebrationType on $celebrationDate";
        $stmt->bind_param("ssssi", $celebrationName, $celebrationType, $celebrationDate, $message, $userId);
    } else {
        // Fallback: store in message field with structured format
        $message = "$celebrationName|$celebrationType|$celebrationDate";
        if ($celebrationMessage !== '') {
            $message .= "|$celebrationMessage";
        }
        
    $sql = "INSERT INTO celebrations (message, created_by, created_at, status)
            VALUES (?, ?, NOW(), 'active')";

    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        die("❌ SQL PREPARE FAILED: " . $this->conn->error);
    }

        $stmt->bind_param("si", $message, $userId);
    }

    if (!$stmt->execute()) {
        die("❌ EXECUTE FAILED: " . $stmt->error);
    }

    $_SESSION['flash'] = 'Celebration reminder added successfully!';
    header("Location: /EALMS/admin");
    exit();
    }

    // Get celebration reminders from database
    public function getCelebrationReminders() {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime('+30 days'));
        
        // Check what columns exist in the celebrations table
        $columnsResult = $this->conn->query("SHOW COLUMNS FROM celebrations");
        $columns = [];
        if ($columnsResult) {
            while ($col = $columnsResult->fetch_assoc()) {
                $columns[] = $col['Field'];
            }
        }
        
        $hasName = in_array('name', $columns) || in_array('username', $columns) || in_array('user_name', $columns);
        $hasType = in_array('celebration_type', $columns) || in_array('type', $columns);
        $hasDate = in_array('celebration_date', $columns) || in_array('date', $columns);
        
        $celebrations = [];
        
        if ($hasName && $hasType && $hasDate) {
            // Use new structure with name, type, date columns
            $nameField = in_array('name', $columns) ? 'name' : (in_array('username', $columns) ? 'username' : 'user_name');
            $typeField = in_array('celebration_type', $columns) ? 'celebration_type' : 'type';
            $dateField = in_array('celebration_date', $columns) ? 'celebration_date' : 'date';
            
            $sql = "SELECT $nameField AS name, $typeField AS type, $dateField AS celebration_date, id
                    FROM celebrations 
                    WHERE status = 'active' 
                    AND (
                        DATE_FORMAT($dateField, '%m-%d') >= DATE_FORMAT('$today', '%m-%d')
                        AND DATE_FORMAT($dateField, '%m-%d') <= DATE_FORMAT('$futureDate', '%m-%d')
                        OR DATE_FORMAT($dateField, '%m-%d') < DATE_FORMAT('$today', '%m-%d')
                        AND DATE_FORMAT($dateField, '%m-%d') <= DATE_FORMAT('$futureDate', '%m-%d')
                    )
                    ORDER BY 
                        CASE 
                            WHEN DATE_FORMAT($dateField, '%m-%d') >= DATE_FORMAT('$today', '%m-%d') 
                            THEN 0 
                            ELSE 1 
                        END,
                        DATE_FORMAT($dateField, '%m-%d')
                    LIMIT 10";
        } else {
            // Fallback: parse from message field
            $sql = "SELECT id, message, created_at 
                    FROM celebrations 
                    WHERE status = 'active' 
                    AND message LIKE '%|%|%'
                    ORDER BY created_at DESC 
                    LIMIT 10";
        }
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($hasName && $hasType && $hasDate) {
                    $celebrations[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'type' => $row['type'],
                        'date' => $row['celebration_date']
                    ];
                } else {
                    // Parse from message format: "Name|Type|Date|Message"
                    $messageParts = explode('|', $row['message']);
                    if (count($messageParts) >= 3) {
                        $celebrations[] = [
                            'id' => $row['id'],
                            'name' => $messageParts[0],
                            'type' => $messageParts[1],
                            'date' => $messageParts[2]
                        ];
                    }
                }
            }
        }
        
        return $celebrations;
    }

    // Handle managing requests (user signup and leave requests)
    public function handleManageRequests() {
    // Check for POST data instead of GET
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_action'], $_POST['user_id'])) {
        
        $userAction = $_POST['user_action'];
        $userId = (int) $_POST['user_id'];

        if ($userAction === 'accept') {
            $sql = "UPDATE users SET approved = 1 WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                if ($stmt->execute()) {
                    $_SESSION['flash'] = 'User request accepted successfully!';
                } else {
                    $_SESSION['flash'] = 'Error executing update: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
            }

        } elseif ($userAction === 'reject') {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                if ($stmt->execute()) {
                    $_SESSION['flash'] = 'User request rejected.';
                } else {
                    $_SESSION['flash'] = 'Error executing delete: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
            }
        }
        
        // Redirect back to admin panel
        header("Location: /EALMS/admin");
        exit();
    }


        // Handle leave requests management
        if (isset($_GET['leave_action'], $_GET['leave_id'])) {
            $leaveAction = $_GET['leave_action']; // accept or reject
            $leaveId = (int) $_GET['leave_id'];

            if ($leaveAction === 'accept') {
                $sql = "UPDATE leaves SET status = 'Approved' WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $leaveId);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Leave request approved successfully!';
                    } else {
                        $_SESSION['flash'] = 'Error processing leave request: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                }
            } elseif ($leaveAction === 'reject') {
                $sql = "UPDATE leaves SET status = 'Rejected' WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $leaveId);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Leave request rejected.';
                    } else {
                        $_SESSION['flash'] = 'Error processing leave request: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                }
            }
            header("Location: /EALMS/admin");
            exit();
        }
    }

    // Display user signup requests
    public function userSignupRequests() {

    // Handle accept/reject FIRST
    if (isset($_GET['user_action']) && isset($_GET['user_id'])) {
        $userId = intval($_GET['user_id']);
        $action = $_GET['user_action'];

        if ($action === 'accept') {
            $updateSql = "UPDATE users SET approved = 1 WHERE id = ?";
            $stmt = $this->conn->prepare($updateSql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            // Refresh page so the approved user disappears
            header("Location: /admin/manage-requests");
            exit;
        }

        if ($action === 'reject') {
            $deleteSql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($deleteSql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            header("Location: /admin/manage-requests");
            exit;
        }
    }

    // Now load only pending users
    $sql = "SELECT id, name, email FROM users WHERE approved = 0";
    $result = $this->conn->query($sql);

    if (!$result) {
        echo "<p>Error loading user signup requests: " . htmlspecialchars($this->conn->error) . "</p>";
        return;
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    if (count($users) === 0) {
        echo "<p>No pending user sign-up requests.</p>";
        return;
    }

        foreach ($users as $user) {
        echo '<div class="request-card">';
        // Use the name/email logic you already had
        echo '<span>' . htmlspecialchars($user["username"] ?? $user["name"] ?? "Unknown") 
                . " (" . htmlspecialchars($user["email"]) . ')</span>';
        
        echo '<div class="request-actions">';

        // --- ACCEPT FORM ---
        echo '<form action="/EALMS/admin/manage-requests" method="POST" style="display:inline;">';
        echo '<input type="hidden" name="user_id" value="' . $user['id'] . '">';
        echo '<input type="hidden" name="user_action" value="accept">';
        echo '<button type="submit" class="accept-btn">Accept</button>';
        echo '</form>';

        // --- REJECT FORM ---
        echo '<form action="/EALMS/admin/manage-requests" method="POST" style="display:inline;">';
        echo '<input type="hidden" name="user_id" value="' . $user['id'] . '">';
        echo '<input type="hidden" name="user_action" value="reject">';
        echo '<button type="submit" class="reject-btn">Reject</button>';
        echo '</form>';

        echo '</div>'; // End request-actions
        echo '</div>'; // End request-card
    }
    }


    // Display leave requests
    public function leaveRequests() {
        $sql = "SELECT l.id, l.start_date, l.end_date, l.leave_type, l.reason, l.status 
                FROM leaves l 
                WHERE l.status = 'Pending'";
        $result = $this->conn->query($sql);
        
        if (!$result) {
            echo "<p>Error loading leave requests: " . htmlspecialchars($this->conn->error) . "</p>";
            return;
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }

        if (count($requests) === 0) {
            echo "<p>No pending leave requests.</p>";
            return;
        }

        foreach ($requests as $request) {
            echo '<div class="request-card">';
            echo '<div>';
            $leaveType = htmlspecialchars($request["leave_type"] ?? 'N/A');
            $startDate = htmlspecialchars($request["start_date"]);
            $endDate = htmlspecialchars($request["end_date"]);
            echo 'Leave request (' . $leaveType . ') from <strong>' . $startDate . '</strong> to <strong>' . $endDate . '</strong><br/>';
            echo '<em>Reason: ' . htmlspecialchars($request["reason"]) . '</em>';
            echo '</div>';
            echo '<div class="request-actions">';
            echo '<a href="/EALMS/admin/manage-requests?leave_action=accept&leave_id=' . $request['id'] . '"><button class="accept-btn">Accept</button></a>';
            echo '<a href="/EALMS/admin/manage-requests?leave_action=reject&leave_id=' . $request['id'] . '"><button class="reject-btn">Reject</button></a>';
            echo '</div>';
            echo '</div>';
        }
    }



    


}
} // End of class_exists check
?>
