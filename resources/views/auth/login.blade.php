<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Login · TaskForge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased overflow-hidden">

{{-- ===== Static Background Layers ===== --}}
<div class="fixed inset-0 -z-10 tf-bg-layer layer-1"></div>
<div class="fixed inset-0 -z-10 tf-bg-layer layer-2"></div>
<div class="fixed inset-0 -z-10 tf-bg-layer layer-3"></div>

{{-- ===== Dark mode toggle ===== --}}
<button id="themeToggle"
        class="fixed top-6 right-6 z-20 h-12 w-12 rounded-2xl
               border transition
               flex items-center justify-center">

    {{-- Moon = light mode --}}
    <svg id="iconMoon" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
    </svg>

    {{-- Sun = dark mode --}}
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

            {{-- Glowing Logo --}}
            <div class="tf-logo-glow">
                <img src="{{ asset('assets/logos/TaskForgeGreen.svg') }}"
                     alt="TaskForge"
                     class="h-44 w-44 shrink-0">
            </div>

            {{-- Text --}}
            <div>
                <h1 class="text-[4.5rem] leading-tight font-extrabold tracking-tight tf-title">
                    TaskForge
                </h1>
                <p class="mt-3 text-xl tf-sub">
                    Forge clarity. Build momentum.
                </p>
            </div>
        </div>

        {{-- ===== RIGHT: Login Card ===== --}}
        <div class="flex justify-center lg:justify-end">
            <div class="w-full max-w-md tf-card tf-hovercard">

                <h2 class="text-2xl font-bold tf-h">
                    Welcome back
                </h2>
                <p class="mt-1 text-sm tf-sub">
                    Sign in to continue to TaskForge
                </p>

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-semibold tf-h">Email</label>
                        <input type="email" name="email" required autofocus class="mt-1 tf-input">
                    </div>

                    <div>
                        <label class="text-sm font-semibold tf-h">Password</label>
                        <input type="password" name="password" required class="mt-1 tf-input">
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 tf-sub">
                            <input type="checkbox" name="remember" class="rounded">
                            Remember me
                        </label>

                        <a href="{{ route('password.request') }}" class="tf-link">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="w-full tf-primary-btn mt-2">
                        Sign in
                    </button>
                </form>

                <p class="mt-6 text-sm tf-sub text-center">
                    Don’t have an account?
                    <a href="{{ route('register') }}"
                       class="font-semibold"
                       style="color:#57A773">
                        Create one
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ===== Styles ===== --}}
<style>
/* ===== Static Background Layers ===== */
.tf-bg-layer {
    background-repeat: no-repeat;
    background-size: cover;
}

.layer-1 {
    background:
        radial-gradient(900px 400px at 10% 20%, rgba(87,167,115,.35), transparent 60%);
}

.layer-2 {
    background:
        radial-gradient(800px 400px at 90% 25%, rgba(155,209,229,.35), transparent 60%);
}

.layer-3 {
    background:
        radial-gradient(900px 500px at 50% 120%, rgba(106,142,174,.30), transparent 65%);
}

/* ===== Logo Glow ===== */
.tf-logo-glow {
    filter: drop-shadow(0 0 20px rgba(87,167,115,.6));
    animation: logoGlow 3s ease-in-out infinite alternate;
}

@keyframes logoGlow {
    from { filter: drop-shadow(0 0 18px rgba(87,167,115,.45)); }
    to   { filter: drop-shadow(0 0 32px rgba(87,167,115,.85)); }
}

/* ===== Card ===== */
.tf-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
    backdrop-filter: blur(16px);
    border-radius: 1.75rem;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 2.25rem;
}


/* ===== Typography ===== */
.tf-title { color:#157145; }
.tf-h { color:#0f172a; }
.tf-sub { color:rgba(15,23,42,.72); }
.tf-link { color:#1d4ed8; }

/* ===== Inputs ===== */
.tf-input {
    width:100%;
    padding:.75rem .9rem;
    border-radius:.75rem;
    border:1px solid rgba(0,0,0,.15);
    background:#fff;
}

/* ===== Buttons ===== */
.tf-primary-btn {
    padding:.8rem 1.25rem;
    border-radius:.75rem;
    background:#57A773;
    color:#fff;
    font-weight:600;
    transition:.2s;
}
.tf-primary-btn:hover {
    background:#4d9a69;
    transform:translateY(-1px);
}

/* ===== Toggle Button ===== */
#themeToggle {
    background: rgba(255,255,255,.85);
    border-color: rgba(0,0,0,.15);
    color: #0f172a;
}

/* ===== DARK MODE ===== */
html.dark body { background:#0b0f14; }

html.dark .tf-card {
    background: linear-gradient(135deg, rgba(18,26,34,0.95), rgba(18,26,34,0.75));
    border-color: rgba(255,255,255,0.12);
}

html.dark .tf-h,
html.dark .tf-sub,
html.dark .tf-link {
    color:#e9eef5;
}

html.dark #themeToggle {
    background:#121a22;
    border-color: rgba(255,255,255,.15);
    color:#e9eef5;
}
</style>

{{-- ===== Dark mode logic ===== --}}
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

toggle.addEventListener('click', () => {
    applyTheme(!document.documentElement.classList.contains('dark'));
});
</script>

</body>
</html>
