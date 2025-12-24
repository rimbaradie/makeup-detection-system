document.addEventListener("DOMContentLoaded", () => {
  fetch('dashboard_data.php')
    .then(res => res.json())
    .then(response => {
      if (response.status === 'success') {
        const data = response.data;

        // Update stat cards
        document.getElementById('totalUsers').textContent = data.total_users;
        document.getElementById('pendingOrders').textContent = data.pending_orders;
        document.getElementById('newComments').textContent = data.new_comments;
        document.getElementById('monthlyRevenue').textContent = `$${parseFloat(data.monthly_revenue).toLocaleString()}`;
        document.getElementById('activeDermatologists').textContent = data.active_dermatologists;
        document.getElementById('productSkus').textContent = data.product_skus;

        // Render charts
        renderCharts(data);
      } else {
        console.error("Failed to load data:", response.message);
      }
    })
    .catch(error => console.error("Fetch error:", error));
});

function renderCharts(data) {
  // Sales Chart
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: data.sales_data.labels,
      datasets: [{
        label: 'Sales ($)',
        data: data.sales_data.values,
        backgroundColor: 'rgba(54, 162, 235, 0.3)',
        borderColor: 'rgb(54, 162, 235)',
        borderWidth: 2,
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Registrations Chart
  const regCtx = document.getElementById('registrationsChart').getContext('2d');
  new Chart(regCtx, {
    type: 'bar',
    data: {
      labels: data.registrations_data.labels,
      datasets: [{
        label: 'User Registrations',
        data: data.registrations_data.values,
        backgroundColor: 'rgba(255, 99, 132, 0.5)',
        borderColor: 'rgb(255, 99, 132)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true } }
    }
  });
}