/**
 * Faky Faky Website - Social Engineering Lab
 * JavaScript for Login Form Interaction
 *
 * Educational Purpose: Demonstrates client-side form validation
 * and basic authentication UI patterns
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');

    // Add input event listeners for real-time validation feedback
    usernameInput.addEventListener('input', function() {
        clearMessage();
        if (this.value.length > 0) {
            this.style.borderColor = '#4CAF50';
        } else {
            this.style.borderColor = '#ddd';
        }
    });

    passwordInput.addEventListener('input', function() {
        clearMessage();
        if (this.value.length > 0) {
            this.style.borderColor = '#4CAF50';
        } else {
            this.style.borderColor = '#ddd';
        }
    });

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent actual form submission

        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        const remember = rememberCheckbox.checked;

        // Clear previous messages
        clearMessage();

        // Validate inputs
        if (!username) {
            showMessage('Please enter a username', 'error');
            usernameInput.focus();
            return;
        }

        if (!password) {
            showMessage('Please enter a password', 'error');
            passwordInput.focus();
            return;
        }

        // Simulate authentication (Educational demonstration)
        authenticateUser(username, password, remember);
    });

    // Handle forgot password link
    const forgotPasswordLink = document.querySelector('.forgot-password');
    forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        showMessage('Password recovery feature - In a real system, this would send a reset email.', 'success');
    });

    // Handle register link
    const registerLink = document.querySelector('.register');
    registerLink.addEventListener('click', function(e) {
        e.preventDefault();
        showMessage('Registration feature - In a real system, this would open a registration form.', 'success');
    });

    /**
     * Simulate user authentication
     * NOTE: This is for educational purposes only!
     * Real authentication should NEVER be done on the client side
     */
    function authenticateUser(username, password, remember) {
        // Show loading state
        showMessage('Authenticating...', 'success');

        // Simulate server delay
        setTimeout(function() {
            // Educational demonstration: Show different messages based on input
            if (username.toLowerCase() === 'admin' && password === 'password') {
                showMessage(
                    'SUCCESS! But this demonstrates a critical vulnerability: ' +
                    'weak credentials. Never use default credentials in production!',
                    'error'
                );
            } else if (username.toLowerCase() === 'demo' && password === 'demo123') {
                showMessage(
                    'Login successful! Welcome to the Social Engineering Lab. ' +
                    'Remember: This is a training environment.',
                    'success'
                );

                if (remember) {
                    console.log('Remember me enabled - In production, this would set a secure cookie');
                }

                // In a real application, redirect to dashboard
                setTimeout(function() {
                    showMessage('In a real system, you would now be redirected to the dashboard...', 'success');
                }, 2000);

            } else {
                showMessage(
                    'Authentication failed. For demo purposes, try: username="demo", password="demo123"',
                    'error'
                );
            }
        }, 1500);
    }

    /**
     * Display a message to the user
     */
    function showMessage(text, type) {
        messageDiv.textContent = text;
        messageDiv.className = 'message ' + type;
        messageDiv.style.display = 'block';

        // Scroll to message
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Clear displayed message
     */
    function clearMessage() {
        messageDiv.textContent = '';
        messageDiv.className = 'message';
        messageDiv.style.display = 'none';
    }

    // Educational console message
    console.log('='.repeat(60));
    console.log('Faky Faky Website - Social Engineering Lab');
    console.log('='.repeat(60));
    console.log('This is an educational environment for learning about');
    console.log('social engineering and web security.');
    console.log('');
    console.log('Demo Credentials:');
    console.log('  Username: demo');
    console.log('  Password: demo123');
    console.log('');
    console.log('IMPORTANT: Never implement authentication like this in');
    console.log('production. Real authentication requires:');
    console.log('  - Server-side validation');
    console.log('  - Encrypted connections (HTTPS)');
    console.log('  - Password hashing');
    console.log('  - Protection against common attacks (CSRF, XSS, etc.)');
    console.log('='.repeat(60));
});

/**
 * Additional helper function to demonstrate security awareness
 */
function checkPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    if (strength < 3) return 'Weak';
    if (strength < 5) return 'Medium';
    return 'Strong';
}
