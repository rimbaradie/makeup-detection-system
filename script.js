document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.main-nav a');
    const contentSections = document.querySelectorAll('.content-section');
    const currentSectionTitle = document.querySelector('.current-section-title');
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.modal .close-button');
    const toastContainer = document.getElementById('toast-container');

    // --- Utility Functions ---

    // Function to show a toast notification
    function showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.classList.add('toast', type);
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`; // Default icon

        if (type === 'error') toast.innerHTML = `<i class="fas fa-times-circle"></i> ${message}`;
        if (type === 'warning') toast.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        if (type === 'info') toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;

        toastContainer.appendChild(toast);

        // Animate out after duration
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(50px)';
            toast.addEventListener('transitionend', () => toast.remove());
        }, duration);
    }

    // Function to hide all sections and show the active one
    function showSection(sectionId) {
        contentSections.forEach(section => {
            section.classList.remove('active');
            section.classList.remove('fade-in'); // Reset animation
        });

        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.add('active');
            setTimeout(() => {
                targetSection.classList.add('fade-in'); // Re-apply animation
            }, 50); // Small delay to allow class removal to register
        }
    }

    // Function to set active navigation link
    function setActiveNavLink(clickedLink) {
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        clickedLink.classList.add('active');
    }

    // Function to open a specific modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex'; // Use flex to center content
        }
    }

    // Function to close all modals
    function closeModals() {
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }

    // --- Global Event Listeners ---

    // Navigation click handler
    navLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const sectionId = link.dataset.section;

            showSection(sectionId);
            setActiveNavLink(link);

            currentSectionTitle.textContent = link.textContent.trim();
        });
    });

    // Initialize: Show Dashboard on page load and set active link
    const initialSectionId = 'dashboard';
    showSection(initialSectionId);
    document.querySelector(`.main-nav a[data-section="${initialSectionId}"]`).classList.add('active');

    // Close buttons for modals
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });

    // Close modal if user clicks outside the modal content
    window.addEventListener('click', (event) => {
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Logout Button
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to log out?')) {
                showToast('Logged out successfully!', 'info');
                // Simulate logout: redirect to login page (if one exists)
                // window.location.href = '/login.html';
            }
        });
    }

    // Profile Dropdown Logout Button
    const logoutBtnDropdown = document.querySelector('.logout-btn-dropdown');
    if (logoutBtnDropdown) {
        logoutBtnDropdown.addEventListener('click', (e) => {
             e.preventDefault(); // Prevent default link behavior
            if (confirm('Are you sure you want to log out?')) {
                showToast('Logged out successfully!', 'info');
                // window.location.href = '/login.html';
            }
        });
    }


    // --- Section Specific Features ---

    // User Management: Add/Edit User Modal
// Elements
// User Management: Add/Edit User Modal



    // Orders: Change Status Button (Conceptual)
    document.querySelectorAll('.change-status-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            const orderId = row.dataset.orderId;
            showToast(`Order ${orderId}: Status change initiated. (Dropdown or sub-modal would appear)`, 'info', 4000);
            // In a real app, this would open a dropdown or a small modal
            // to select new status (Pending, Processing, Shipped, Delivered, Cancelled)
        });
    });

    // Reviews & Testimonials: Actions
    document.querySelectorAll('.approve-review-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const reviewItem = event.target.closest('.comment-item');
            showToast('Review approved!', 'success');
            // Update UI/Backend
            reviewItem.querySelector('.comment-header h5').innerHTML += ' <span class="status-approved">Approved</span>';
            event.target.style.display = 'none'; // Hide approve button
        });
    });
    document.querySelectorAll('.delete-review-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            if (confirm('Are you sure you want to delete this review?')) {
                showToast('Review deleted!', 'error');
                event.target.closest('.comment-item').remove();
            }
        });
    });
    document.querySelectorAll('.flag-review-btn').forEach(button => {
        button.addEventListener('click', () => {
            showToast('Review flagged for moderation!', 'warning');
            // Update UI/Backend
        });
    });

  
    

    // Blog & Content: Create New Post (Conceptual Rich Text Editor)
    const createBlogPostBtn = document.getElementById('createBlogPostBtn');
    if (createBlogPostBtn) {
        createBlogPostBtn.addEventListener('click', () => {
            showToast('This would open a rich text editor for creating a new blog post.', 'info', 4000);
            // Integrate a rich text editor like TinyMCE or Quill.js
        });
    }


    // --- Third-Party Integrations ---

   
  

});