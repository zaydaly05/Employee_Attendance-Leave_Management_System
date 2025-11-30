
        let dashboardData = {
            totalUsers: 0,
            totalLeaves: 0,
            celebrations: []
        };

        function fetchDashboardData() {
            return new Promise((resolve) => {
                setTimeout(() => {
                    const data = {
                        totalUsers: 156,
                        totalLeaves: 23,
                        celebrations: [
                            {
                                name: "Ahmed Mohamed",
                                type: "Birthday",
                                date: "Nov 22, 2025"
                            },
                            {
                                name: "Sara Hassan",
                                type: "Work Anniversary",
                                date: "Nov 25, 2025"
                            },
                            {
                                name: "Mohamed Ali",
                                type: "Birthday",
                                date: "Nov 28, 2025"
                            },
                            {
                                name: "Fatima Ibrahim",
                                type: "Work Anniversary",
                                date: "Dec 1, 2025"
                            }
                        ]
                    };
                    resolve(data);
                }, 500);
            });
        }

        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function updateTotalUsers(count) {
            const element = document.getElementById('totalUsers');
            animateValue(element, 0, count, 1000);
        }

        function updateTotalLeaves(count) {
            const element = document.getElementById('totalLeaves');
            animateValue(element, 0, count, 1000);
        }

        function updateCelebrations(celebrations) {
            const listElement = document.getElementById('celebrationList');
            
            if (celebrations.length === 0) {
                listElement.innerHTML = '<p class="card-description">No upcoming celebrations</p>';
                return;
            }

            let html = '';
            celebrations.forEach(celebration => {
                html += `
                    <div class="celebration-item">
                        <div class="celebration-name">${celebration.name}</div>
                        <div class="celebration-date"><i class="fas fa-calendar"></i> ${celebration.date}</div>
                        <span class="celebration-type">${celebration.type}</span>
                    </div>
                `;
            });
            listElement.innerHTML = html;
        }

        async function refreshDashboard() {
            const btn = document.querySelector('.refresh-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Refreshing...';
            btn.disabled = true;

            const data = await fetchDashboardData();
            
            updateTotalUsers(data.totalUsers);
            updateTotalLeaves(data.totalLeaves);
            updateCelebrations(data.celebrations);

            dashboardData = data;

            btn.innerHTML = '<i class="fas fa-sync-alt"></i>Refresh Data';
            btn.disabled = false;
        }

        window.addEventListener('DOMContentLoaded', () => {
            refreshDashboard();
        });

        setInterval(() => {
            refreshDashboard();
        }, 300000);