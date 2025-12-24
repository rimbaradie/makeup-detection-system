// === DOM REFERENCES ===
const addUserBtn = document.getElementById('addUserBtn');
const userModal = document.getElementById('add-edit-user-modal');
const userModalTitle = document.getElementById('user-modal-title');
const userForm = document.getElementById('user-form');
const userIdField = document.getElementById('user-id-field');
const userNameInput = document.getElementById('user-name');
const userEmailInput = document.getElementById('user-email');
const userSkinTypeSelect = document.getElementById('user-skin-type');
const userRoleSelect = document.getElementById('user-role');
const userPasswordGroup = document.getElementById('user-password-group');
const userPasswordInput = document.getElementById('user-password');
const userFnameInput = document.getElementById('user-fname');
const userLnameInput = document.getElementById('user-lname');
const userProfileInput = document.getElementById('user-profile');
const searchInput = document.querySelector('.search-input');
const filterSelect = document.querySelector('.filter-select');
const tbody = document.querySelector('tbody');

// === VIEW MODAL DOM REFERENCES ===
const viewModal = document.getElementById('view-user-modal');
const viewProfileImg = document.getElementById('view-user-profile');
const viewUserName = document.getElementById('view-user-name');
const viewUserEmail = document.getElementById('view-user-email');
const viewUserRole = document.getElementById('view-user-role');
const viewUserSkin = document.getElementById('view-user-skin');
const viewUserFname = document.getElementById('view-user-fname');
const viewUserLname = document.getElementById('view-user-lname');

// === MODAL CONTROLS ===
function openModal(id) {
    document.getElementById(id).style.display = 'block';
}

function closeModals() {
    document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
}

// === FORM SUBMISSION ===
userForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const userId = userIdField.value;
    const url = userId ? 'update_user.php' : 'add_user.php';
    const formData = new FormData();

    // Collect form data
    formData.append('username', userNameInput.value);
    formData.append('email', userEmailInput.value);
    formData.append('role', userRoleSelect.value);
    formData.append('skin_type', userSkinTypeSelect.value);
    formData.append('fname', userFnameInput.value);
    formData.append('lname', userLnameInput.value);
    if (!userId) {
        formData.append('password', userPasswordInput.value);
    } else {
        formData.append('user_id', userId);
    }
    if (userProfileInput.files[0]) {
        formData.append('profile', userProfileInput.files[0]);
    }

    // Submit
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success' || data.status === 'updated') {
            showToast(`User ${userNameInput.value} ${userId ? 'updated' : 'added'} successfully!`, 'success');
            refreshUserTable();
        } else {
            showToast(`Error: ${data.message || 'Unknown error'}`, 'error');
        }
        closeModals();
    })
    .catch(err => {
        console.error(err);
        showToast('Network or server error.', 'error');
    });
});

// === REFRESH USERS TABLE ===
function refreshUserTable() {
    fetch('get_users.php')
        .then(res => res.text())
        .then(html => {
            tbody.innerHTML = html;
            attachEventListeners();
            filterTable(); // Apply current filter after reload
        });
}

// === EDIT USER ===
function editUserHandler(event) {
    const row = event.target.closest('tr');
    const userId = row.dataset.userId;

    userModalTitle.textContent = 'Edit User';
    userPasswordGroup.style.display = 'none';

    userIdField.value = userId;
    userNameInput.value = row.children[1].textContent;
    userEmailInput.value = row.children[2].textContent;
    userSkinTypeSelect.value = row.children[3].textContent;
    userRoleSelect.value = row.children[4].textContent.toLowerCase();

    openModal('add-edit-user-modal');
}

// === DELETE USER ===
function deleteUserHandler(event) {
    const row = event.target.closest('tr');
    const userId = row.dataset.userId;

    if (confirm('Are you sure you want to delete this user?')) {
        fetch('delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'deleted') {
                showToast(`User ${userId} deleted.`, 'error');
                refreshUserTable();
            } else {
                showToast('Error deleting user.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Network or server error.', 'error');
        });
    }
}

// === VIEW USER DETAILS ===
function viewUserHandler(event) {
    const row = event.target.closest('tr');
    const userId = row.dataset.userId;

    fetch('view_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `user_id=${userId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const user = data.user;

            viewUserName.textContent = user.username;
            viewUserEmail.textContent = user.email;
            viewUserRole.textContent = user.role;
            viewUserSkin.textContent = user.skin_type;
            viewUserFname.textContent = user.fname || '';
            viewUserLname.textContent = user.lname || '';
            viewProfileImg.src = user.profile ? user.profile : 'default-profile.png';

            openModal('view-user-modal');
        } else {
            showToast('User not found', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Failed to load user data', 'error');
    });
}

// === FILTER TABLE ===
function filterTable() {
    const searchText = searchInput.value.toLowerCase();
    const selectedRole = filterSelect.value;

    tbody.querySelectorAll('tr').forEach(row => {
        const username = row.children[1].textContent.toLowerCase();
        const role = row.children[4].textContent.toLowerCase();

        const matchesSearch = username.includes(searchText);
        const matchesRole = selectedRole === 'all' || role === selectedRole;

        row.style.display = matchesSearch && matchesRole ? '' : 'none';
    });
}

// === ATTACH EVENT LISTENERS ===
function attachEventListeners() {
    // Remove old listeners by cloning and replacing buttons

    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.replaceWith(newBtn);
        newBtn.addEventListener('click', editUserHandler);
    });

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.replaceWith(newBtn);
        newBtn.addEventListener('click', deleteUserHandler);
    });

    document.querySelectorAll('.view-user-btn').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.replaceWith(newBtn);
        newBtn.addEventListener('click', viewUserHandler);
    });
}

// === INITIAL SETUP ===
if (addUserBtn) {
    addUserBtn.addEventListener('click', () => {
        userModalTitle.textContent = 'Add New User';
        userForm.reset();
        userIdField.value = '';
        userPasswordGroup.style.display = 'block';
        openModal('add-edit-user-modal');
    });
}

searchInput.addEventListener('input', filterTable);
filterSelect.addEventListener('change', filterTable);

// === LOAD INITIAL TABLE ===
refreshUserTable();