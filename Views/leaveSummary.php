<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - digg</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>Public/Css/User Dashboard.css" />
  <link rel="stylesheet" href="<?php echo $base_url; ?>Public/Css/leaveSummary.css" />
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">digg</div>
      <nav>
        <a href="<?php echo $base_url; ?>dashboard" class="menu-item active">
          <span class="icon" aria-hidden="true">▦</span>
          Dashboard
        </a>
        <a href="<?php echo $base_url; ?>history" class="menu-item">
          <span class="icon" aria-hidden="true">⏲️</span>
          History
        </a>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="main">
      <header>
        <button class="icon-btn" aria-label="Notifications">🔔</button>
        <button class="icon-btn profile" aria-label="User Profile">
          <div class="profile-circle" aria-hidden="true">Z</div> ⌵
        </button>
      </header>

      <main class="content" role="main">
        <h1>My Leaves</h1>

        <!-- Leave list table -->
        <table class="leave-table" aria-label="Leave list" style="width:100%; border-collapse: collapse;">
          <thead>
            <tr style="background:#f4f4f4;">
              <th style="padding:8px; border-bottom:1px solid #ccc;">Leave ID</th>
              <th style="padding:8px; border-bottom:1px solid #ccc;">Reason</th>
              <th style="padding:8px; border-bottom:1px solid #ccc;">Start Date</th>
              <th style="padding:8px; border-bottom:1px solid #ccc;">Status</th>
            </tr>
          </thead>
          <tbody>
            <% leaves.forEach(leave => { %>
              <tr data-leave-id="<%= leave._id %>" class="leave-row" tabindex="0" role="button" aria-pressed="false" style="cursor:pointer; border-bottom:1px solid #eee;">
                <td style="padding:8px;"><%= leave._id %></td>
                <td style="padding:8px;"><%= leave.reason.length > 40 ? leave.reason.substring(0, 40) + '...' : leave.reason %></td>
                <td style="padding:8px;"><%= new Date(leave.startDate).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }) %></td>
                <td style="padding:8px;"><%= leave.status %></td>
              </tr>
            <% }) %>
          </tbody>
        </table>
      </main>
    </div>
  </div>

  <!-- Leave Detail Modal -->
  <div id="leave-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-desc" hidden>
    <div class="modal-content" role="document" tabindex="0">
      <header class="modal-header">
        <h2 id="modal-title">Leave</h2>
        <button type="button" aria-label="Close Leave Modal" id="close-leave-modal" class="close-btn">&times;</button>
      </header>

      <section id="modal-desc" class="modal-body">
        <dl class="leave-details">
          <div class="detail-row">
            <dt>Start Date</dt>
            <dd id="start-date"></dd>
          </div>
          <div class="detail-row">
            <dt>End Date</dt>
            <dd id="end-date"></dd>
          </div>
          <div class="detail-row">
            <dt>Reason</dt>
            <dd id="reason" style="white-space: pre-wrap;"></dd>
          </div>
          <div class="detail-row">
            <dt>Type</dt>
            <dd id="leave-type"></dd>
          </div>
          <div class="detail-row">
            <dt>Days</dt>
            <dd id="days"></dd>
          </div>
          <div class="detail-row">
            <dt>Request Date</dt>
            <dd id="request-date"></dd>
          </div>
          <div class="detail-row">
            <dt>Status</dt>
            <dd id="status-text"><span id="status" class="status-badge"></span></dd>
          </div>
        </dl>
      </section>
    </div>
  </div>

  <!-- JavaScript: dynamic modal loading -->
  <script>
    // Format date string like "27 Oct, 2025"
    function formatDate(dateStr) {
      if (!dateStr) return '';
      const options = { day: '2-digit', month: 'short', year: 'numeric' };
      const d = new Date(dateStr);
      return d.toLocaleDateString('en-US', options);
    }

    // Map status to badge styles & text
    function updateStatusBadge(statusElem, status) {
      const statusClassMap = {
        Pending: 'pending',
        Approved: 'approved',
        Rejected: 'rejected',
      };
      statusElem.textContent = status;
      statusElem.className = 'status-badge ' + (statusClassMap[status] || '');
    }

    const modal = document.getElementById('leave-modal');
    const closeBtn = document.getElementById('close-leave-modal');
    const startDateEl = document.getElementById('start-date');
    const endDateEl = document.getElementById('end-date');
    const reasonEl = document.getElementById('reason');
    const leaveTypeEl = document.getElementById('leave-type');
    const daysEl = document.getElementById('days');
    const requestDateEl = document.getElementById('request-date');
    const statusEl = document.getElementById('status');

    async function openLeaveModal(leaveId) {
      try {
        const response = await fetch(`/api/leave/${leaveId}`);
        if (!response.ok) throw new Error('Failed to load leave data');
        const leave = await response.json();

        startDateEl.textContent = formatDate(leave.startDate);
        endDateEl.textContent = formatDate(leave.endDate);
        reasonEl.textContent = leave.reason;
        leaveTypeEl.textContent = leave.type || leave.leaveType || 'N/A';
        daysEl.textContent = leave.days ? leave.days.toString().padStart(2,'0') : '01';
        requestDateEl.textContent = formatDate(leave.requestDate);
        updateStatusBadge(statusEl, leave.status);

        modal.hidden = false;
        modal.querySelector('.modal-content').focus();
      } catch (err) {
        console.error(err);
        alert('Error loading leave details.');
      }
    }

    closeBtn.addEventListener('click', () => {
      modal.hidden = true;
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === "Escape" && !modal.hidden) modal.hidden = true;
    });

    document.querySelectorAll('.leave-row').forEach(row => {
      row.addEventListener('click', () => {
        const leaveId = row.getAttribute('data-leave-id');
        if (leaveId) openLeaveModal(leaveId);
      });
      row.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const leaveId = row.getAttribute('data-leave-id');
          if (leaveId) openLeaveModal(leaveId);
        }
      });
    });
  </script>
</body>
</html>