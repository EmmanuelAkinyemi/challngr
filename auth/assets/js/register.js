
document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const errorElement = document.getElementById('error');

    // Show loading state
    submitBtn.disabled = true;
    btnText.textContent = 'Processing...';
    btnSpinner.style.display = 'inline-block';
    errorElement.textContent = '';

    try {
        const formData = new FormData(this);

        // Client-side validation
        if (formData.get('password') !== formData.get('confirm_password')) {
            throw new Error('Passwords do not match');
        }

        const response = await fetch('./controller/register.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();

        if (result.success) {
            errorElement.style.color = 'var(--success-green)';
            errorElement.textContent = 'Registration successful! Redirecting...';
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 1500);
        } else {
            throw new Error(result.message || 'Registration failed');
        }
    } catch (error) {
        console.error('Registration error:', error);
        errorElement.textContent = error.message;
    } finally {
        submitBtn.disabled = false;
        btnText.textContent = 'Register';
        btnSpinner.style.display = 'none';
    }
});