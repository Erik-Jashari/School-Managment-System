// Hide page content immediately to prevent flash on protected pages
document.documentElement.style.visibility = 'hidden';

/**
 * Check authentication status and prevent back-button access to protected pages
 * This handles browser back-forward cache by verifying session is still valid
 */
function checkAuthStatus() {
    fetch('/School-Managment-System/includes/checkAuth.php')
        .then(response => response.json())
        .then(data => {
            const currentPath = window.location.pathname;
            const isAdminPage = currentPath.includes('/admin/');
            
            // If on admin page but not logged in or not admin, redirect to login
            if (isAdminPage && (!data.isLoggedIn || data.role !== 'Admin')) {
                window.location.replace('/School-Managment-System/Login.php');
                return;
            }

            // Auth passed, show the page
            document.documentElement.style.visibility = 'visible';
        })
        .catch(() => {
            // On error, redirect to login to be safe
            window.location.replace('/School-Managment-System/Login.php');
        });
}

// Check on page show (handles back button and bfcache)
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.documentElement.style.visibility = 'hidden';
        checkAuthStatus();
    }
});

// Also check on page load
checkAuthStatus();
