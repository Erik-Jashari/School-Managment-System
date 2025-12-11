const AppHeader = `
    <nav class="nav-bar">
        <div class="logo-box">SM</div>
        <div class="page-links">
            <a href="Index.html" class="links">Home</a>
            <a href="About.html" class="links">About</a>
            <a href="Contact.html" class="links">Contact</a>
        </div>
        <div class="auth-links">
            <a href="Dashboard.html" class="links">Dashboard</a>
            <a href="Teacher-Profile.html" class="links">Profile</a>
            <a href="Login.html"><button class="login-button">Login</button></a>
        </div>
    </nav>
`;

const AppFooter = `
    <footer>
        <p>© 2025 TeachShare • All rights reserved</p>
        <a class="admin-link" href="admin-login.html">Login as admin</a>
    </footer>
`;


function loadSharedComponents() {

    const headerContainer = document.getElementById('app-header');
    if (headerContainer) {
        headerContainer.innerHTML = AppHeader;
        highlightActiveLink(); // Run helper to bold the current page
    }

    const footerContainer = document.getElementById('app-footer');
    if (footerContainer) {
        footerContainer.innerHTML = AppFooter;
    }
}

function highlightActiveLink() {
    const currentPage = window.location.pathname.split("/").pop();
    const links = document.querySelectorAll('.nav-bar a');

    links.forEach(link => {
        // Check if the link's href matches the current page
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', loadSharedComponents);