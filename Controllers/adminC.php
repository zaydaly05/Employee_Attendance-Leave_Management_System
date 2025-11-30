<?php
// adminC.php - Admin Controller
// This controller handles admin-related requests (announcements, user management, leave management)

require_once __DIR__ . '/../Models/dbConnect.php';
require_once __DIR__ . '/../Models/user.php';

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
    public function handlePostAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        if (isset($_POST['postAnnouncement'])) {
            $announcement = trim($_POST['announcement'] ?? '');
            if ($announcement !== '') {
                $sql = "INSERT INTO announcements (message, created_at) VALUES (?, NOW())";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("s", $announcement);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Announcement posted successfully!';
                        header("Location: /admin");
                        exit();
                    } else {
                        $_SESSION['flash'] = 'Error posting announcement: ' . $stmt->error;
                        header("Location: /admin");
                        exit();
                    }
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                    header("Location: /admin");
                    exit();
                }
            } else {
                $_SESSION['flash'] = 'Announcement cannot be empty.';
                header("Location: /admin");
                exit();
            }
        }

        // Handle posting celebration
        if (isset($_POST['postCelebration'])) {
            $celebration = trim($_POST['celebration'] ?? '');
            if ($celebration !== '') {
                $sql = "INSERT INTO celebrations (message, created_at) VALUES (?, NOW())";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("s", $celebration);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Celebration posted successfully!';
                        header("Location: /admin");
                        exit();
                    } else {
                        $_SESSION['flash'] = 'Error posting celebration: ' . $stmt->error;
                        header("Location: /admin");
                        exit();
                    }
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                    header("Location: /admin");
                    exit();
                }
            } else {
                $_SESSION['flash'] = 'Celebration cannot be empty.';
                header("Location: /admin");
                exit();
            }
        }
    }

    // Handle managing requests (user signup and leave requests)
    public function handleManageRequests() {
        // Handle user signup requests management
        if (isset($_GET['user_action'], $_GET['user_id'])) {
            $userAction = $_GET['user_action']; // accept or reject
            $userId = (int) $_GET['user_id'];

            if ($userAction === 'accept') {
                $sql = "UPDATE users SET approved = 1 WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $userId);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'User request accepted successfully!';
                    } else {
                        $_SESSION['flash'] = 'Error processing user request: ' . $stmt->error;
                    }
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
                        $_SESSION['flash'] = 'Error processing user request: ' . $stmt->error;
                    }
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                }
            }
            header("Location: /admin");
            exit();
        }

        // Handle leave requests management
        if (isset($_GET['leave_action'], $_GET['leave_id'])) {
            $leaveAction = $_GET['leave_action']; // accept or reject
            $leaveId = (int) $_GET['leave_id'];

            if ($leaveAction === 'accept') {
                $sql = "UPDATE leave_requests SET status = 'approved' WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $leaveId);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Leave request approved successfully!';
                    } else {
                        $_SESSION['flash'] = 'Error processing leave request: ' . $stmt->error;
                    }
                } else {
                    $_SESSION['flash'] = 'Error preparing statement: ' . $this->conn->error;
                }
            } elseif ($leaveAction === 'reject') {
                $sql = "UPDATE leave_requests SET status = 'rejected' WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $leaveId);
                    if ($stmt->execute()) {
                        $_SESSION['flash'] = 'Leave request rejected.';
                    } else {
                        $_SESSION['flash'] = 'Error processing leave request: ' . $stmt->error;
                    }
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
        $sql = "SELECT id, name, email FROM users ";
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
            echo '<span>' . htmlspecialchars($user["username"] ?? $user["name"] ?? 'Unknown') . ' (' . htmlspecialchars($user['email']) . ')</span>';
            echo '<div class="request-actions">';
            echo '<a href="/admin/manage-requests?user_action=accept&user_id=' . $user['id'] . '"><button class="accept-btn">Accept</button></a>';
            echo '<a href="/admin/manage-requests?user_action=reject&user_id=' . $user['id'] . '"><button class="reject-btn">Reject</button></a>';
            echo '</div>';
            echo '</div>';
        }
    }

    // Display leave requests
    public function leaveRequests() {
        $sql = "SELECT lr.id, u.name, lr.start_date, lr.end_date, lr.reason, lr.status 
                FROM leave_requests lr 
                JOIN users u ON lr.user_id = u.id 
                WHERE lr.status = 'pending'";
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
            echo '<span>' . htmlspecialchars($request["username"] ?? $request["name"] ?? 'Unknown') . '</span> requested leave from <strong>' . htmlspecialchars($request["start_date"]) . '</strong> to <strong>' . htmlspecialchars($request["end_date"]) . '</strong><br/>';
            echo '<em>Reason: ' . htmlspecialchars($request["reason"]) . '</em>';
            echo '</div>';
            echo '<div class="request-actions">';
            echo '<a href="/admin/manage-requests?leave_action=accept&leave_id=' . $request['id'] . '"><button class="accept-btn">Accept</button></a>';
            echo '<a href="/admin/manage-requests?leave_action=reject&leave_id=' . $request['id'] . '"><button class="reject-btn">Reject</button></a>';
            echo '</div>';
            echo '</div>';
        }
    }
}
} // End of class_exists check
?>
