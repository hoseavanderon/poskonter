<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        :root {
            --bg: #0b1220;
            --card: #1e293b;
            --text: #e5e7eb;
            /* lebih terang */
            --muted: #94a3b8;
            /* abu secondary */
            --input: #334155;
            --primary: #3b82f6;
        }

        body.light {
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --input: #e2e8f0;
            --primary: #3b82f6;
        }

        body {
            margin: 0;
            font-family: sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* container */
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* card */
        .card {
            width: 900px;
            height: 520px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            background: var(--card);
        }

        /* LEFT */
        .left {
            width: 50%;
            background: linear-gradient(135deg, #0b3c88, #0a1f44);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .left-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .left-content img {
            width: 70%;
        }

        .left-footer {
            text-align: center;
            padding: 20px;
        }

        .left-footer h2 {
            margin: 0;
        }

        .left-footer p {
            opacity: 0.7;
        }

        /* RIGHT */
        .right {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header h2 {
            margin-bottom: 10px;
            font-size: 26px;
            font-weight: 700;
            color: #f8fafc;
            /* terang banget */
        }

        .login-header p {
            color: var(--muted);
            margin: 5px 0;
            font-size: 14px;
        }

        .login-header span {
            font-size: 12px;
            color: #64748b;
        }

        /* FORM */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 14px;
            color: #cbd5f5;
        }

        input {
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: var(--input);
            color: #f1f5f9;
        }

        input::placeholder {
            color: #94a3b8;
        }

        /* password */
        .password-box {
            display: flex;
            align-items: center;
            background: var(--input);
            border-radius: 10px;
        }

        .password-box input {
            flex: 1;
            background: transparent;
            border: none;
        }

        .password-box span {
            padding: 0 10px;
            cursor: pointer;
        }

        /* options */
        .options {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12px;
            color: var(--muted);
        }

        .options a {
            color: #3b82f6;
            text-decoration: none;
        }

        .options a:hover {
            text-decoration: underline;
        }

        /* button */
        button {
            margin-top: 20px;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* toggle */
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
        }

        /* ================= MOBILE ================= */
        @media (max-width: 768px) {

            .container {
                padding: 20px;
                align-items: stretch;
            }

            .card {
                flex-direction: column;
                width: 100%;
                height: auto;
                border-radius: 16px;
            }

            /* ❌ HILANGKAN PANEL KIRI */
            .left {
                display: none;
            }

            /* ✅ FULL WIDTH FORM */
            .right {
                width: 100%;
                padding: 24px;
            }

            /* biar lebih enak di mobile */
            .login-header h2 {
                font-size: 22px;
                text-align: center;
            }

            form {
                margin-top: 10px;
            }

            input {
                padding: 14px;
                font-size: 14px;
            }

            button {
                padding: 14px;
                font-size: 15px;
            }

            .options {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .container {
                height: auto;
                /* ❌ jangan full layar */
                min-height: 100vh;
                padding: 20px;
                align-items: flex-start;
                /* ❗ penting */
            }

            .container {
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }

            .card {
                width: 100%;
                max-width: 420px;
                /* 🔥 tadinya 400 → naik dikit */
                margin: 40px auto;
                /* 🔥 kasih jarak atas bawah */
                border-radius: 20px;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">

            <!-- LEFT -->
            <div class="left">
                <div class="left-content">
                    <img src="{{ asset('img/tri.jpeg') }}" alt="illustration">
                </div>

                <div class="left-footer">
                    <h2>Ciaelah Jaga Sendiri</h2>
                    <p>Semangat Bos Tanggal Tua !!!</p>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="right">
                <div class="login-header">
                    <h2>Login</h2>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email">

                    <label>Password</label>
                    <div class="password-box">
                        <input type="password" name="password" placeholder="Enter your password">
                        <span>👁️</span>
                    </div>

                    <div class="options">
                        <label><input type="checkbox"> Remember me</label>
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit">Login</button>
                </form>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
