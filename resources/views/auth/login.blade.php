<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — POS Konter</title>
    <link rel="stylesheet" href="{{ asset('css/sf-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('login-theme') || 'light');
    </script>
</head>

<body class="login-page">
    <div class="login-bg" aria-hidden="true">
        <span class="login-orb login-orb-a"></span>
        <span class="login-orb login-orb-b"></span>
        <span class="login-orb login-orb-c"></span>
        <span class="login-grid"></span>
    </div>

    <button type="button" class="theme-toggle" id="themeToggle" aria-label="Ganti tema">
        <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="4"></circle>
            <path
                d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41">
            </path>
        </svg>
        <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"></path>
        </svg>
    </button>

    <main class="login-shell">
        <section class="login-brand">
            <div class="login-stage" aria-hidden="true">
                <span class="login-mesh login-mesh-a"></span>
                <span class="login-mesh login-mesh-b"></span>
                <span class="login-ring login-ring-1"></span>
                <span class="login-ring login-ring-2"></span>
                <span class="login-ring login-ring-3"></span>

                <div class="login-hero">
                    <span class="login-hero-glow"></span>
                    <span class="login-hero-mark">POS</span>
                </div>

                <div class="login-tile login-tile-a">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path d="M7 9v6M10 9v6M13 9v3M16 9v6"></path>
                    </svg>
                </div>
                <div class="login-tile login-tile-b">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                        <path d="M8 8h8M8 12h8M8 16h4"></path>
                    </svg>
                </div>
                <div class="login-tile login-tile-c">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="20" r="1.4"></circle>
                        <circle cx="18" cy="20" r="1.4"></circle>
                        <path d="M3 4h2l2.4 11.2A2 2 0 0 0 9.4 17H18a2 2 0 0 0 2-1.5L21 8H6"></path>
                    </svg>
                </div>
            </div>
            <h1>POS Satpam</h1>
        </section>

        <section class="login-panel">
            <div class="login-header">
                <p class="login-kicker">Selamat datang</p>
                <h2>Masuk ke akun</h2>
                <p>Gunakan email dan kata sandi kasir atau admin.</p>
            </div>

            @if (session('status'))
                <div class="login-alert login-alert-ok">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="login-alert login-alert-err" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" placeholder="nama@konter.com"
                        class="{{ $errors->has('email') ? 'error' : '' }}">
                    <span class="error-message {{ $errors->has('email') ? 'visible' : '' }}" id="email-error">
                        {{ $errors->first('email') }}
                    </span>
                </div>

                <div class="form-group">
                    <label for="password">Kata sandi</label>
                    <div class="password-wrapper">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" class="{{ $errors->has('password') ? 'error' : '' }}">
                        <button type="button" class="toggle-password" id="togglePassword"
                            aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path id="eyePath" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <span class="error-message {{ $errors->has('password') ? 'visible' : '' }}" id="password-error">
                        {{ $errors->first('password') }}
                    </span>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    @endif
                </div>

                <button type="submit" class="submit-btn">
                    <span class="btn-text">Masuk</span>
                    <span class="btn-loader" aria-hidden="true"></span>
                </button>
            </form>
        </section>
    </main>

    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
