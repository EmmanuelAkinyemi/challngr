document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const alertContainer = document.getElementById('alertContainer');
    let lastResponse = null;

    // Enhanced alert system with types and auto-dismiss
    function showAlert(message, type = 'error', duration = 3000) {
        // Clear existing alerts
        alertContainer.innerHTML = '';

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} show`;

        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;

        const closeBtn = document.createElement('button');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', () => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        });

        alert.append(messageSpan, closeBtn);
        alertContainer.appendChild(alert);

        // Auto-dismiss after duration
        if (duration > 0) {
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            }, duration);
        }

        return alert;
    }

    // Enhanced fetch wrapper with timeout
    async function fetchWithTimeout(resource, options = {}, timeout = 8000) {
        const controller = new AbortController();
        const id = setTimeout(() => controller.abort(), timeout);

        try {
            const response = await fetch(resource, {
                ...options,
                signal: controller.signal
            });
            clearTimeout(id);
            return response;
        } catch (error) {
            clearTimeout(id);
            throw error;
        }
    }

    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // UI Loading state
        loginBtn.disabled = true;
        btnText.textContent = 'Authenticating...';
        btnSpinner.style.display = 'inline-block';

        try {
            const formData = new FormData(loginForm);
            lastResponse = null;

            // Debug log
            console.debug('Login attempt:', Object.fromEntries(formData));

            const response = await fetchWithTimeout(
                './controller/login.php',
                {
                    method: 'POST',
                    body: new URLSearchParams(formData),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                10000 // 10 second timeout
            );

            // Store raw response for debugging
            const responseText = await response.text();
            lastResponse = responseText;

            // Parse response
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', e, '\nResponse:', responseText);
                throw new Error('Invalid server response format');
            }

            // Handle successful response
            if (response.ok) {
                if (result.success) {
                    showAlert('✓ Login successful', 'success', 1500);

                    // Store auth token if provided
                    if (result.token) {
                        localStorage.setItem('authToken', result.token);
                    }

                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = result.redirect || '/dashboard.html';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Authentication failed');
                }
            } else {
                throw new Error(result.message || `Server error (${response.status})`);
            }
        } catch (error) {
            console.error('Login error:', error);

            let userMessage = 'An unexpected error occurred';
            if (error.name === 'AbortError') {
                userMessage = 'Request timeout - server is not responding';
            } else if (error.message.includes('Failed to fetch')) {
                userMessage = 'Network error - check your connection';
            } else if (error.message.includes('Invalid server response')) {
                userMessage = 'Server configuration error';
                console.error('Raw server response:', lastResponse);
            }

            showAlert(`⚠ ${userMessage}`, 'error');
        } finally {
            // Reset UI state
            loginBtn.disabled = false;
            btnText.textContent = 'Login';
            btnSpinner.style.display = 'none';
        }
    });

    // Debug tools
    window.debugLogin = {
        getLastResponse: () => lastResponse,
        testEndpoint: async () => {
            try {
                const response = await fetch('./controller/login.php');
                return {
                    status: response.status,
                    text: await response.text()
                };
            } catch (error) {
                return { error: error.message };
            }
        },
        simulateLogin: async (email, password) => {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            try {
                const response = await fetch('./controller/login.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData),
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                return await response.json();
            } catch (error) {
                return { error: error.message };
            }
        }
    };

    // Auto-focus username field
    const emailField = loginForm.querySelector('[name="email"]');
    if (emailField) emailField.focus();
});