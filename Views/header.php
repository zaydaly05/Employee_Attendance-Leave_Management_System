<link rel="stylesheet" href="./Public/Css/header.css">

<header>
    <!-- <button class="icon-btn" aria-label="Notifications">🔔</button> -->

    <div class="profile-wrapper">
        <span class="avatar">Z</span>
        <!-- <span class="arrow"></span> -->

        <!-- Dropdown -->
        <div class="profile-dropdown">
            <div class="profile-info">  
                <div class="avatar-large">Z</div>
                <div class="user-details">
                    <div class="name">Zayd Ali</div>
                    <div class="handle">HR Department</div>
                    
                </div>
            </div>

            <div class="dropdown-links">
                <a href="#"><span class="icon">✏️</span>Edit Profile</a>
                <a href="#"><span class="icon">⚙️</span> Settings</a>
                
            </div>

            <div class="dropdown-links">
               
               
                <a onclick="window.location.href='<?php echo $base_url; ?>logout';"><span class="icon">⏻</span> Log out</a>
            </div>
        </div>
    </div>
</header>
