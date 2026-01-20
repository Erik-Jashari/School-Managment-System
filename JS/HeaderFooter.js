const AppHeader = `
    <nav class="nav-bar">
        <a href="Index.html"><div class="logo-box">SM</div></a>
        <button id="hamburgerBtn" class="hamburger" aria-label="Toggle navigation" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="page-links">
            <a href="/School-Managment-System/Index.html" class="links">Home</a>
            <a href="/School-Managment-System/About.html" class="links">About</a>
            <a href="/School-Managment-System/Contact.html" class="links">Contact</a>
            <a href="/School-Managment-System/Dashboard.html" class="links">Dashboard</a>
        </div>
        <div class="auth-links">
            <a href="/School-Managment-System/Teacher-Profile.html" class="links">Profile</a>
            <a href="/School-Managment-System/Login.html"><button class="login-button">Login</button></a>
        </div>
    </nav>
`;

const AppFooter = `
    <footer>
        <p>© 2025 School Management • All rights reserved</p>
        <a class="admin-link" href="/School-Managment-System/admin-login.html">Login as admin</a>
    </footer>
`;


function loadSharedComponents() {

    const headerContainer = document.getElementById('app-header');
    if (headerContainer) {
        headerContainer.innerHTML = AppHeader;
        highlightActiveLink(); // Run helper to bold the current page
        setupMobileMenu(); // initialize hamburger behaviour
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

function setupMobileMenu() {
    const nav = document.querySelector('.nav-bar');
    const btn = document.getElementById('hamburgerBtn');
    if (!nav || !btn) return;

    btn.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('mobile-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // Close menu when a navigation link is clicked
    nav.addEventListener('click', (e) => {
        const target = e.target;
        if (target.tagName === 'A' && nav.classList.contains('mobile-open')) {
            nav.classList.remove('mobile-open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && nav.classList.contains('mobile-open')) {
            nav.classList.remove('mobile-open');
            btn.setAttribute('aria-expanded', 'false');
            btn.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', loadSharedComponents);