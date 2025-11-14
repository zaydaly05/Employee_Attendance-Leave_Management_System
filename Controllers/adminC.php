<?php
// adminC.php - Admin Controller
// This controller handles admin-related requests (announcements, user management, leave management)

require_once __DIR__ . '/../Models/dbConnect.php';
require_once __DIR__ . '/../Models/user.php';

class adminC {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Handle posting announcement
    public function handlePostAnnouncement() {
        global $pdo;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        if (isset($_POST['postAnnouncement'])) {
            $announcement = trim($_POST['announcement'] ?? '');
            if ($announcement !== '') {
                try {
                    $stmt = $pdo->prepare("INSERT INTO announcements (message, created_at) VALUES (?, NOW())");
                    $stmt->execute([$announcement]);
                    $_SESSION['flash'] = 'Announcement posted successfully!';
                    header("Location: /admin");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['flash'] = 'Error posting announcement: ' . $e->getMessage();
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
                try {
                    $stmt = $pdo->prepare("INSERT INTO celebrations (message, created_at) VALUES (?, NOW())");
                    $stmt->execute([$celebration]);
                    $_SESSION['flash'] = 'Celebration posted successfully!';
                    header("Location: /admin");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['flash'] = 'Error posting celebration: ' . $e->getMessage();
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
        global $pdo;

        // Handle user signup requests management
        if (isset($_GET['user_action'], $_GET['user_id'])) {
            $userAction = $_GET['user_action']; // accept or reject
            $userId = (int) $_GET['user_id'];

            try {
                if ($userAction === 'accept') {
                    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
                    $stmt->execute([$userId]);
                    $_SESSION['flash'] = 'User request accepted successfully!';
                } elseif ($userAction === 'reject') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $_SESSION['flash'] = 'User request rejected.';
                }
                header("Location: /admin");
                exit();
            } catch (Exception $e) {
                $_SESSION['flash'] = 'Error processing user request: ' . $e->getMessage();
                header("Location: /admin");
                exit();
            }
        }

        // Handle leave requests management
        if (isset($_GET['leave_action'], $_GET['leave_id'])) {
            $leaveAction = $_GET['leave_action']; // accept or reject
            $leaveId = (int) $_GET['leave_id'];

            try {
                if ($leaveAction === 'accept') {
                    $stmt = $pdo->prepare("UPDATE leave_requests SET status = 'approved' WHERE id = ?");
                    $stmt->execute([$leaveId]);
                    $_SESSION['flash'] = 'Leave request approved successfully!';
                } elseif ($leaveAction === 'reject') {
                    $stmt = $pdo->prepare("UPDATE leave_requests SET status = 'rejected' WHERE id = ?");
                    $stmt->execute([$leaveId]);
                    $_SESSION['flash'] = 'Leave request rejected.';
                }
                header("Location: /admin");
                exit();
            } catch (Exception $e) {
                $_SESSION['flash'] = 'Error processing leave request: ' . $e->getMessage();
                header("Location: /admin");
                exit();
            }
        }
    }

    // Display user signup requests
    public function userSignupRequests() {
        global $pdo;
        
        try {
            $stmt = $pdo->query("SELECT id, username, email FROM users WHERE approved = 0");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($users) === 0) {
                echo "<p>No pending user sign-up requests.</p>";
                return;
            }

            foreach ($users as $user) {
                echo '<div class="request-card">';
                echo '<span>' . htmlspecialchars($user["username"]) . ' (' . htmlspecialchars($user['email']) . ')</span>';
                echo '<div class="request-actions">';
                echo '<a href="/admin/manage-requests?user_action=accept&user_id=' . $user['id'] . '"><button class="accept-btn">Accept</button></a>';
                echo '<a href="/admin/manage-requests?user_action=reject&user_id=' . $user['id'] . '"><button class="reject-btn">Reject</button></a>';
                echo '</div>';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo "<p>Error loading user signup requests: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // Display leave requests
    public function leaveRequests() {
        global $pdo;
        
        try {
            $stmt = $pdo->query("SELECT lr.id, u.username, lr.start_date, lr.end_date, lr.reason, lr.status 
                                 FROM leave_requests lr 
                                 JOIN users u ON lr.user_id = u.id 
                                 WHERE lr.status = 'pending'");
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($requests) === 0) {
                echo "<p>No pending leave requests.</p>";
                return;
            }

            foreach ($requests as $request) {
                echo '<div class="request-card">';
                echo '<div>';
                echo '<span>' . htmlspecialchars($request["username"]) . '</span> requested leave from <strong>' . htmlspecialchars($request["start_date"]) . '</strong> to <strong>' . htmlspecialchars($request["end_date"]) . '</strong><br/>';
                echo '<em>Reason: ' . htmlspecialchars($request["reason"]) . '</em>';
                echo '</div>';
                echo '<div class="request-actions">';
                echo '<a href="/admin/manage-requests?leave_action=accept&leave_id=' . $request['id'] . '"><button class="accept-btn">Accept</button></a>';
                echo '<a href="/admin/manage-requests?leave_action=reject&leave_id=' . $request['id'] . '"><button class="reject-btn">Reject</button></a>';
                echo '</div>';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo "<p>Error loading leave requests: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>
