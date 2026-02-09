// Dark Futuristic Login Terminal - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const messageDiv = document.getElementById('message');

    // Add typing effect to inputs
    let typingTimer;
    [usernameInput, passwordInput].forEach(input => {
        input.addEventListener('keydown', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'inputGlow 0.3s ease';
            }, 10);
        });

        input.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                // Add subtle pulse effect when user stops typing
                this.style.boxShadow = '0 0 15px rgba(0, 212, 255, 0.6), inset 0 0 10px rgba(0, 212, 255, 0.2)';
                setTimeout(() => {
                    this.style.boxShadow = '0 0 15px rgba(0, 212, 255, 0.4), inset 0 0 10px rgba(0, 212, 255, 0.2)';
                }, 200);
            }, 500);
        });
    });

    // Add CSS animation for input glow
    const style = document.createElement('style');
    style.textContent = `
        @keyframes inputGlow {
            0% { transform: translateX(-2px); }
            50% { transform: translateX(2px); }
            100% { transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        // Clear previous messages
        messageDiv.className = 'message';
        messageDiv.textContent = '';

        // Simulate authentication attempt
        showMessage('AUTHENTICATING...', 'info');

        // Add loading animation to button
        const submitButton = loginForm.querySelector('.cyber-button.primary');
        const originalText = submitButton.querySelector('.button-text').textContent;
        submitButton.querySelector('.button-text').textContent = 'PROCESSING...';
        submitButton.disabled = true;

        // Simulate network delay
        setTimeout(() => {
            // Note: HTTP Basic Auth is handled by Apache, this is just for UX feedback
            if (username && password) {
                // In reality, Apache will handle the authentication
                // This is just client-side validation and feedback
                showMessage(`ACCESS GRANTED > CREDENTIALS: ${username}`, 'success');

                // Add success effect
                document.querySelector('.terminal-window').style.animation = 'successFlash 0.5s';

                setTimeout(() => {
                    showMessage('SYSTEM ACCESS INITIALIZED...', 'success');
                }, 1500);
            } else {
                showMessage('ERROR > INVALID INPUT', 'error');
                // Add error shake effect
                loginForm.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    loginForm.style.animation = '';
                }, 500);
            }

            // Restore button
            submitButton.querySelector('.button-text').textContent = originalText;
            submitButton.disabled = false;
        }, 1500);
    });

    // Message display function
    function showMessage(text, type) {
        messageDiv.textContent = text;
        messageDiv.className = 'message ' + type;

        // Add typewriter effect
        const originalText = text;
        messageDiv.textContent = '';
        let i = 0;
        const typeInterval = setInterval(() => {
            if (i < originalText.length) {
                messageDiv.textContent += originalText.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
            }
        }, 30);
    }

    // Add shake animation
    const shakeStyle = document.createElement('style');
    shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes successFlash {
            0%, 100% { border-color: var(--primary-color); }
            50% { border-color: var(--secondary-color); box-shadow: 0 0 40px rgba(0, 212, 255, 0.8); }
        }
    `;
    document.head.appendChild(shakeStyle);

    // Matrix rain effect (optional easter egg)
    let matrixEnabled = false;
    document.addEventListener('keydown', function(e) {
        // Konami code-like sequence: press 'M' key 3 times quickly
        if (e.key === 'm' || e.key === 'M') {
            matrixEnabled = true;
            createMatrixRain();
        }
    });

    function createMatrixRain() {
        const chars = '01アイウエオカキクケコサシスセソタチツテト';
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '9999';
        canvas.style.opacity = '0.1';

        document.body.appendChild(canvas);

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const columns = Math.floor(canvas.width / 20);
        const drops = Array(columns).fill(1);

        function draw() {
            ctx.fillStyle = 'rgba(10, 14, 39, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = '#00ff9f';
            ctx.font = '15px monospace';

            for (let i = 0; i < drops.length; i++) {
                const char = chars[Math.floor(Math.random() * chars.length)];
                ctx.fillText(char, i * 20, drops[i] * 20);

                if (drops[i] * 20 > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        const matrixInterval = setInterval(draw, 50);

        // Remove after 5 seconds
        setTimeout(() => {
            clearInterval(matrixInterval);
            canvas.remove();
            matrixEnabled = false;
        }, 5000);
    }

    // Dynamic clock in status bar (optional enhancement)
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        // You can add a clock element if desired
        // For now, this is just a placeholder for future enhancement
    }

    setInterval(updateClock, 1000);

    // Add glitch effect on logo occasionally
    const logo = document.querySelector('.ascii-art');
    setInterval(() => {
        if (Math.random() > 0.95) {
            logo.style.transform = 'skew(2deg)';
            logo.style.textShadow = '2px 0 var(--danger-color), -2px 0 var(--secondary-color)';
            setTimeout(() => {
                logo.style.transform = '';
                logo.style.textShadow = '0 0 10px var(--shadow-glow)';
            }, 100);
        }
    }, 3000);

    // Console easter egg
    console.log('%c███╗   ██╗███████╗██╗  ██╗██╗   ██╗███████╗', 'color: #00ff9f; font-weight: bold;');
    console.log('%cNEXUS Security Lab - Workshop Environment', 'color: #00d4ff; font-size: 14px;');
    console.log('%c⚠️  This is an intentionally vulnerable system', 'color: #ff0055; font-size: 12px;');
    console.log('%cDefault credentials: admin / 12345', 'color: #ffa500;');
    console.log('%c\nHint: Try scanning this system with nmap!', 'color: #8892b0;');
    console.log('%cnmap -sV -sC 192.168.56.10', 'color: #00ff9f; background: #0a0e27; padding: 5px; border: 1px solid #00ff9f;');
});
