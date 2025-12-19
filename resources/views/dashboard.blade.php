<x-app-layout>
    <div class="min-h-screen" style="background-color:#EFEFEF;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

            {{-- Header (Profile) --}}
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                {{-- Logo + Title --}}
                <div class="flex items-center gap-4">
                    <img
                        src="{{ asset('assets/logos/TaskForgeGreen.svg') }}"
                        alt="TaskForge"
                        class="h-16 w-16"
                    />

                    <div>
                        <h1 class="text-3xl font-bold tracking-tight tf-title" style="color:#157145;">
                            Profile
                        </h1>
                        <p class="mt-1 text-sm tf-subtitle text-gray-600">
                            Manage your account and preferences.
                        </p>
                    </div>
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-3">

                    {{-- Projects --}}
                    <a href="{{ route('projects.index') }}"
                       title="Projects"
                       class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                              text-white hover:-translate-y-0.5 hover:shadow-lg transition tf-primary-btn"
                       style="background-color:#57A773;">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                        </svg>
                    </a>

                    {{-- Dark Mode Toggle --}}
                    <button
                        type="button"
                        id="themeToggle"
                        title="Toggle dark mode"
                        class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                               border border-black/10 bg-white
                               hover:-translate-y-0.5 hover:shadow-lg transition tf-theme-btn"
                    >
                        {{-- Sun/Moon icons (swap in JS) --}}
                        <svg id="iconMoon" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                        </svg>
                        <svg id="iconSun" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0-1.414-1.414M7.05 7.05 5.636 5.636"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                        </svg>
                    </button>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                title="Logout"
                                class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                                       bg-gray-900 text-white
                                       hover:bg-gray-800 hover:-translate-y-0.5 hover:shadow-lg transition tf-logout-btn">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>

            @php
                $u = auth()->user();

                $photoUrl = null;
                if (!empty($u->profile_photo_path)) {
                    $photoUrl = str_starts_with($u->profile_photo_path, 'http')
                        ? $u->profile_photo_path
                        : asset('storage/'.$u->profile_photo_path);
                }

                $fallbackAvatar = "https://ui-avatars.com/api/?name=".urlencode($u->name ?? 'User')."&background=57A773&color=ffffff&size=256";
                $username = $u->username ? '@'.$u->username : null;

                // Clean bio (fix leading whitespace / weird indentation)
                $bio = $u->description ? trim($u->description) : null;
            @endphp

            {{-- Profile viewer card --}}
            <div class="relative overflow-hidden rounded-3xl border border-black/10 shadow-sm bg-white tf-card">

                {{-- Decorative gradient --}}
                <div class="absolute inset-0 opacity-60 pointer-events-none tf-grad"
                     style="background:
                        radial-gradient(800px 200px at 15% 20%, rgba(87,167,115,0.22), transparent 55%),
                        radial-gradient(700px 220px at 85% 10%, rgba(155,209,229,0.28), transparent 55%),
                        radial-gradient(800px 260px at 50% 110%, rgba(106,142,174,0.18), transparent 60%);">
                </div>

                {{-- Subtle animated shine --}}
                <div class="absolute -top-24 -left-24 h-64 w-64 rotate-12 opacity-20 pointer-events-none
                            animate-pulse tf-shine"
                     style="background: linear-gradient(135deg, rgba(209,250,255,0.0), rgba(209,250,255,1), rgba(209,250,255,0.0));">
                </div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col md:flex-row gap-8 md:items-start">

                        {{-- Big profile picture --}}
                        <div class="shrink-0 flex justify-center md:justify-start">
                            <img
                                src="{{ $photoUrl ?: $fallbackAvatar }}"
                                alt="Profile photo"
                                class="h-40 w-40 sm:h-44 sm:w-44 rounded-full object-cover
                                       border-4 border-white shadow-md"
                                onerror="this.onerror=null;this.src='{{ $fallbackAvatar }}';"
                            />
                        </div>

                        {{-- Profile info --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-2">
                                <h2 class="text-2xl font-bold tf-h text-gray-900 truncate">
                                    {{ $u->name }}
                                </h2>

                                <div class="flex flex-wrap items-center gap-3 text-sm tf-sub text-gray-700">
                                    @if($username)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full border border-black/10 tf-chip"
                                              style="background-color:#D1FAFF;">
                                            <span class="font-semibold" style="color:#157145;">{{ $username }}</span>
                                        </span>
                                    @endif

                                    <span class="truncate">{{ $u->email }}</span>
                                </div>

                                @if($bio)
                                    {{-- No whitespace padding / no pre indentation --}}
                                    <p class="mt-2 text-sm tf-sub text-gray-700 leading-relaxed">
                                        {{ $bio }}
                                    </p>
                                @else
                                    <p class="mt-2 text-sm tf-sub text-gray-600">
                                        Add a description to tell people what you’re working on.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('profile.edit.tab') }}"
                                   class="px-8 py-3 rounded-2xl font-semibold text-white
                                          transition transform-gpu
                                          hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0 active:shadow-md tf-primary-btn"
                                   style="background-color:#57A773;">
                                    Edit profile
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- My Projects --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold tf-h text-gray-900">My Projects</h2>
                    </div>

                    <a href="{{ route('projects.index') }}"
                       class="px-4 py-2 rounded-2xl font-semibold border border-black/10 bg-white
                              hover:-translate-y-0.5 hover:shadow-lg transition tf-theme-btn">
                        View all
                    </a>
                </div>

                @php
                    // Assumes your controller passes $projects (like your projects page),
                    // OR you can fetch here if needed.
                    // Best: pass $myProjects from controller, but fallback if not:
                    $myProjects = $myProjects ?? ($projects ?? collect());
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($myProjects as $project)
                        <a href="{{ route('projects.overview', $project) }}"
                           class="group rounded-3xl p-6 border border-black/10 bg-white tf-card
                                  transition transform-gpu
                                  hover:-translate-y-1 hover:shadow-xl">

                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-bold tf-h text-gray-900 truncate">
                                    {{ $project->name }}
                                </h3>

                                <span class="shrink-0 h-9 w-9 rounded-2xl flex items-center justify-center border border-black/10 tf-chip"
                                      style="background-color:#D1FAFF;">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#157145" stroke-width="2.25">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 12h6m-6 4h6M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H9l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="mt-5 flex justify-between items-center text-xs tf-sub text-gray-600">
                                <span>{{ $project->created_at->format('Y-m-d') }}</span>
                                <span class="font-semibold group-hover:underline" style="color:#157145;">
                                    Open →
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-black/10 bg-white tf-card p-6">
                            <div class="tf-h text-gray-900 font-semibold">No owned projects yet.</div>
                            <div class="mt-1 tf-sub text-sm text-gray-600">
                                Create a new project from the Projects page.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Dark mode styling (same system as index.blade.php) --}}
    <style>
        html.dark body { background: #0b0f14; }
        html.dark .min-h-screen { background-color: #0f141a !important; }

        html.dark .tf-title,
        html.dark .tf-h { color: #e9eef5 !important; }
        html.dark .tf-subtitle,
        html.dark .tf-sub { color: rgba(233, 238, 245, 0.70) !important; }

        html.dark .tf-card {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.08) !important;
        }

        html.dark .tf-theme-btn {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #e9eef5 !important;
        }

        html.dark .tf-logout-btn { background: #0b0f14 !important; }

        html.dark .tf-chip {
            background: rgba(209, 250, 255, 0.10) !important;
            border-color: rgba(255,255,255,0.10) !important;
        }

        html.dark .tf-grad { opacity: 0.25 !important; }
        html.dark .tf-shine { opacity: 0.10 !important; }

        html.dark img[alt="Profile photo"] { border-color: #121a22 !important; }
    </style>

    <script>
        (function () {
            const toggle = document.getElementById('themeToggle');
            const iconSun = document.getElementById('iconSun');
            const iconMoon = document.getElementById('iconMoon');

            function apply(mode) {
                const isDark = mode === 'dark';
                document.documentElement.classList.toggle('dark', isDark);
                localStorage.setItem('tf_theme', isDark ? 'dark' : 'light');

                // If dark => show sun (to switch back), else show moon
                if (iconSun && iconMoon) {
                    iconSun.style.display = isDark ? 'block' : 'none';
                    iconMoon.style.display = isDark ? 'none' : 'block';
                }
            }

            const saved = localStorage.getItem('tf_theme');
            if (saved === 'dark' || saved === 'light') apply(saved);
            else apply('light');

            toggle?.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                apply(isDark ? 'light' : 'dark');
            });
        })();
    </script>
</x-app-layout>
