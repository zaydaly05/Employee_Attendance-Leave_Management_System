<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
<link rel="stylesheet" href="<?php echo $base_url; ?>Public/Css/User Dashboard.css" />

</head>
<body>


  <div class="container">
    <!-- Sidebar -->  
    <aside class="sidebar">
      <div class="logo">digg</div>
      <nav>
        <a href="#" class="active">
          <span class="icon" aria-hidden="true">📊</span>
          Dashboard
        </a>
        <a href="<?php echo $base_url; ?>history">
          <span class="icon" aria-hidden="true">⏰</span>
          History
        </a>
      </nav>
    </aside>
    <!-- Main content -->
    <div class="main">
      <header>
        <!-- <button aria-label="Notifications" class="icon-button">🔔</button>
        <button aria-label="Profile" class="icon-button" title="Profile">
          <div class="profile-circle">Z</div>
        </button>
        <button aria-label="Expand Menu" class="icon-button dropdown" title="Expand Menu">⌵</button>
      </header> -->

      <?php  include_once 'header.php';?>
      </header>
      <main class="content" role="main">
        <!-- Welcome and Request Time Off button -->
        <div style="display:flex; align-items:center; gap: 12px; margin-bottom: 12px; flex-wrap:wrap;">
          <div class="welcome">Welcome back, Zayd <span class="wave" aria-label="waving hand" role="img">👋</span></div>
          
          <a href="<?php echo $base_url; ?>request-time-off" class="btn-request" aria-label="Request Time Off" role="button">
            <svg aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              viewBox="0 0 24 24"><path d="M8 7V3M16 7V3M3 11h18M5 19h14a2 2 0 002-2v-5H3v5a2 2 0 002 2z"></path></svg>
            Request Time Off
          </a>

        </div>

       <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Leave Dashboard</title>

<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f6fb;
  padding: 30px;
}

.leave-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
}

.leave-card {
  background: #fff;
  padding: 16px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  text-align: center;
}

.title {
  font-weight: bold;
  margin-bottom: 12px;
}

.circle-bg {
  position: relative;
  width: 60px;
  height: 60px;
  margin: auto;
}

svg {
  transform: rotate(-90deg);
}

.bg {
  fill: none;
  stroke: #eee;
  stroke-width: 4;
}

.progress {
  fill: none;
  stroke-width: 4;
  stroke-linecap: round;
}

.percent {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: bold;
}

.details {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin-top: 8px;
}
</style>

</head>
<body>

<section class="leave-cards" id="leaveCards"></section>

<script>
fetch("getLeaves.php")
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById("leaveCards");

    data.forEach(leave => {
      const available = leave.total_days - leave.used_days;
      const percent = Math.round((available / leave.total_days) * 100);

      container.innerHTML += `
        <article class="leave-card">
          <div class="title">${leave.leave_type}</div>

          <div class="circle-bg">
            <svg width="48" height="48" viewBox="0 0 36 36">
              <circle cx="18" cy="18" r="16" class="bg"/>
              <circle
                cx="18" cy="18" r="16"
                class="progress"
                stroke="${leave.color}"
                stroke-dasharray="100"
                stroke-dashoffset="${100 - percent}"
              />
            </svg>
            <div class="percent" style="color:${leave.color}">
              ${percent}%
            </div>
          </div>

          <div class="details">
            <span>Available - ${available}</span>
            <span>Used - ${leave.used_days}</span>
          </div>
        </article>
      `;
    });
  });
</script>

</body>
</html>

          <div class="attendance-container card">
            <div class="section flex-row">
              <div style="flex: 2 1 0;">

            <h3>Daily Attendance</h3>
            <br>
            <form method="POST" action="">
                <label>
                    <input type="radio" name="attendance" value="present" required> Present
                </label>
                <label>
                    <input type="radio" name="attendance" value="absent"> Absent
                </label>
                <label>
                    <input type="radio" name="attendance" value="remote"> Remote
                </label>
                <button type="submit" class="btn-request" style="background-color: darkgreen;" name="mark_attendance">Mark Attendance</button>
            </form>
            <?php if(isset($attendance_message)): ?>
                <p class="attendance-message"><?= htmlspecialchars($attendance_message) ?></p>
            <?php endif; ?>
              </div>
            </div>
        </div>
       
        <!-- Main sections in two columns -->
        <div class="section flex-row">
          <div style="flex: 2 1 0;">
              <section class="section announcements-section" aria-label="Announcements">
              <?php 
                $admin = new adminC();
                $announcements = $admin->getAnnouncements();
                $expireAnnouncement = $admin->expireOldAnnouncements();
                
                if (!function_exists('formatAnnouncementTime')) {
                  function formatAnnouncementTime($timestamp) {
                    $time = strtotime($timestamp);
                    if (!$time) {
                      return '';
                    }
                    $diff = time() - $time;
                    if ($diff < 60) {
                      return 'Just now';
                    }
                    if ($diff < 3600) {
                      return floor($diff / 60) . ' min ago';
                    }
                    if ($diff < 86400) {
                      return floor($diff / 3600) . ' hrs ago';
                    }
                    return date('M j - h:i A', $time);
                  }
                }

                $announcementCount = count($announcements);
              ?>

              <div class="section-title announcements-title">
                <span>Announcements</span>
                <span class="announcement-badge"><?= $announcementCount; ?> new</span>
              </div>

              <?php if (!empty($announcements)): ?>
                <div class="announcement-list">
                  <?php foreach ($announcements as $a): ?>
                    <article class="announcement-item">
                      <div class="announcement-icon" aria-hidden="true">📢</div>
                      <div class="announcement-content">
                        <div class="announcement-text">
                          <?= nl2br(htmlspecialchars(strip_tags($a['message'], '<br>'))) ?>
                        </div>
                        <div class="announcement-meta">
                          <span><?= formatAnnouncementTime($a['created_at']); ?></span>
                          <?php if (!empty($a['admin_name'])): ?>
                            <span class="dot">•</span>
                            <span>By <?= htmlspecialchars($a['admin_name']); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </article>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="announcement-empty">
                  <p>No announcements available.</p>
                  <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 12h20L12 2z"></path>
                    <line x1="12" y1="8" x2="12" y2="13"></line>
                    <circle cx="12" cy="17" r="1"></circle>
                  </svg>
                </div>
              <?php endif; ?>
            </section>

            

            <!-- Leave Request -->
            <section class="section leave-request" aria-label="Leave Request">
              <div class="section-title" style="display:flex; align-items:center; gap:8px;">
                Leave Request
                <span style="font-size:0.9rem; font-weight: 600; cursor: default;" title="Info">ⓘ</span>
              </div>
              <div aria-hidden="true" class="leave-request">
                <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="2" y1="22" x2="22" y2="2"></line>
                  <line x1="7" y1="7" x2="12" y2="12"></line>
                  <line x1="12" y1="12" x2="17" y2="17"></line>
                </svg>
              </div>
            </section>
          </div>

          <!-- Right side small columns -->
          <div style="flex: 1 1 0; display:flex; flex-direction: column; gap:12px;">
            <!-- Who's on Leave -->
            <section class="section whos-on-leave" aria-label="Who's on Leave">
              <div class="section-title">Who's on Leave</div>
              <div class="filter" role="region" aria-label="On leave filter">
                On Leave: <span class="count">0</span>
                <select aria-label="Filter date">
                  <option>Today</option>
                  <option>Tomorrow</option>
                  <option>This Week</option>
                </select>
              </div>
              <div aria-hidden="true" style="text-align:center; padding-top: 30px;">
                <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="#c9c9c9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="9" y1="9" x2="15" y2="15"></line>
                  <line x1="15" y1="9" x2="9" y2="15"></line>
                </svg>
              </div>
            </section>

            <!-- Celebrations this month -->
            <section class="section celebrations" aria-label="Celebrations this month">
              <div class="section-title">Celebrations this month</div>
              <div aria-hidden="true" style="text-align:center; padding-top: 30px;">
                <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="#c9c9c9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="9" y1="9" x2="15" y2="15"></line>
                  <line x1="15" y1="9" x2="9" y2="15"></line>
                </svg>
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
<footer>
  <?php  include_once 'footer.php';?>
</footer>
</html>
