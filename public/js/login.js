document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const submitBtn = form.querySelector('.submit-btn');
  const themeToggle = document.getElementById('themeToggle');

  const savedTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  themeToggle.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    clearErrors();

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    let isValid = true;

    if (!email) {
      showError('email', 'Email is required');
      isValid = false;
    } else if (!isValidEmail(email)) {
      showError('email', 'Please enter a valid email address');
      isValid = false;
    }

    if (!password) {
      showError('password', 'Password is required');
      isValid = false;
    } else if (password.length < 6) {
      showError('password', 'Password must be at least 6 characters');
      isValid = false;
    }

    if (!isValid) {
      return;
    }

    // Loading animation
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // Kirim ke Laravel pakai Fetch
    try {
      const response = await fetch(form.action, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
          "Accept": "application/json",
        },
        body: JSON.stringify({
          email: email,
          password: password,
          remember: document.getElementById('remember_me').checked,
        }),
      });

      if (response.ok) {
        // Jika sukses, redirect ke dashboard
        window.location.href = "/";
      } else {
        const data = await response.json();
        if (data.errors) {
          if (data.errors.email) showError('email', data.errors.email[0]);
          if (data.errors.password) showError('password', data.errors.password[0]);
        } else if (data.message) {
          alert(data.message);
        }
      }
    } catch (error) {
      alert("Login gagal. Periksa koneksi atau server Anda.");
      console.error(error);
    } finally {
      submitBtn.classList.remove('loading');
      submitBtn.disabled = false;
    }
  });

  emailInput.addEventListener('input', () => {
    clearError('email');
  });

  passwordInput.addEventListener('input', () => {
    clearError('password');
  });

  function showError(field, message) {
    const input = document.getElementById(field);
    const errorElement = document.getElementById(`${field}-error`);

    input.classList.add('error');
    errorElement.textContent = message;
    errorElement.classList.add('visible');
  }

  function clearError(field) {
    const input = document.getElementById(field);
    const errorElement = document.getElementById(`${field}-error`);

    input.classList.remove('error');
    errorElement.textContent = '';
    errorElement.classList.remove('visible');
  }

  function clearErrors() {
    clearError('email');
    clearError('password');
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
});

