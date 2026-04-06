<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0b1220',
                            card: '#1e293b',
                            input: '#334155',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-dark-bg text-gray-200 m-0 font-sans">

    <div class="min-h-screen flex justify-center items-center p-4">
        <div class="w-full max-w-[900px] flex flex-col md:flex-row rounded-2xl overflow-hidden bg-dark-card">

            <!-- LEFT -->
            <div class="hidden md:flex md:w-1/2 flex-col justify-between"
                style="background: linear-gradient(135deg, #0b3c88, #0a1f44);">
                <div class="flex justify-center items-center flex-1 p-6">
                    <img src="{{ asset('img/tri.jpeg') }}" alt="illustration" class="w-[70%] rounded-lg">
                </div>
                <div class="text-center p-5">
                    <h2 class="text-white text-xl font-bold m-0">Ciaelah Jaga Sendiri</h2>
                    <p class="text-white/70 text-sm mt-1">Semangat Bos Tanggal Tua !!!</p>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="w-full md:w-1/2 p-6 sm:p-10 flex flex-col justify-center">
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-[26px] font-bold text-slate-50 text-center md:text-left">Login</h2>
                </div>

                <form method="POST" action="{{ route('login') }}" class="flex flex-col">
                    @csrf

                    <label class="mt-4 mb-1 text-sm text-blue-100">Email</label>
                    <input type="email" name="email" placeholder="Enter your email"
                        class="p-3 rounded-xl border-none bg-dark-input text-slate-100 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <label class="mt-4 mb-1 text-sm text-blue-100">Password</label>
                    <div class="flex items-center bg-dark-input rounded-xl px-3">
                        <input type="password" name="password" id="passwordInput" placeholder="Enter your password"
                            class="flex-1 py-3 bg-transparent border-none text-slate-100 placeholder-slate-400 text-sm focus:outline-none">

                        <button type="button" onclick="togglePassword()" class="text-slate-400 hover:text-white">
                            <!-- Eye (default) -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- Eye Slash (hidden) -->
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.77 9.77 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a16.96 16.96 0 01-3.13 4.18M6.53 6.53A16.93 16.93 0 002.25 12s3.75 7.5 9.75 7.5c1.57 0 3-.33 4.28-.9" />
                            </svg>
                        </button>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-3 text-xs text-slate-400 gap-2">
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" class="accent-blue-500"> Remember me
                        </label>
                        <a href="#" class="text-blue-500 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit"
                        class="mt-5 p-3 rounded-xl bg-blue-500 hover:bg-blue-600 transition-colors text-white font-bold cursor-pointer border-none text-sm">
                        Login
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
