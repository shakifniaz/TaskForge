@props([
    'project',
    'active' => 'overview'
])

<div x-data="{ collapsed: false }" class="h-screen">
    {{-- Fixed Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 border-r border-black/10 transition-all duration-200 overflow-hidden tf-sidebar"
        :class="collapsed ? 'w-20' : 'w-72'"
        style="background-color:#ffffff;"
    >
        {{-- Sidebar decorative gradient (light + toned down in dark) --}}
        <div class="absolute inset-0 pointer-events-none opacity-70 tf-sb-grad"
             style="background:
                radial-gradient(900px 220px at 15% 12%, rgba(87,167,115,0.18), transparent 55%),
                radial-gradient(800px 260px at 90% 18%, rgba(155,209,229,0.28), transparent 58%),
                radial-gradient(900px 300px at 55% 105%, rgba(106,142,174,0.16), transparent 60%);">
        </div>

        {{-- Sidebar subtle shine --}}
        <div class="absolute -top-24 -left-24 h-64 w-64 rotate-12 opacity-15 pointer-events-none tf-sb-shine animate-pulse"
             style="background: linear-gradient(135deg, rgba(209,250,255,0.0), rgba(209,250,255,1), rgba(209,250,255,0.0));">
        </div>

        {{-- Sidebar Header --}}
        <div class="relative h-16 flex items-center justify-between px-4 border-b border-black/10">
            <div class="min-w-0" x-show="!collapsed" x-transition>
                <div class="mt-0.5 font-extrabold text-[18px] leading-6 text-gray-900 truncate tf-project-name">
                    {{ $project->name }}
                </div>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center h-10 w-10 rounded-2xl border border-black/10 bg-white/80 hover:bg-white hover:-translate-y-0.5 hover:shadow-md transition tf-sb-btn"
                @click="collapsed = !collapsed"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <svg class="h-5 w-5 tf-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z"/>
                </svg>
            </button>
        </div>

        @php
            $linkBase = "group flex items-center gap-3 px-3 py-2.5 text-sm rounded-2xl mx-3 transition transform-gpu";
            $activeCls = "bg-white/90 shadow-sm border border-black/10 text-black font-semibold";
            $idleCls = "text-black/80 hover:bg-white/70";
            $iconWrap = "h-10 w-10 rounded-2xl flex items-center justify-center border border-black/10 bg-white/70 group-hover:bg-white transition shrink-0";
            $sectionTitle = "px-6 mt-6 mb-2 text-[11px] font-semibold tracking-wider text-black/50 uppercase";
            $divider = "my-4 border-t border-black/10 mx-4";
        @endphp

        {{-- ✅ Scrollable sidebar content (only this scrolls) --}}
        <nav class="relative py-4 overflow-y-auto"
             style="height: calc(100vh - 4rem);">
            <div class="{{ $sectionTitle }}" x-show="!collapsed" x-transition>Main</div>

            <a href="{{ route('projects.overview', $project) }}"
               class="{{ $linkBase }} {{ $active === 'overview' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8.5Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 22v-7h6v7"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Overview</span>
            </a>

            <a href="{{ route('projects.chat', $project) }}"
               class="{{ $linkBase }} {{ $active === 'chat' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Chat</span>
            </a>

            <a href="{{ route('projects.tasks', $project) }}"
               class="{{ $linkBase }} {{ $active === 'tasks' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20 6-11 11-5-5"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Tasks</span>
            </a>

            <a href="{{ route('projects.board', $project) }}"
               class="{{ $linkBase }} {{ $active === 'board' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h7v14H4V5Zm9 0h7v8h-7V5Zm0 10h7v4h-7v-4Z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Board</span>
            </a>

            <a href="{{ route('projects.roadmap', $project) }}"
               class="{{ $linkBase }} {{ $active === 'roadmap' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 17h10M9 7v10M15 7v10"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Roadmap</span>
            </a>

            <a href="{{ route('projects.activity', $project) }}"
               class="{{ $linkBase }} {{ $active === 'activity' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3l2-6 4 12 2-6h3"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Activity</span>
            </a>

            <a href="{{ route('projects.files', $project) }}"
               class="{{ $linkBase }} {{ $active === 'files' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05 12.5 20a6 6 0 0 1-8.49-8.49l9.9-9.9a4.5 4.5 0 0 1 6.36 6.36l-9.9 9.9a2.5 2.5 0 1 1-3.54-3.54l8.84-8.84"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Files</span>
            </a>

            <a href="{{ route('projects.reports', $project) }}"
               class="{{ $linkBase }} {{ $active === 'reports' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 17v-6m4 6V7m4 10v-4"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Reports</span>
            </a>

            <div class="{{ $divider }}"></div>

            <div class="{{ $sectionTitle }}" x-show="!collapsed" x-transition>Project</div>

            <a href="{{ route('projects.members', $project) }}"
               class="{{ $linkBase }} {{ $active === 'members' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Members</span>
            </a>

            <a href="{{ route('projects.manage', $project) }}"
               class="{{ $linkBase }} {{ $active === 'manage' ? $activeCls : $idleCls }} tf-sb-link tf-hover">
                <span class="{{ $iconWrap }}">
                    <svg class="h-5 w-5 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a7.8 7.8 0 0 0 .1-6l2-1.2-2-3.4-2.2 1a8 8 0 0 0-5-2L12 1H8l-.3 2.2a8 8 0 0 0-5 2l-2.2-1-2 3.4L.5 9a7.8 7.8 0 0 0 .1 6L-1.5 16.2l2 3.4 2.2-1a8 8 0 0 0 5 2L8 23h4l.3-2.2a8 8 0 0 0 5-2l2.2 1 2-3.4-2-1.2Z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Manage Project</span>
            </a>

            {{-- little bottom padding so last item isn't flush --}}
            <div class="h-4"></div>
        </nav>
    </aside>

    {{-- Main area --}}
    <div class="h-screen flex flex-col" :class="collapsed ? 'ml-20' : 'ml-72'">

        {{-- Top Navbar (slightly thicker) --}}
        <header class="h-20 border-b border-black/10 flex items-center justify-between px-4 sm:px-6 lg:px-8 tf-topbar">
            {{-- Left: TaskForge --}}
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('assets/logos/TaskForgeGreen.svg') }}" alt="TaskForge" class="h-10 w-10" />
                <div class="min-w-0">
                    <div class="font-extrabold tf-title text-[20px] leading-6 truncate">
                        TaskForge
                    </div>
                </div>
            </div>

            {{-- Right: actions --}}
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

                {{-- Profile --}}
                <a href="{{ route('dashboard') }}"
                   title="Profile"
                   class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                          text-white hover:-translate-y-0.5 hover:shadow-lg transition tf-primary-btn"
                   style="background-color:#57A773;">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M12 2a5 5 0 100 10 5 5 0 000-10zM4 20a8 8 0 0116 0v1H4v-1z"
                              clip-rule="evenodd"/>
                    </svg>
                </a>

                {{-- Dark Mode --}}
                <button
                    type="button"
                    id="themeToggle"
                    title="Toggle dark mode"
                    class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                           border border-black/10 bg-white
                           hover:-translate-y-0.5 hover:shadow-lg transition tf-theme-btn"
                >
                    <svg id="iconMoon" class="h-7 w-7 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
                    </svg>
                    <svg id="iconSun" class="h-7 w-7 tf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" style="display:none;">
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
        </header>

        {{-- Scrollable content --}}
        <main class="flex-1 overflow-y-auto tf-page" style="background-color:#EFEFEF;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <style>
        .tf-topbar { background: rgba(255,255,255,0.75); backdrop-filter: blur(10px); }
        .tf-title { color: #157145; }

        /* Sidebar hover animation */
        .tf-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }
        .tf-hover:active { transform: translateY(0px); }

        /* Dark theme */
        html.dark body { background: #0b0f14; }
        html.dark .tf-page { background-color: #0f141a !important; }
        html.dark .tf-topbar {
            background: rgba(18, 26, 34, 0.70) !important;
            border-color: rgba(255,255,255,0.08) !important;
        }

        /* Sidebar dark */
        html.dark aside {
            background-color: #0f1a17 !important;
            border-color: rgba(255,255,255,0.08) !important;
        }

        /* Sidebar gradient toned down */
        html.dark .tf-sb-grad { opacity: 0.25 !important; }
        html.dark .tf-sb-shine { opacity: 0.10 !important; }

        /* Project name in sidebar should be white */
        html.dark .tf-project-name { color: #ffffff !important; }

        /* Titles */
        html.dark .tf-title { color: #e9eef5 !important; }

        /* Theme button */
        html.dark .tf-theme-btn {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #e9eef5 !important;
        }

        /* Logout in dark */
        html.dark .tf-logout-btn { background: #0b0f14 !important; }

        /* Collapse button dark in dark mode, icon stays white */
        html.dark .tf-sb-btn {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.10) !important;
        }

        /* Make ALL icons white in dark mode */
        html.dark .tf-icon {
            color: #ffffff !important;
            stroke: #ffffff !important;
            fill: none;
        }
        html.dark svg[fill="currentColor"] { fill: #ffffff !important; }

        /* Sidebar link colors */
        html.dark .text-black\/80 { color: rgba(233,238,245,0.75) !important; }
        html.dark .text-black\/50 { color: rgba(233,238,245,0.45) !important; }
        html.dark .border-black\/10 { border-color: rgba(255,255,255,0.10) !important; }

        html.dark .bg-white\/90,
        html.dark .bg-white\/70,
        html.dark .bg-white\/60 {
            background: rgba(18,26,34,0.70) !important;
        }

        /* Active sidebar item text white in dark mode */
        html.dark .tf-sb-link.bg-white\/90 { color: #ffffff !important; }
        html.dark .tf-sb-link.bg-white\/90 span,
        html.dark .tf-sb-link.bg-white\/90 svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* Optional: nicer scrollbar */
        nav::-webkit-scrollbar { width: 10px; }
        nav::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 999px; }
        html.dark nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.14); }
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

                if (iconSun && iconMoon) {
                    iconSun.style.display = isDark ? 'block' : 'none';
                    iconMoon.style.display = isDark ? 'none' : 'block';
                }
            }

            const saved = localStorage.getItem('tf_theme');
            apply(saved === 'dark' ? 'dark' : 'light');

            toggle?.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                apply(isDark ? 'light' : 'dark');
            });
        })();
    </script>
</div>
