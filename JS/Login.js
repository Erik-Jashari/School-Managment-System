function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.toggle-password');
    if(passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Hide';
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Show';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    const success = params.get('success');

    if (error) {
        errorMessage.textContent = decodeURIComponent(error);
        errorMessage.style.display = 'block';
    } else if (success) {
        successMessage.textContent = decodeURIComponent(success);
        successMessage.style.display = 'block';
    }
});

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');

    errorMessage.style.display = 'none';
    successMessage.style.display = 'none';

    e.target.submit();
});