
document.addEventListener('DOMContentLoaded', () => {
    // Load settings
    fetch('get_settings.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const settings = data.settings;
                document.getElementById('site-name').value = settings.site_name;
                document.getElementById('contact-email').value = settings.contact_email;
                document.getElementById('currency').value = settings.currency;
            }
        });

    // Save settings
        document.querySelector('#save-settings-btn').addEventListener('click', () => {
        const siteName = document.getElementById('site-name').value;
        const contactEmail = document.getElementById('contact-email').value;
        const currency = document.getElementById('currency').value;

        fetch('update_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                site_name: siteName,
                contact_email: contactEmail,
                currency: currency
            })
        })
        .then(res => res.json())
        .then(response => {
            alert(response.message);
        });
    });
     const tabButtons = document.querySelectorAll(".tab-button");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Remove 'active' from all buttons and content
            tabButtons.forEach((btn) => btn.classList.remove("active"));
            tabContents.forEach((content) => content.classList.remove("active"));

            // Add 'active' to clicked button and its corresponding content
            this.classList.add("active");
            const tabId = this.getAttribute("data-tab");
            document.getElementById(tabId).classList.add("active");
        });
    });
});

document.querySelector('#security-settings .btn-primary').addEventListener('click', function () {
    const twoFactor = document.getElementById('two-factor').checked;
    const strictPassword = document.getElementById('strict-password').checked;
    const sessionTimeout = document.getElementById('session-timeout').value;

    fetch('save_security_settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            two_factor: twoFactor,
            strict_password: strictPassword,
            session_timeout: sessionTimeout
        })
    })
    .then(res => res.json())
    .then(response => {
        alert(response.message);
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Failed to save settings.');
    });
});
