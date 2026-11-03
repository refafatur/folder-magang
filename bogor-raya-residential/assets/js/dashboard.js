function toggleSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
}
// Chart initialization code...
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a page that needs charts
    const chartCanvas = document.getElementById('myChart');
    if (chartCanvas) {
        initializeCharts();
    }
    initializeInteractions();
});

function initializeCharts() {
    // Traffic Overview Chart
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Website Traffic',
                data: [65, 59, 80, 81, 56, 55, 70],
                fill: true,
                backgroundColor: gradient,
                borderColor: '#6366f1',
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Additional chart initializations...
}

function initializeInteractions() {
    // Add hover effects to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseover', function() {
            this.querySelector('.stat-icon').style.transform = 'scale(1.1)';
        });
        
        card.addEventListener('mouseout', function() {
            this.querySelector('.stat-icon').style.transform = 'scale(1)';
        });
    });

    // Add smooth transitions for chart type switching
    document.querySelectorAll('.chart-actions .btn-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.chart-actions .btn-link').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Update chart data here
        });
    });
}

function exportReport() {
    alert('Downloading report...');
    // Implement actual report download functionality
}