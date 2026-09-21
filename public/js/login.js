document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const themeToggle = document.getElementById('themeToggle');
  const submitBtn = form ? form.querySelector('.submit-btn') : null;
  const eyePath = document.getElementById('eyePath');

  const savedTheme = localStorage.getItem('login-theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('login-theme', newTheme);
    });
  }

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', () => {
      const show = passwordInput.type === 'password';
      passwordInput.type = show ? 'text' : 'password';
      togglePassword.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');

      if (eyePath) {
        eyePath.setAttribute(
          'd',
          show
            ? 'M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.77 21.77 0 0 1 5.06-6.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a21.66 21.66 0 0 1-2.16 3.19M1 1l22 22'
            : 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'
        );
      }
    });
  }

  if (form && submitBtn) {
    form.addEventListener('submit', () => {
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    });
  }
});
