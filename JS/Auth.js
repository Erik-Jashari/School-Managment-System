// Hide page content immediately to prevent flash on protected pages
document.documentElement.style.visibility = 'hidden';

/**
 * Check authentication status and prevent back-button access to protected pages
 * This handles browser back-forward cache by verifying session is still valid
 */
function checkAuthStatus() {
    // Include credentials to ensure session cookies are sent
    return fetch('/School-Managment-System/includes/checkAuth.php', {
        method: 'GET',
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            const currentPath = window.location.pathname;
            const isAdminPage = currentPath.includes('/admin/');
            
            // If on admin page but not logged in or not admin, redirect to login
            if (isAdminPage && (!data.isLoggedIn || data.role !== 'Admin')) {
                window.location.replace('/School-Managment-System/Login.php');
                return false;
            }

            // Auth passed, show the page
            document.documentElement.style.visibility = 'visible';
            return true;
        })
        .catch((error) => {
            // On error, redirect to login to be safe
            console.error('Auth check failed:', error);
            window.location.replace('/School-Managment-System/Login.php');
            return false;
        });
}

// Keep session alive by periodically refreshing auth status
// This prevents session timeout during active use
setInterval(() => {
    if (document.documentElement.style.visibility === 'visible') {
        fetch('/School-Managment-System/includes/checkAuth.php', {
            method: 'GET',
            credentials: 'include'
        }).catch(() => {
            // Session may have expired, redirect on next interaction
            window.location.replace('/School-Managment-System/Login.php');
        });
    }
}, 60000); // Check every 60 seconds

// Check on page show (handles back button and bfcache)
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.documentElement.style.visibility = 'hidden';
        checkAuthStatus();
    }
});

// Also check on page load
checkAuthStatus();
