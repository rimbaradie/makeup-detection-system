document.addEventListener("DOMContentLoaded", function () {

    // 🟦 Weekly User Registrations (Real data from PHP)
    const registrationsCtx = document.getElementById('registrationsChart');
    if (registrationsCtx) {
        fetch('get_weekly_registrations.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    registrationsCtx.parentNode.innerHTML = '<p>Error loading registration data.</p>';
                    return;
                }

                new Chart(registrationsCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'New Users',
                            data: data.data,
                            backgroundColor: [
                                'rgba(52, 152, 219, 0.7)',
                                'rgba(155, 89, 182, 0.7)',
                                'rgba(241, 196, 15, 0.7)',
                                'rgba(230, 126, 34, 0.7)'
                            ],
                            borderColor: [
                                'rgba(52, 152, 219, 1)',
                                'rgba(155, 89, 182, 1)',
                                'rgba(241, 196, 15, 1)',
                                'rgba(230, 126, 34, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Weekly User Registrations'
                            },
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            })
            .catch(err => {
                console.error('Error loading weekly registrations:', err);
                registrationsCtx.parentNode.innerHTML = '<p>Error loading registration data.</p>';
            });
    }

    // 🟢 Product Sales by Category (Dynamic from PHP)
    const categorySalesCtx = document.getElementById('categorySalesChart');
    if (categorySalesCtx) {
        fetch('get_category_sales.php')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.category);
                const values = data.map(item => item.total_sales);

                new Chart(categorySalesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales ($)',
                            data: values,
                            backgroundColor: [
                                'rgba(26, 188, 156, 0.8)',
                                'rgba(52, 152, 219, 0.8)',
                                'rgba(155, 89, 182, 0.8)',
                                'rgba(241, 196, 15, 0.8)',
                                'rgba(230, 126, 34, 0.8)',
                                'rgba(231, 76, 60, 0.8)',
                                'rgba(39, 174, 96, 0.8)'
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Product Sales by Category'
                            },
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading category sales:', error));
    }

    // 🔵 User Demographics by Age (Dynamic from PHP)
    const ageDemographicsCtx = document.getElementById('ageDemographicsChart');
    if (ageDemographicsCtx) {
        fetch('get_age_demographics.php')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.age_range);
                const values = data.map(item => item.user_count);

                new Chart(ageDemographicsCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Users',
                            data: values,
                            backgroundColor: 'rgba(52, 73, 94, 0.7)',
                            borderColor: 'rgba(52, 73, 94, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'User Demographics by Age'
                            },
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading age demographics:', error));
    }

    // 🔍 Live Navigation Search
    document.getElementById('panelSearchInput')?.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const navItems = document.querySelectorAll('.main-nav ul li');

        navItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // 📊 Dashboard Metrics: Conversion Rate, Avg Order Value, etc.
    fetch('get_dashboard_metrics.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('conversion-rate').textContent = data.conversion_rate;
            document.getElementById('avg-order-value').textContent = data.avg_order_value;
            document.getElementById('website-traffic').textContent = data.website_traffic;
            document.getElementById('top-selling-product').textContent = data.top_product;
        })
        .catch(error => {
            console.error('Error loading dashboard metrics:', error);
            document.getElementById('conversion-rate').textContent = 'Error';
            document.getElementById('avg-order-value').textContent = 'Error';
            document.getElementById('website-traffic').textContent = 'Error';
            document.getElementById('top-selling-product').textContent = 'Error';
        });

});

// Custom Report Generator Function
function generateCustomReport() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const resultDiv = document.getElementById('report-result');

    // Clear previous results
    resultDiv.innerHTML = '';

    if (!startDate || !endDate) {
        alert('Please select both start and end dates.');
        return;
    }
    if (startDate > endDate) {
        alert('Start date cannot be after end date.');
        return;
    }

    // Send dates via POST to PHP
    fetch('generate_report.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            resultDiv.innerHTML = `<p style="color:red;">${data.error}</p>`;
            return;
        }

        // Show report summary
        resultDiv.innerHTML = `
            <h4>Report from ${startDate} to ${endDate}</h4>
            <p><strong>Total Orders:</strong> ${data.total_orders}</p>
            <p><strong>Total Sales:</strong> $${parseFloat(data.total_sales).toFixed(2)}</p>
            <p><strong>Top Selling Product:</strong> ${data.top_product}</p>
        `;
    })
    .catch(error => {
        resultDiv.innerHTML = `<p style="color:red;">Error loading report</p>`;
        console.error('Error:', error);
    });
}