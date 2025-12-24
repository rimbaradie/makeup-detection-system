document.addEventListener('DOMContentLoaded', function () {
    loadCampaigns();

    // Open the create campaign modal
    const openModalBtn = document.getElementById('openCreateModal');
    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            document.getElementById('createCampaignModal').style.display = 'block';
        });
    }

    // Close modals on outside click
    window.addEventListener('click', (event) => {
        if (event.target === document.getElementById('createCampaignModal')) closeModal();
        if (event.target === document.getElementById('reportModal')) closeReportModal();
        if (event.target === document.getElementById('editCampaignModal')) closeEditModal();
    });

    // Modal close functions
    window.closeModal = function () {
        document.getElementById('createCampaignModal').style.display = 'none';
    };
    window.closeReportModal = function () {
        document.getElementById('reportModal').style.display = 'none';
    };
    window.closeEditModal = function () {
        document.getElementById('editCampaignModal').style.display = 'none';
    };

    // Handle create campaign form submission
    const createForm = document.getElementById('createCampaignForm');
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('create_campaign.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                closeModal();
                this.reset();
                loadCampaigns();
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Handle edit campaign form submission
    const editForm = document.getElementById('editCampaignForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('update_campaign.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                alert(response);
                closeEditModal();
                loadCampaigns();
            })
            .catch(error => console.error('Error:', error));
        });
    }
});

function loadCampaigns() {
    fetch('fetch_campaigns.php')
        .then(response => response.json())
        .then(data => {
            const campaignList = document.querySelector('.campaign-list');
            campaignList.innerHTML = '';

            data.forEach(campaign => {
                const item = document.createElement('div');
                item.className = 'campaign-item';
                item.dataset.id = campaign.campaign_id;

                item.innerHTML = `
                    <h4>${campaign.title}</h4>
                    <p>Status: ${campaign.status} | Last Sent: ${campaign.last_sent || 'N/A'} | Opens: ${campaign.opens_percentage}%</p>
                    <div class="campaign-actions">
                        <button class="btn btn-sm btn-info tooltip view-report-btn" data-tooltip="View Reports" data-id="${campaign.campaign_id}">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="btn btn-sm btn-warning tooltip edit-btn" data-tooltip="Edit" data-id="${campaign.campaign_id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                    </div>
                `;
                campaignList.appendChild(item);
            });

            // Add listeners to View Reports buttons
            document.querySelectorAll('.view-report-btn').forEach(button => {
                button.addEventListener('click', function () {
                    openReportModal(this.dataset.id);
                });
            });

            // Add listeners to Edit buttons
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const campaignId = this.dataset.id;

                    fetch(`get_campaign_report.php?id=${campaignId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }

                            // Populate form
                            document.getElementById('edit_campaign_id').value = data.campaign_id;
                            document.getElementById('edit_title').value = data.title;
                            document.getElementById('edit_status').value = data.status;
                            document.getElementById('edit_last_sent').value = data.last_sent || '';
                            document.getElementById('edit_opens_percentage').value = data.opens_percentage || 0;
                            document.getElementById('edit_recipient').value = data.recipient || '';

                            document.getElementById('editCampaignModal').style.display = 'block';
                        });
                });
            });
        })
        .catch(error => {
            console.error('Error fetching campaigns:', error);
        });
}

function openReportModal(campaignId) {
    const modal = document.getElementById('reportModal');
    const content = document.getElementById('reportContent');

    content.innerHTML = "<p>Loading report...</p>";
    modal.style.display = 'block';

    fetch(`get_campaign_report.php?id=${campaignId}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `<p style="color:red;"><strong>Error:</strong> ${data.error}</p>`;
                return;
            }

            content.innerHTML = `
                <p><strong>📧 Title:</strong> ${data.title}</p>
                <p><strong>⚙️ Status:</strong> ${data.status}</p>
                <p><strong>📅 Last Sent:</strong> ${data.last_sent || 'N/A'}</p>
                <p><strong>📊 Open Rate:</strong> ${data.opens_percentage}%</p>
                <p><strong>👤 Recipient:</strong> ${data.recipient}</p>
            `;
        })
        .catch(err => {
            content.innerHTML = `<p style="color:red;">❌ Failed to load report: ${err.message}</p>`;
        });
}
