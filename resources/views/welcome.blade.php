<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Welcome · TaskForge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased overflow-x-hidden bg-white dark:bg-[#0b0f14] transition-colors">

<!-- ===== Gradient Background ===== -->
<div class="fixed inset-0 -z-10 tf-bg"></div>

<!-- ===== Top Right Actions ===== -->
<div class="fixed top-6 right-6 z-20 flex items-center gap-3">
    <a href="{{ route('login') }}" class="tf-top-btn">Sign In</a>
    <a href="{{ route('register') }}" class="tf-top-btn-outline">Register</a>

    <button id="themeToggle" class="tf-toggle-btn">
        <svg id="iconMoon" class="tf-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
        </svg>

        <svg id="iconSun" class="tf-toggle-icon hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0-1.414-1.414M7.05 7.05 5.636 5.636"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
        </svg>
    </button>
</div>

<!-- ===== Hero Section ===== -->
<section class="min-h-screen flex flex-col items-center justify-center text-center px-6">
    <div class="tf-logo-glow">
        <img src="{{ asset('assets/logos/TaskForgeGreen.svg') }}" class="h-56 w-56 mx-auto" />
    </div>

    <h1 class="mt-8 text-[5rem] leading-tight font-extrabold tf-title">
        TaskForge
    </h1>

    <p class="mt-4 text-2xl tf-sub max-w-2xl">
        Forge clarity. Build momentum.
    </p>

    <a href="{{ route('register') }}" class="mt-10 tf-primary-btn-lg">
        Get Started
    </a>
</section>

<!-- ===== Supporting Text ===== -->
<section class="max-w-5xl mx-auto px-6 pb-20 text-center space-y-6">
    <div class="tf-support-row">
        <svg class="tf-support-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Designed for students, teams, and professionals
    </div>

    <div class="flex items-center justify-center gap-3 text-lg tf-sub">
        <svg class="tf-support-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Stay on track with deadlines and real-time progress</span>
    </div>
</section>

<!-- ===== Feature Cards ===== -->
<section class="max-w-7xl mx-auto px-6 pb-32 space-y-20">

    <!-- Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="tf-feature-card">
            
            <h3 class="tf-feature-title">Smart Tasks</h3>
            <p class="tf-feature-text">
                Assign tasks, set priorities, due dates, and track ownership clearly.
            </p>
        </div>

        <div class="tf-feature-card">
            
            <h3 class="tf-feature-title">Visual Boards</h3>
            <p class="tf-feature-text">
                Kanban-style boards show progress at a glance.
            </p>
        </div>

        <div class="tf-feature-card">
            
            <h3 class="tf-feature-title">Milestones</h3>
            <p class="tf-feature-text">
                Break projects into achievable goals with timelines.
            </p>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="tf-feature-card">
            <!-- Collaboration (smaller icon) -->
            
            <h3 class="tf-feature-title">Collaboration</h3>
            <p class="tf-feature-text">
                Invite members, chat, and work together in real time.
            </p>
        </div>

        <div class="tf-feature-card">
            <!-- File management (smaller icon) -->
            
            <h3 class="tf-feature-title">File Management</h3>
            <p class="tf-feature-text">
                Upload, preview, and download files securely.
            </p>
        </div>

        <div class="tf-feature-card">
            
            <h3 class="tf-feature-title">Reports</h3>
            <p class="tf-feature-text">
                Analyze progress, deadlines, and productivity trends.
            </p>
        </div>
    </div>
</section>

<!-- ===== Styles ===== -->
<style>
.tf-bg {
    background:
        radial-gradient(900px 500px at 15% 20%, rgba(87,167,115,.35), transparent 60%),
        radial-gradient(900px 500px at 85% 25%, rgba(155,209,229,.35), transparent 60%),
        radial-gradient(1000px 600px at 50% 120%, rgba(106,142,174,.30), transparent 65%);
}

.tf-logo-glow {
    filter: drop-shadow(0 0 40px rgba(87,167,115,.9));
    animation: glow 3s ease-in-out infinite alternate;
}
@keyframes glow {
    to { filter: drop-shadow(0 0 65px rgba(87,167,115,1)); }
}

.tf-title { color:#157145; }
.tf-sub { color:rgba(15,23,42,.75); }
html.dark .tf-sub { color:#e9eef5; }

.tf-primary-btn-lg {
    padding:1rem 2.5rem;
    border-radius:1rem;
    background:#57A773;
    color:#fff;
    font-weight:700;
}

.tf-top-btn {
    background:#57A773;
    color:#fff;
    padding:.6rem 1.2rem;
    border-radius:1rem;
}
.tf-top-btn-outline {
    background:rgba(255,255,255,.85);
    padding:.6rem 1.2rem;
    border-radius:1rem;
}

.tf-toggle-btn {
    height:3rem;
    width:3rem;
    border-radius:1rem;
    background:rgba(255,255,255,.85);
    border:1px solid rgba(0,0,0,.15);
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}
.tf-toggle-icon {
    position:absolute;
    height:24px;
    width:24px;
}

html.dark .tf-toggle-btn {
    background:#121a22;
    border-color:rgba(255,255,255,.15);
    color:#e9eef5;
}

.tf-support-row {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:1rem;
    font-size:1.1rem;
    color:rgba(15,23,42,.75);
}
html.dark .tf-support-row { color:#e9eef5; }

.tf-support-icon {
    height:28px;
    width:28px;
    color:#57A773;
}

.tf-feature-card {
    height:320px;
    background:rgba(255,255,255,.9);
    backdrop-filter:blur(14px);
    border-radius:1.5rem;
    padding:2rem;
    border:1px solid rgba(0,0,0,.1);
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
}
html.dark .tf-feature-card {
    background:rgba(18,26,34,.85);
    border-color:rgba(255,255,255,.12);
}

.tf-feature-icon {
    height:48px;
    width:48px;
    color:#57A773;
    margin-bottom:1rem;
}

/* Smaller icons ONLY for collaboration & files */
.tf-feature-icon-sm {
    height:40px;
    width:40px;
    color:#57A773;
    margin-bottom:1rem;
}

.tf-feature-title {
    font-size:1.3rem;
    font-weight:700;
    margin-bottom:.5rem;
    color:#0f172a;
}
html.dark .tf-feature-title,
html.dark .tf-feature-text {
    color:#ffffff;
}

.tf-feature-text {
    line-height:1.6;
}
</style>

<!-- ===== Theme Logic ===== -->
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
