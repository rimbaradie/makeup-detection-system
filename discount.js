
    // Marketing & Promotions: Tab Switching
    document.querySelectorAll('#marketing .tab-button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#marketing .tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('#marketing .tab-content').forEach(content => content.classList.remove('active', 'fade-in'));

            button.classList.add('active');
            const targetTabId = button.dataset.tab;
            const targetContent = document.getElementById(targetTabId);
            if (targetContent) {
                targetContent.classList.add('active');
                setTimeout(() => targetContent.classList.add('fade-in'), 50);
            }
        });
    });
    
    document.getElementById('createDiscountBtn').addEventListener('click', () => {
    document.getElementById('createDiscountModal').style.display = 'flex';
      });

    function closeModal() {
    document.getElementById('createDiscountModal').style.display = 'none';
      }
      document.getElementById('discountForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('add_discount.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            closeModal();
            loadDiscounts(); // reload the table
        } else {
            alert(data.message);
        }
    });
});
function loadDiscounts() {
    fetch('load_discounts.php')
        .then(res => res.text())
        .then(html => {
            document.querySelector('#discounts tbody').innerHTML = html;
        });
}

window.addEventListener('DOMContentLoaded', loadDiscounts);
function closeEditModal() {
    document.getElementById('editDiscountModal').style.display = 'none';
}

document.querySelector('#discounts').addEventListener('click', e => {
    if (e.target.closest('.edit-btn')) {
        const btn = e.target.closest('.edit-btn');
        document.getElementById('edit_sale_id').value = btn.dataset.id;
        document.getElementById('edit_product_id').value = btn.dataset.product;
        document.getElementById('edit_discount_percentage').value = btn.dataset.discount;
        document.getElementById('edit_start_date').value = btn.dataset.start;
        document.getElementById('edit_end_date').value = btn.dataset.end;

        document.getElementById('editDiscountModal').style.display = 'flex';
    }
});

document.getElementById('editDiscountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('edit_discount.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            closeEditModal();
            loadDiscounts();
        }
    });
});
document.addEventListener('click', function (e) {
  if (e.target.closest('.btn-danger')) {  // your delete button class
    const btn = e.target.closest('.btn-danger');
    const saleId = btn.dataset.id;  // assuming data-id contains sale_id
    if (!saleId) return alert('Invalid ID');

    if (confirm('Are you sure you want to delete this discount?')) {
      fetch('delete_discount.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'sale_id=' + encodeURIComponent(saleId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Successfully deleted');
          loadDiscounts();  // refresh the table after delete
        } else {
          alert('Delete failed: ' + data.message);
        }
      })
      .catch(() => alert('Delete request failed'));
    }
  }
});

document.getElementById('discountSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#discounts tbody tr');

    rows.forEach(row => {
        const codeCell = row.querySelector('td:first-child'); // Code is in first column
        if (!codeCell) return;

        const codeText = codeCell.textContent.toLowerCase();
        if (codeText.includes(filter)) {
            row.style.display = ''; // show row
        } else {
            row.style.display = 'none'; // hide row
        }
    });
});
document.getElementById('orderSearchInput').addEventListener('input', function () {
  const query = this.value.toLowerCase();
  const rows = document.querySelectorAll('#orders tbody tr');

  rows.forEach(row => {
    const orderId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
    const userId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

    if (orderId.includes(query) || userId.includes(query)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
