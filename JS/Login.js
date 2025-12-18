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

// Regex patterns for validation

// Email: Validates format username@domain.extension (letters, numbers, dots, underscores, hyphens allowed)
const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

// Password: Minimum 8 characters with at least one uppercase, one lowercase, one digit, and one special character (@$!%*?&)
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
            
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');
    
    // Validate email format
    if(!emailRegex.test(email)) {
        errorMessage.textContent = 'Please enter a valid email address';
        errorMessage.style.display = 'block';
        return;
    }
    
    // Validate password strength
    if(!passwordRegex.test(password)) {
        errorMessage.textContent = 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character (@$!%*?&)';
        errorMessage.style.display = 'block';
        return;
    }
    
    errorMessage.style.display = 'none';
    
    // Login logic here
    console.log('Login attempt:', { email, password });
});