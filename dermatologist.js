// DOM Elements
const addDermBtn = document.getElementById('addDermBtn');
const dermModal = document.getElementById('add-edit-derm-modal');
const dermModalTitle = document.getElementById('derm-modal-title');
const dermForm = document.getElementById('derm-form');
const dermIdField = document.getElementById('dermatologist-id-field');

const dermFnameInput = document.getElementById('derm-fname');
const dermLnameInput = document.getElementById('derm-lname');
const dermNameInput = document.getElementById('derm-name');
const dermPasswordInput = document.getElementById('derm-password');
const dermPhoneInput = document.getElementById('derm-phone');
const dermSpecialtyInput = document.getElementById('derm-specialty');
const dermEmailInput = document.getElementById('derm-email');
const dermLocationInput = document.getElementById('derm-location');
const dermNextAvailableInput = document.getElementById('derm-next-available');
const dermStatusInput = document.getElementById('derm-status');
const dermProfileInput = document.getElementById('derm-profile');
const dermProfilePreview = document.getElementById('profile-preview');
const passwordGroup = dermPasswordInput?.closest('.form-group');

// View Modal Elements
const viewDermModal = document.getElementById('view-derm-modal');
const viewDermName = document.getElementById('view-derm-name');
const viewDermFname = document.getElementById('view-derm-fname');
const viewDermLname = document.getElementById('view-derm-lname');
const viewDermUsername = document.getElementById('view-derm-username');
const viewDermProfile = document.getElementById('view-derm-profile');
const viewDermSpecialty = document.getElementById('view-derm-specialty');
const viewDermPhone = document.getElementById('view-derm-phone');
const viewDermEmail = document.getElementById('view-derm-email');
const viewDermLocation = document.getElementById('view-derm-location');
const viewDermNextAvailable = document.getElementById('view-derm-next-available');
const viewDermStatus = document.getElementById('view-derm-status');

// Open Add Modal
addDermBtn.addEventListener('click', () => {
  dermModalTitle.textContent = 'Add New Dermatologist';
  dermForm.reset();
  dermIdField.value = '';
  dermProfilePreview.style.display = 'none';

  if (dermPasswordInput) {
    dermPasswordInput.value = '';
    dermPasswordInput.required = true;  // Password required on add
  }

  if (passwordGroup) passwordGroup.style.display = 'block';

  dermStatusInput.value = 'Active';
  dermModal.style.display = 'block';
});

// Close Modals
document.querySelectorAll('.close-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal').style.display = 'none';
  });
});

window.addEventListener('click', e => {
  if (e.target === dermModal) dermModal.style.display = 'none';
  if (e.target === viewDermModal) viewDermModal.style.display = 'none';
});

// Handle Add / Edit Submit
dermForm.addEventListener('submit', e => {
  e.preventDefault();
  const formData = new FormData(dermForm);
  const id = dermIdField.value.trim();
  const url = id ? 'update_dermatologist.php' : 'add_dermatologist.php';

  fetch(url, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert(id ? 'Dermatologist updated successfully!' : 'Dermatologist added successfully!');
        dermModal.style.display = 'none';
        refreshDermList();
      } else {
        alert('Error: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(err => {
      alert('Network or server error.');
      console.error(err);
    });
});

// Load Dermatologists
function refreshDermList() {
  fetch('get_dermatologists.php')
    .then(res => res.text())
    .then(html => {
      const dermList = document.querySelector('.dermatologist-list');
      if (dermList) {
        dermList.innerHTML = html;
        attachButtons();
      }
    })
    .catch(err => console.error('Error loading dermatologist list:', err));
}

// Attach view/edit/delete buttons
function attachButtons() {
  const dermList = document.querySelector('.dermatologist-list');
  if (!dermList) return;

  // View button
  dermList.querySelectorAll('.view-derm-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      fetch(`view_dermatologist.php?id=${encodeURIComponent(id)}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            const d = data.data;
            viewDermName.textContent = `${d.fname} ${d.lname}`;
            viewDermFname.textContent = d.fname;
            viewDermLname.textContent = d.lname;
            viewDermUsername.textContent = d.username;
            viewDermEmail.textContent = d.email;
            viewDermPhone.textContent = d.phone_number;
            viewDermSpecialty.textContent = d.specialty;
            viewDermLocation.textContent = d.location;
            viewDermNextAvailable.textContent = d.next_available;
            viewDermStatus.textContent = d.status || 'Pending';
            viewDermProfile.src = d.profile ? `pic/${d.profile}` : 'pic/default.png';
            viewDermModal.style.display = 'block';
          } else {
            alert('Error: ' + (data.message || 'User not found.'));
          }
        })
        .catch(err => {
          alert('Network or server error.');
          console.error(err);
        });
    });
  });

  // Edit button
  dermList.querySelectorAll('.edit-derm-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      fetch(`view_dermatologist.php?id=${encodeURIComponent(id)}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            const d = data.data;
            dermModalTitle.textContent = 'Edit Dermatologist';
            dermIdField.value = d.user_id;
            dermFnameInput.value = d.fname || '';
            dermLnameInput.value = d.lname || '';
            dermNameInput.value = d.username || '';
            dermEmailInput.value = d.email || '';
            dermPhoneInput.value = d.phone_number || '';
            dermSpecialtyInput.value = d.specialty || '';
            dermLocationInput.value = d.location || '';
            dermNextAvailableInput.value = d.next_available || '';
            dermStatusInput.value = d.status || 'Active';

            if (dermPasswordInput) {
              dermPasswordInput.value = '';
              dermPasswordInput.required = false; // Password NOT required on edit
            }

            if (passwordGroup) passwordGroup.style.display = 'none';

            if (d.profile) {
              dermProfilePreview.src = `pic/${d.profile}`;
              dermProfilePreview.style.display = 'block';
            } else {
              dermProfilePreview.style.display = 'none';
            }

            dermModal.style.display = 'block';
          } else {
            alert('Error: ' + (data.message || 'User not found.'));
          }
        })
        .catch(err => {
          alert('Network or server error.');
          console.error(err);
        });
    });
  });

  // Delete button
  dermList.querySelectorAll('.delete-derm-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      if (confirm('Are you sure you want to delete this dermatologist?')) {
        fetch('delete_dermatologist.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `dermatologist_id=${encodeURIComponent(id)}`
        })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              alert('Dermatologist deleted successfully!');
              refreshDermList();
            } else {
              alert('Error deleting dermatologist: ' + (data.message || 'Unknown error'));
            }
          })
          .catch(err => {
            alert('Network or server error.');
            console.error(err);
          });
      }
    });
  });
}

// Initial Load
refreshDermList();