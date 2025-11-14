<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>History - digg Dashboard</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>Public/Css/history.css" />
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">digg</div>
      <nav>
        <a href="<?php echo $base_url; ?>dashboard">
          <span class="icon" aria-hidden="true">📊</span>
          Dashboard
        </a>
        
        <a href="<?php echo $base_url; ?>history" class="active">
          <span class="icon" aria-hidden="true">⏰</span>
          History
        </a>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="main">
      <!-- <header>
        <button class="icon-btn" aria-label="Notifications">🔔</button>
        <button class="icon-btn profile" aria-label="User Profile">👤 ⌵</button>
      </header> -->
      <?php  include_once 'header.php';?>

      <main class="content" role="main">
        <h1 class="page-title">History</h1>

        <section class="history-filters" aria-label="History Filters">
          <div class="tabs" role="tablist">
            <button role="tab" aria-selected="true" class="tab active" id="tab-present" data-status="present" tabindex="0">Present</button>
            <button role="tab" aria-selected="false" class="tab" id="tab-halfday" data-status="halfday" tabindex="-1">Half Day</button>
            <button role="tab" aria-selected="false" class="tab" id="tab-absent" data-status="absent" tabindex="-1">Absent</button>
          </div>

          <div class="period-buttons" role="group" aria-label="Select period">
            <button class="period-btn active" type="button" data-period="1w">1W</button>
            <button class="period-btn" type="button" data-period="1m">1M</button>
            <button class="period-btn" type="button" data-period="3m">3M</button>
            <button class="period-btn" type="button" data-period="6m">6M</button>
            <button class="period-btn" type="button" data-period="1y">1Y</button>
            <button class="period-btn" type="button" data-period="all">ALL</button>
          </div>

          <form class="date-range" aria-label="Select date range" onsubmit="return false;">
            <label for="start-date" class="sr-only">Start Date</label>
            <input type="date" id="start-date" name="start-date" placeholder="MM-DD-YYYY" />

            <label for="end-date" class="sr-only">End Date</label>
            <input type="date" id="end-date" name="end-date" placeholder="MM-DD-YYYY" />
          </form>
        </section>

        <section class="history-table-section" aria-labelledby="history-table-title">
          <h2 id="history-table-title" class="sr-only">History Table</h2>
          <table class="history-table" role="grid" aria-live="polite" aria-relevant="all" aria-atomic="true">
            <thead>
              <tr>
                <th scope="col">Date</th>
                <th scope="col">Day</th>
                <th scope="col">Check In</th>
                <th scope="col">Check Out</th>
                <th scope="col">Work Time</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody id="history-table-body">
              <!-- Dynamic rows inserted here -->
            </tbody>
          </table>
        </section>

      </main>
    </div>
  </div>

<script>
  // Sample dataset with dates, status and work info
  const historyData = [
    { date: "2025-09-23", day: "Monday", checkIn: "10:30 AM", checkOut: "8:30 PM", workTime: "08H 00M", status: "present" },
    { date: "2025-09-22", day: "Sunday", checkIn: "09:00 AM", checkOut: "1:00 PM", workTime: "04H 00M", status: "halfday" },
    { date: "2025-09-21", day: "Saturday", checkIn: "-", checkOut: "-", workTime: "00H 00M", status: "absent" },
    { date: "2025-09-20", day: "Friday", checkIn: "10:00 AM", checkOut: "7:00 PM", workTime: "07H 00M", status: "present" },
    // Add more records as needed
  ];

  // DOM Elements
  const tabs = document.querySelectorAll(".tab");
  const periodButtons = document.querySelectorAll(".period-btn");
  const startDateInput = document.querySelector("#start-date");
  const endDateInput = document.querySelector("#end-date");
  const tableBody = document.querySelector("#history-table-body");

  let selectedStatus = "present"; // default tab
  let selectedPeriod = "1w"; // default period

  // Helper: Parse date string "YYYY-MM-DD" to Date object
  function parseDate(dateStr) {
    const parts = dateStr.split("-");
    return new Date(parts[0], parts[1] - 1, parts[2]);
  }

  // Helper: Format Date object to "MMM-DD-YYYY" (example: Sept-23-2025)
  function formatDate(dateObj) {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    return dateObj.toLocaleDateString('en-US', options).replace(',', '');
  }

  // Calculate the start date for given period
  function getPeriodStartDate(period) {
    const now = new Date();
    switch(period){
      case "1w": return new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
      case "1m": return new Date(now.getFullYear(), now.getMonth() -1, now.getDate());
      case "3m": return new Date(now.getFullYear(), now.getMonth() -3, now.getDate());
      case "6m": return new Date(now.getFullYear(), now.getMonth() -6, now.getDate());
      case "1y": return new Date(now.getFullYear() -1, now.getMonth(), now.getDate());
      case "all": return new Date(0); // all time
      default: return new Date(0);
    }
  }

  // Filter data based on selected status and period or date range
  function filterData() {
    let filtered = historyData.filter(rec => rec.status === selectedStatus);

    // Apply date range filter if inputs have values
    const startInput = startDateInput.value;
    const endInput = endDateInput.value;

    if (startInput) {
      const start = new Date(startInput);
      filtered = filtered.filter(rec => parseDate(rec.date) >= start);
    } else {
      // apply period filter if no start date chosen
      const periodStart = getPeriodStartDate(selectedPeriod);
      filtered = filtered.filter(rec => parseDate(rec.date) >= periodStart);
    }

    if (endInput) {
      const end = new Date(endInput);
      filtered = filtered.filter(rec => parseDate(rec.date) <= end);
    }

    // Sort by date descending (most recent first)
    filtered.sort((a,b) => parseDate(b.date) - parseDate(a.date));

    return filtered;
  }

  // Render table rows dynamically
  function renderTable() {
    const records = filterData();
    if(records.length === 0){
      tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 20px; color: #666;">No records found</td></tr>`;
      return;
    }
    let html = "";
    records.forEach(rec => {
      html += `
        <tr>
          <td>${formatDate(parseDate(rec.date))}</td>
          <td>${rec.day}</td>
          <td>${rec.checkIn}</td>
          <td>${rec.checkOut}</td>
          <td>${rec.workTime}</td>
          <td>${capitalize(rec.status)}</td>
        </tr>
      `;
    });
    tableBody.innerHTML = html;
  }

  // Capitalize first letter helper
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  // Setup event listeners for tabs
  tabs.forEach(tab => {
    tab.addEventListener("click", e => {
      // Update aria and classes for tabs
      tabs.forEach(t => {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
        t.setAttribute("tabindex", "-1");
      });
      e.currentTarget.classList.add("active");
      e.currentTarget.setAttribute("aria-selected", "true");
      e.currentTarget.setAttribute("tabindex", "0");
      selectedStatus = e.currentTarget.dataset.status;
      renderTable();
    });
  });

  // Setup event listeners for period buttons
  periodButtons.forEach(btn => {
    btn.addEventListener("click", e => {
      periodButtons.forEach(b => b.classList.remove("active"));
      e.currentTarget.classList.add("active");
      selectedPeriod = e.currentTarget.dataset.period;
      // Clear date inputs when using a preset period
      startDateInput.value = "";
      endDateInput.value = "";
      renderTable();
    });
  });

  // Date inputs event to clear period buttons' active when typing custom date range
  [startDateInput, endDateInput].forEach(input => {
    input.addEventListener("change", e => {
      // Remove active class from period buttons because manual date selected
      periodButtons.forEach(b => b.classList.remove("active"));
      renderTable();
    });
  });

  // Initial render
  renderTable();
</script>
</body>
</html>