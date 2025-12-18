function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleBtn = passwordInput.nextElementSibling;
    if (passwordInput.type === 'password') {
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
// Full Name: Allows letters, spaces, hyphens, and apostrophes (e.g., "John Smith", "O'Brien", "Mary-Jane")
const fullNameRegex = /^[a-zA-Z]+([ '-][a-zA-Z]+)*$/;
// Password: Minimum 8 characters with at least one uppercase, one lowercase, one digit, and one special character (@$!%*?&)
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

document.getElementById('register-form').addEventListener('submit', function(e) {
e.preventDefault();
const fullname = document.getElementById('fullname').value.trim();
const email = document.getElementById('email').value.trim();
const password = document.getElementById('password').value;
const confirmPassword = document.getElementById('confirm-password').value;
const errorMessage = document.getElementById('error-message');
    
    // Full name validation
    if(!fullNameRegex.test(fullname)) {
        errorMessage.textContent = 'Please enter a valid full name (letters, spaces, hyphens, and apostrophes only)';
        errorMessage.style.display = 'block';
        return;
    }
    
    // Email format validation
    if(!emailRegex.test(email)) {
        errorMessage.textContent = 'Please enter a valid email address';
        errorMessage.style.display = 'block';
        return;
    }
    
    // Validation of password strength
    if(!passwordRegex.test(password)) {
        errorMessage.textContent = 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character (@$!%*?&)';
        errorMessage.style.display = 'block';
        return;
    }
    // Confirm password match
    if(password !== confirmPassword) {
        errorMessage.textContent = 'Passwords do not match!';
        errorMessage.style.display = 'block';
        return;
    }
            
    errorMessage.style.display = 'none';
            
    // Registration logic here
    console.log('Registration attempt:', { fullname, email, password });
});