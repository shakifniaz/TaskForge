<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Register · TaskForge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased overflow-hidden">

{{-- ===== Static Background ===== --}}
<div class="fixed inset-0 -z-10 tf-bg"></div>

{{-- ===== Dark Mode Toggle ===== --}}
<button id="themeToggle"
        class="fixed top-6 right-6 z-20 h-12 w-12 rounded-2xl
               border transition flex items-center justify-center">

    {{-- Moon (light mode) --}}
    <svg id="iconMoon" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
    </svg>

    {{-- Sun (dark mode) --}}
    <svg id="iconSun" class="h-6 w-6 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0-1.414-1.414M7.05 7.05 5.636 5.636"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>
</button>

{{-- ===== Main Layout ===== --}}
<div class="min-h-screen flex items-center justify-center px-8">
    <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

        {{-- ===== LEFT: Branding ===== --}}
        <div class="flex items-center gap-10">
            <div class="tf-logo-glow">
                <img src="{{ asset('assets/logos/TaskForgeGreen.svg') }}"
                     alt="TaskForge"
                     class="h-44 w-44 shrink-0">
            </div>

            <div>
                <h1 class="text-[4.5rem] leading-tight font-extrabold tracking-tight tf-title">
                    TaskForge
                </h1>
                <p class="mt-3 text-xl tf-sub">
                    Forge clarity. Build momentum.
                </p>
            </div>
        </div>

        {{-- ===== RIGHT: Register Card ===== --}}
        <div class="flex justify-center lg:justify-end">
            <div class="w-full max-w-md tf-card">

                <h2 class="text-2xl font-bold tf-h">Create your account</h2>
                <p class="mt-1 text-sm tf-sub">Start building with TaskForge</p>

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    {{-- Name row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-semibold tf-h">First name</label>
                            <input type="text" name="first_name" required class="mt-1 tf-input">
                        </div>
                        <div>
                            <label class="text-sm font-semibold tf-h">Last name</label>
                            <input type="text" name="last_name" required class="mt-1 tf-input">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="text-sm font-semibold tf-h">Email</label>
                        <input type="email" name="email" required class="mt-1 tf-input">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="text-sm font-semibold tf-h">Password</label>
                        <input id="password" type="password" name="password" required class="mt-1 tf-input">
                        <div class="mt-2 h-2 rounded-full bg-black/10 overflow-hidden">
                            <div id="strengthBar" class="h-2 w-0 transition-all"></div>
                        </div>
                        <p id="strengthText" class="mt-1 text-xs tf-sub"></p>
                    </div>

                    {{-- Confirm --}}
                    <div>
                        <label class="text-sm font-semibold tf-h">Confirm password</label>
                        <input type="password" name="password_confirmation" required class="mt-1 tf-input">
                    </div>

                    <button type="submit" class="w-full tf-primary-btn mt-2">
                        Create account
                    </button>
                </form>

                <p class="mt-6 text-sm tf-sub text-center">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold" style="color:#57A773;">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ===== Styles ===== --}}
<style>
.tf-bg {
    background:
        radial-gradient(900px 400px at 10% 20%, rgba(87,167,115,.35), transparent 60%),
        radial-gradient(800px 400px at 90% 25%, rgba(155,209,229,.35), transparent 60%),
        radial-gradient(900px 500px at 50% 120%, rgba(106,142,174,.30), transparent 65%);
}

.tf-logo-glow {
    filter: drop-shadow(0 0 28px rgba(87,167,115,.75));
    animation: logoGlow 3s ease-in-out infinite alternate;
}

@keyframes logoGlow {
    from { filter: drop-shadow(0 0 18px rgba(87,167,115,.45)); }
    to   { filter: drop-shadow(0 0 36px rgba(87,167,115,.9)); }
}

.tf-card {
    background: linear-gradient(135deg, rgba(255,255,255,.9), rgba(255,255,255,.7));
    backdrop-filter: blur(16px);
    border-radius: 1.75rem;
    padding: 2.25rem;
    border: 1px solid rgba(0,0,0,.1);
}

.tf-title { color:#157145; }
.tf-h { color:#0f172a; }
.tf-sub { color:rgba(15,23,42,.72); }

.tf-input {
    width:100%;
    padding:.75rem .9rem;
    border-radius:.75rem;
    border:1px solid rgba(0,0,0,.15);
}

.tf-primary-btn {
    background:#57A773;
    color:#fff;
    padding:.8rem;
    border-radius:.75rem;
    font-weight:600;
}

#strengthBar { background:#dc2626; }

html.dark body { background:#0b0f14; }

html.dark .tf-card {
    background: linear-gradient(135deg, rgba(18,26,34,.95), rgba(18,26,34,.75));
    border-color: rgba(255,255,255,.12);
}

html.dark .tf-h,
html.dark .tf-sub { color:#e9eef5; }

html.dark #themeToggle {
    background:#121a22;
    color:#e9eef5;
}
</style>

{{-- ===== Scripts ===== --}}
<script>
const toggle = document.getElementById('themeToggle');
const sun = document.getElementById('iconSun');
const moon = document.getElementById('iconMoon');

function applyTheme(isDark) {
    document.documentElement.classList.toggle('dark', isDark);
    sun.classList.toggle('hidden', !isDark);
    moon.classList.toggle('hidden', isDark);
    localStorage.setItem('tf_theme', isDark ? 'dark' : 'light');
}
applyTheme(localStorage.getItem('tf_theme') === 'dark');
toggle.addEventListener('click', () => applyTheme(!document.documentElement.classList.contains('dark')));

// Password strength
const pwd = document.getElementById('password');
const bar = document.getElementById('strengthBar');
const text = document.getElementById('strengthText');

pwd.addEventListener('input', () => {
    const v = pwd.value;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const pct = [0,25,50,75,100][score];
    bar.style.width = pct + '%';
    bar.style.background =
        score < 2 ? '#dc2626' :
        score < 3 ? '#f59e0b' :
        score < 4 ? '#84cc16' :
        '#16a34a';

    text.textContent =
        score < 2 ? 'Weak' :
        score < 3 ? 'Fair' :
        score < 4 ? 'Good' :
        'Strong';
});
</script>

</body>
</html>
