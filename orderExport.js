document.addEventListener('DOMContentLoaded', () => {
    console.log('JS loaded ✅');

    const tbody = document.getElementById('ordersTableBody');
    const modal = document.getElementById('order-detail-modal');
    const modalContent = document.getElementById('order-detail-content');
    const modalCloseBtn = document.getElementById('close-order-detail');
    const exportBtn = document.getElementById('export-orders-btn');

    if (!tbody) {
        console.error('❌ ordersTableBody not found!');
        return;
    }

    if (!exportBtn) {
        console.error('❌ Export button not found!');
        return;
    }

    // Load orders on page load
    loadOrders();

    function loadOrders() {
        tbody.innerHTML = '<tr><td colspan="7">Loading orders...</td></tr>';

        fetch('fetch_orders.php')
            .then(response => response.text())
            .then(data => {
                tbody.innerHTML = data;
            })
            .catch(error => {
                console.error('Failed to fetch orders:', error);
                tbody.innerHTML = '<tr><td colspan="7">Failed to load orders.</td></tr>';
            });
    }

    // Handle button actions in the table
    tbody.addEventListener('click', (event) => {
        const btn = event.target.closest('button');
        if (!btn) return;

        const orderRow = btn.closest('tr');
        const orderId = orderRow?.getAttribute('data-order-id');

        if (!orderId) {
            alert('Invalid order ID.');
            return;
        }

        if (btn.classList.contains('view-order-btn')) {
            showOrderDetails(orderId);
        } else if (btn.classList.contains('change-status-btn')) {
            changeOrderStatus(orderId);
        } else if (btn.classList.contains('print-invoice-btn')) {
            printInvoice(orderId);
        }
    });

    // Show order details in modal (excluding price and quantity)
    function showOrderDetails(orderId) {
        fetch(`get_order_details.php?order_id=${encodeURIComponent(orderId)}`)
            .then(res => res.json())
            .then(order => {
                if (order.error) {
                    alert(order.error);
                    return;
                }

                modalContent.innerHTML = `
                    <h3>Order ID: ${order.order_id}</h3>
                    <p><strong>User ID:</strong> ${order.user_id}</p>
                    <p><strong>Product ID:</strong> ${order.product_id}</p>
                    <p><strong>Total Amount:</strong> $${order.total_amount}</p>
                    <p><strong>Status:</strong> ${order.status}</p>
                    <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                    <p><strong>Order Date:</strong> ${order.order_date}</p>
                `;

                modal.style.display = 'block';
            })
            .catch(err => {
                console.error('Error loading order details:', err);
                alert('Failed to load order details.');
            });
    }

    // Change order status
    function changeOrderStatus(orderId) {
        const validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        const newStatus = prompt(`Enter new status: ${validStatuses.join(', ')}`);

        if (!newStatus || !validStatuses.includes(newStatus.toLowerCase())) {
            alert('Invalid status entered.');
            return;
        }

        fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, new_status: newStatus.toLowerCase() })
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Status updated successfully.');
                    loadOrders();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(err => {
                console.error('Error updating order:', err);
                alert('Failed to update status.');
            });
    }

    // Print invoice
    function printInvoice(orderId) {
        window.open(`print_invoice.php?order_id=${encodeURIComponent(orderId)}`, '_blank');
    }

    // Modal close
    modalCloseBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    });

    // Export orders to CSV
    exportBtn.addEventListener('click', () => {
        console.log('🟢 Export button clicked');
        const link = document.createElement('a');
        link.href = 'export_orders.php';
        link.setAttribute('download', 'orders_export.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});