const addProductBtn = document.getElementById('addProductBtn');
const productModal = document.getElementById('add-edit-product-modal');
const productModalTitle = document.getElementById('product-modal-title');
const productForm = document.getElementById('product-form');

const productIdField = document.getElementById('product-id-field');
const productNameInput = document.getElementById('product-name');
const productBrandInput = document.getElementById('product-brand');
const productCategoryInput = document.getElementById('product-category');
const productSkinTypeInput = document.getElementById('product-skin-type');
const productPriceInput = document.getElementById('product-price');
const productStockInput = document.getElementById('product-stock');
const productImageInput = document.getElementById('product-image');
const productImage2Input = document.getElementById('product-image2');
const productDescriptionTextarea = document.getElementById('product-description');

const imagePreview1 = document.querySelector('.image-preview1');
const imagePreview2 = document.querySelector('.image-preview2');

function openModal(id) {
    document.getElementById(id).style.display = 'block';
}

function closeModals() {
    productModal.style.display = 'none';
}

function showToast(message, type) {
    alert(message); // You can replace this with nicer toast UI later
}

if (addProductBtn) {
    addProductBtn.addEventListener('click', () => {
        productModalTitle.textContent = 'Add New Product';
        productForm.reset();
        productIdField.value = '';
        if (imagePreview1) imagePreview1.src = '';
        if (imagePreview2) imagePreview2.src = '';
        openModal('add-edit-product-modal');
    });
}

// Load all products from server and bind buttons
function loadProducts() {
    fetch('products.php')
        .then(res => res.text())
        .then(html => {
            const grid = document.querySelector('.product-grid');
            grid.innerHTML = html;
            bindActionButtons();  // Bind edit, delete, view buttons after loading
        })
        .catch(err => console.error("Failed to load products:", err));
}

document.addEventListener('DOMContentLoaded', loadProducts);

function bindActionButtons() {
    // Edit button handler
    document.querySelectorAll('.edit-product-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            productModalTitle.textContent = 'Edit Product';
            const card = event.target.closest('.product-card');
            const productId = button.getAttribute('data-id');

            productIdField.value = productId;
            productNameInput.value = card.querySelector('h4').textContent;
            productBrandInput.value = card.querySelector('.product-details p:nth-child(2)').textContent.replace('Brand: ', '').trim();
            productCategoryInput.value = card.querySelector('.product-details p:nth-child(3)').textContent.replace('Category: ', '').trim();
            productSkinTypeInput.value = card.querySelector('.product-details p:nth-child(4)').textContent.replace('Skin Type: ', '').trim();
            productPriceInput.value = parseFloat(card.querySelector('.product-details p:nth-child(5)').textContent.replace('Price: $', '').trim());
            productStockInput.value = parseInt(card.querySelector('.product-details p:nth-child(6)').textContent.replace('Stock: ', '').trim());
            productDescriptionTextarea.value = card.querySelector('.product-details p:nth-child(7)') 
                ? card.querySelector('.product-details p:nth-child(7)').textContent.replace('Description: ', '').trim()
                : '';

            if (imagePreview1) imagePreview1.src = card.querySelector('img.product-img').src;
            if (imagePreview2) imagePreview2.src = '';

            productImageInput.value = '';
            productImage2Input.value = '';

            openModal('add-edit-product-modal');
        });
    });

    // Delete button handler
    document.querySelectorAll('.delete-product-btn').forEach(button => {
        button.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this product?')) {
                const productId = button.getAttribute('data-id');

                fetch('delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === "success") {
                        showToast('Product deleted successfully.', 'success');
                        loadProducts();
                    } else {
                        showToast('Delete failed: ' + response.message, 'error');
                    }
                })
                .catch(error => {
                    console.error("Delete error:", error);
                    showToast('Error deleting product.', 'error');
                });
            }
        });
    });

    // View button handler
    document.querySelectorAll('.view-product-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            fetch(`get_product.php?product_id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showProductDetailModal(data.product);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Failed to fetch product details:', err);
                    alert('Failed to load product details.');
                });
        });
    });
}

// Handle form submit with FormData (for add or edit)
productForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const formData = new FormData(productForm);
    const isEdit = !!productIdField.value;
    const endpoint = isEdit ? 'update_product.php' : 'add_product.php';

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            showToast(`Product ${isEdit ? 'updated' : 'added'} successfully!`, 'success');
            closeModals();
            loadProducts();
        } else {
            showToast("Error: " + response.message, "error");
        }
    })
    .catch(error => {
        console.error("Form submission error:", error);
        showToast("An error occurred. Please try again.", "error");
    });
});

// Preview primary image
productImageInput.addEventListener('change', () => {
    const file = productImageInput.files[0];
    if (file && imagePreview1) {
        const reader = new FileReader();
        reader.onload = e => imagePreview1.src = e.target.result;
        reader.readAsDataURL(file);
    }
});

// Preview secondary image
productImage2Input.addEventListener('change', () => {
    const file = productImage2Input.files[0];
    if (file && imagePreview2) {
        const reader = new FileReader();
        reader.onload = e => imagePreview2.src = e.target.result;
        reader.readAsDataURL(file);
    }
});

// Show product detail modal
function showProductDetailModal(product) {
    document.getElementById('detail-name').textContent = product.name;
    document.getElementById('detail-brand').textContent = product.brand;
    document.getElementById('detail-category').textContent = product.category;
    document.getElementById('detail-skin-type').textContent = product.skin_type;
    document.getElementById('detail-price').textContent = parseFloat(product.price).toFixed(2);
    document.getElementById('detail-stock').textContent = product.stock_quantity;
    document.getElementById('detail-description').textContent = product.description || 'No description provided.';

    const img1 = document.getElementById('detail-image1');
    img1.src = product.image_product || 'https://via.placeholder.com/200';
    img1.style.display = 'block';

    const img2 = document.getElementById('detail-image2');
    if (product.image2_product) {
        img2.src = product.image2_product;
        img2.style.display = 'block';
    } else {
        img2.style.display = 'none';
    }

    document.getElementById('product-detail-modal').style.display = 'flex';
}

function closeProductDetailModal() {
    document.getElementById('product-detail-modal').style.display = 'none';
}
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.querySelector('.search-input');
  const productGrid = document.querySelector('.product-grid');

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();

    fetch(`products.php?search=${encodeURIComponent(query)}`)
      .then(response => response.text())
      .then(html => {
        productGrid.innerHTML = html;
      })
      .catch(err => {
        console.error('Error fetching filtered products:', err);
      });
  });
});
document.getElementById('save-notifications-btn').addEventListener('click', function() {
    const notifyNewOrder = document.getElementById('notify-new-order').checked ? 1 : 0;
    const notifyNewUser = document.getElementById('notify-new-user').checked ? 1 : 0;
    const notifyLowStock = document.getElementById('notify-low-stock').checked ? 1 : 0;

    fetch('save_notifications.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            notify_new_order: notifyNewOrder,
            notify_new_user: notifyNewUser,
            notify_low_stock: notifyLowStock
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Notification preferences saved!');
        } else {
            alert('Error saving preferences.');
        }
    })
    .catch(() => alert('Network error.'));
});