<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Forgot Password · TaskForge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased overflow-hidden">

{{-- ===== Static Gradient Background ===== --}}
<div class="fixed inset-0 -z-10 tf-bg"></div>

{{-- ===== Dark mode toggle ===== --}}
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
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

        {{-- ===== LEFT: Branding ===== --}}
        <div class="flex items-center gap-10">
            <div class="tf-logo-glow">
                <img src="{{ asset('assets/logos/TaskForgeGreen.svg') }}"
                     alt="TaskForge"
                     class="h-40 w-40 shrink-0">
            </div>

            <div>
                <h1 class="text-[4.2rem] leading-tight font-extrabold tracking-tight tf-title">
                    TaskForge
                </h1>
                <p class="mt-3 text-xl tf-sub">
                    Forge clarity. Build momentum.
                </p>
            </div>
        </div>

        {{-- ===== RIGHT: Forgot Password Card ===== --}}
        <div class="flex justify-center lg:justify-end">
            <div class="w-full max-w-md tf-card tf-hovercard">

                <h2 class="text-2xl font-bold tf-h">
                    Forgot your password?
                </h2>
                <p class="mt-1 text-sm tf-sub">
                    Enter your email and we’ll send you a reset link.
                </p>

                @if (session('status'))
                    <div class="mt-4 text-sm text-green-600 font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-semibold tf-h">Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               class="mt-1 tf-input">
                        @error('email')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="w-full tf-primary-btn mt-2">
                        Send reset link
                    </button>
                </form>

                <p class="mt-6 text-sm tf-sub text-center">
                    Remembered your password?
                    <a href="{{ route('login') }}"
                       class="font-semibold"
                       style="color:#57A773;">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ===== Styles ===== --}}
<style>
/* ===== Background ===== */
.tf-bg {
    background:
        radial-gradient(900px 400px at 10% 20%, rgba(87,167,115,.35), transparent 60%),
        radial-gradient(800px 400px at 90% 25%, rgba(155,209,229,.35), transparent 60%),
        radial-gradient(900px 500px at 50% 120%, rgba(106,142,174,.30), transparent 65%);
}

/* ===== Logo Glow ===== */
.tf-logo-glow {
    filter: drop-shadow(0 0 22px rgba(87,167,115,.6));
    animation: glow 3s ease-in-out infinite alternate;
}

@keyframes glow {
    from { filter: drop-shadow(0 0 18px rgba(87,167,115,.45)); }
    to   { filter: drop-shadow(0 0 34px rgba(87,167,115,.9)); }
}

/* ===== Card ===== */
.tf-card {
    background: linear-gradient(135deg,
        rgba(255,255,255,0.9),
        rgba(255,255,255,0.7));
    backdrop-filter: blur(18px);
    border-radius: 1.75rem;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 2.25rem;
}


/* ===== Typography ===== */
.tf-title { color:#157145; }
.tf-h { color:#0f172a; }
.tf-sub { color:rgba(15,23,42,.72); }

/* ===== Inputs ===== */
.tf-input {
    width:100%;
    padding:.75rem .9rem;
    border-radius:.75rem;
    border:1px solid rgba(0,0,0,.15);
    background:#fff;
}

/* ===== Button ===== */
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

/* ===== Toggle ===== */
#themeToggle {
    background: rgba(255,255,255,.85);
    border-color: rgba(0,0,0,.15);
    color:#0f172a;
}

/* ===== Dark Mode ===== */
html.dark body { background:#0b0f14; }

html.dark .tf-card {
    background: linear-gradient(135deg,
        rgba(18,26,34,0.95),
        rgba(18,26,34,0.75));
    border-color: rgba(255,255,255,0.12);
}

html.dark .tf-h,
html.dark .tf-sub {
    color:#e9eef5;
}

html.dark #themeToggle {
    background:#121a22;
    border-color: rgba(255,255,255,.15);
    color:#e9eef5;
}
</style>

{{-- ===== Theme Logic ===== --}}
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
