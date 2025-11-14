<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./Public/Css/admin.css"/>
    <?php
        require_once './Controllers/adminC.php';?>
</head>
<body>
    <div class="sidebar">
        <h2 class="logo">digg</h2>
        <nav>
            <ul>
                <li class="active"><a href="#">Dashboard</a></li>
                <li><a href="#">User Sign-Ups</a></li>
                <li><a href="#">Leave Requests</a></li>
                <li><a href="#">Announcements</a></li>
                <li><a href="#">Celebrations</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <?php  include_once 'header.php';?>
           
         
        </header>

        
                 <h1>Welcome back, Admin 👋</h1>
        <section class="post-section">
            <h2>Post Announcement</h2>
            <form id="announcementForm" action="/admin/post-announcement" method="POST">
                <textarea name="announcement" placeholder="Type your announcement here..." required></textarea>
                <button type="submit" name="postAnnouncement">Post Announcement</button>
       
            </form>
        </section>

        <section class="post-section">
            <h2>Post Celebration</h2>
            <form id="celebrationForm" action="/admin/post-announcement" method="POST">
                <textarea name="celebration" placeholder="Type your celebration here..." required></textarea>
                <button type="submit" name="postCelebration">Post Celebration</button>
            </form>
        </section>

        <section class="requests-section">
            <h2>User Sign-Up Requests</h2>
            <div id="userSignupRequests">
                <!-- User sign-up requests will be listed here -->
                <?php 
                if (!isset($adminController)) {
                    $adminController = new adminC();
                }
                $adminController->userSignupRequests(); 
                ?>
            </div>
        </section>

        <section class="requests-section">
            <h2>Leave Requests</h2>
            <div id="leaveRequests">
                <!-- Leave requests will be listed here -->
                <?php 
                if (!isset($adminController)) {
                    $adminController = new adminC();
                }
                $adminController->leaveRequests(); 
                ?>
            </div>
        </section>
    </div>
</body>
</html>