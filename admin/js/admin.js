function showSection(section) {
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(section)) {
                    link.classList.add('active');
                }
            });
            
            // Placeholder for section switching
            alert('Navigating to ' + section.charAt(0).toUpperCase() + section.slice(1) + ' section.\n\nThis would load the respective management interface.');
        }
        
       
        function viewPayment(id) {
            alert('Payment Details for Transaction #' + id + '\n\nThis would open a detailed payment record modal.');
        }
        
       
        function notifyTenant(id) {
            const confirmed = confirm('Send overdue payment notification to tenant?\n\nNotification will be sent via SMS/Email/WhatsApp.');
            if (confirmed) {
                alert('Notification sent successfully!\n\nTenant has been notified about the overdue payment.');
            }
        }
        
        
        function logout() {
            const confirmed = confirm('Are you sure you want to logout?');
            if (confirmed) {
                alert('Logging out...\n\nYou will be redirected to the login page.');
                window.location.href = 'index.html';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Boarding House Management System Initialized');
            setInterval(function() {
                console.log('Checking for new updates...');
            }, 30000); 
        });