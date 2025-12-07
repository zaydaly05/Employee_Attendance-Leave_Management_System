<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="./Public/Css/adminFinal.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <script src="./Public/Js/animateNumber.js"></script>

    <?php require_once './Controllers/adminC.php'; ?>
    
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2 class="logo">digg</h2>
        <nav>
            <ul>
                <li class="active"><a href="#">Dashboard</a></li>
                <li><a href="#user-requests">User Sign-Ups</a></li>
                <li><a href="#leave-requests">Leave Requests</a></li>
                <li><a href="#announcements">Announcements</a></li>
                <li><a href="#celeb">Celebrations</a></li>
            </ul>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- LEFT COLUMN -->
        <div class="left-column">
            <h1>Welcome back, Admin 👋</h1>
                <?php
                // Define BASE_PATH once in a central config file or index.php
                define('BASE_PATH', '/EALMS/'); 
                // ... rest of your code to render the HTML ...
                ?>
                
      <!-- Announcement -->
            <section id="announcements" name="postAnnouncement" class="post-section">
                <h2>Post Announcement</h2>
                <form action="<?php echo BASE_PATH; ?>admin/post-announcement" method="POST">
                    <input type="hidden" name="postAnnouncement" value="1">
                    <textarea name="announcement" placeholder="Type your announcement.." required><?php echo htmlspecialchars($_POST['announcement'] ?? ''); ?></textarea>
                    <button type="submit">Post Announcement</button>
                </form>
            </section>

            <!-- Celebration -->
            <section id="celeb" class="post-section">
                <h2>Post Celebration</h2>
                <form action="<?php echo $base_url; ?>admin/post-celebration" method="POST" >
                    <input type="hidden" name="postCelebration" value="1">
                    <textarea name="celebration" placeholder="Write your celebration..." required><?php echo htmlspecialchars($_POST['celebration'] ?? ''); ?></textarea>
                    <button type="submit">Post Celebration</button>
                </form>
            </section>
            
            <!-- User Requests -->
          
            <section id="user-requests" class="requests-section" style="margin-bottom: 0 !important;">
                <h2>User Sign-Up Requests</h2>
                <?php 
                    if (!isset($adminController)) $adminController = new adminC();
                    $adminController->userSignupRequests(); 
                    // $adminController->handleManageRequests();
                ?>
            </section>
            <br>
            <section class="requests-section" id="leave-requests" style="width: 850px; margin-top: 0 !important;">   <!-- Leave Requests -->
                    <h2>Leave Requests</h2>
                    <?php 
                        if (!isset($adminController)) $adminController = new adminC();
                        $adminController->leaveRequests(); 
                        
                    ?>
                </section>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">
                
            <!-- DASHBOARD SUMMARY -->
            <div class="dashboard-container">
                <h1 class="dashboard-title">Dashboard Summary</h1>

                <div class="summary-card" id="usersCard">
                    <div class="card-header">
                        <h2 class="card-title">Total Users</h2>
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="card-value" id="totalUsers">0</div>
                    <p class="card-description">Active employees registered</p>
                </div>

                <div class="summary-card" id="leavesCard">
                    <div class="card-header">
                        <h2 class="card-title">Total Leaves</h2>
                        <div class="card-icon"><i class="fas fa-calendar-times"></i></div>
                    </div>
                    <div class="card-value" id="totalLeaves">0</div>
                    <p class="card-description">Leave requests this month</p>
                </div>

                <div class="summary-card" id="celebrationCard">
                    <div class="card-header">
                        <h2 class="card-title">Celebration Reminders</h2>
                        <div class="card-icon"><i class="fas fa-birthday-cake"></i></div>
                    </div>
                    <div id="celebrationList">
                        <div class="loading">Loading...</div>
                    </div>
                </div>

                <button class="refresh-btn" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i>   Refresh Data
                </button>
                
            </div> 
        </div><!-- RIGHT COLUMN END -->
      
            <!-- Leave Requests -->
                
    </div><!-- MAIN CONTENT END --> 
   
</body>

<!-- <footer>
   <?php //include_once 'footer.php'; ?> 
</footer> -->

</html>
