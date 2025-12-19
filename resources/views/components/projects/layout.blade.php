@props([
    'project',
    'active' => 'overview'
])

<div x-data="{ collapsed: false }" class="h-screen bg-gray-50">
    {{-- Fixed Sidebar (non-scrollable) --}}
    <aside
        class="fixed inset-y-0 left-0 border-r border-black/10 transition-all duration-200 overflow-hidden z-40"
        :class="collapsed ? 'w-20' : 'w-72'"
        style="background-color:#D1FAFF;"
    >
        {{-- Sidebar Header --}}
        <div class="h-16 flex items-center justify-between px-4 border-b border-black/10">
            <div class="min-w-0" x-show="!collapsed" x-transition>
                <div class="text-xs font-semibold tracking-wider text-black/60 uppercase">Project</div>
                <div class="mt-0.5 font-semibold text-black truncate">{{ $project->name }}</div>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-black/10 bg-white/60 hover:bg-white transition"
                @click="collapsed = !collapsed"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <svg class="h-5 w-5 text-black/70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z"/>
                </svg>
            </button>
        </div>

        @php
            $linkBase = "group flex items-center gap-3 px-3 py-2.5 text-sm rounded-xl mx-3 transition";
            $activeCls = "bg-white/80 shadow-sm border border-black/10 text-black font-semibold";
            $idleCls = "text-black/80 hover:bg-white/60";
            $iconWrap = "h-9 w-9 rounded-lg flex items-center justify-center border border-black/10 bg-white/50 group-hover:bg-white transition shrink-0";
            $sectionTitle = "px-6 mt-6 mb-2 text-xs font-semibold tracking-wider text-black/50 uppercase";
            $divider = "my-4 border-t border-black/10 mx-4";
            $iconCls = "h-5 w-5 text-black/80";
        @endphp

        {{-- Sidebar Navigation (non-scrollable) --}}
        <nav class="py-4">
            <div class="{{ $sectionTitle }}" x-show="!collapsed" x-transition>Main</div>

            <a href="{{ route('projects.overview', $project) }}"
               class="{{ $linkBase }} {{ $active === 'overview' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10.5L12 3l9 7.5V21a1.5 1.5 0 01-1.5 1.5H4.5A1.5 1.5 0 013 21V10.5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Overview</span>
            </a>

            <a href="{{ route('projects.chat', $project) }}"
               class="{{ $linkBase }} {{ $active === 'chat' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 10h8M8 14h5M21 12c0 4.418-4.03 8-9 8a10.6 10.6 0 01-4-.77L3 20l1.2-3.6A7.64 7.64 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Chat</span>
            </a>

            <a href="{{ route('projects.tasks', $project) }}"
               class="{{ $linkBase }} {{ $active === 'tasks' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Tasks</span>
            </a>

            <a href="{{ route('projects.board', $project) }}"
               class="{{ $linkBase }} {{ $active === 'board' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 4h6v16H9V4zM4 8h4v12H4V8zm12 6h4v6h-4v-6z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Board</span>
            </a>

            <a href="{{ route('projects.roadmap', $project) }}"
               class="{{ $linkBase }} {{ $active === 'roadmap' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 3v18M18 3v18M6 8h12M6 16h12"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Roadmap</span>
            </a>

            <a href="{{ route('projects.activity', $project) }}"
               class="{{ $linkBase }} {{ $active === 'activity' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Activity</span>
            </a>

            <a href="{{ route('projects.files', $project) }}"
               class="{{ $linkBase }} {{ $active === 'files' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 15V7a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 17l-1 1a3 3 0 01-4 0 3 3 0 010-4l5-5a3 3 0 014 4l-4 4"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Files</span>
            </a>

            <a href="{{ route('projects.reports', $project) }}"
               class="{{ $linkBase }} {{ $active === 'reports' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 19V5m0 14h16M8 17V9m4 8V7m4 10v-6"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Reports</span>
            </a>

            <div class="{{ $divider }}"></div>

            <div class="{{ $sectionTitle }}" x-show="!collapsed" x-transition>Project</div>

            <a href="{{ route('projects.members', $project) }}"
               class="{{ $linkBase }} {{ $active === 'members' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m5-3a4 4 0 10-8 0 4 4 0 008 0zm6 4a3 3 0 10-6 0 3 3 0 006 0z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Members</span>
            </a>

            <a href="{{ route('projects.manage', $project) }}"
               class="{{ $linkBase }} {{ $active === 'manage' ? $activeCls : $idleCls }}">
                <span class="{{ $iconWrap }}">
                    <svg class="{{ $iconCls }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.983 13.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.4 15a7.97 7.97 0 000-6l2-1.5-2-3.5-2.3 1a8.2 8.2 0 00-5.2-2L11 1H7l-.9 2A8.2 8.2 0 002.9 5l-2.3-1L-1.4 7.5.6 11l2 1.5a7.97 7.97 0 000 6l-2 1.5 2 3.5 2.3-1a8.2 8.2 0 005.2 2L13 23h4l.9-2a8.2 8.2 0 003.2-2l2.3 1 2-3.5-2-1.5z"/>
                    </svg>
                </span>
                <span x-show="!collapsed" x-transition>Manage Project</span>
            </a>
        </nav>
    </aside>

    {{-- Main area starts to the right of sidebar --}}
    <div class="h-screen flex flex-col" :class="collapsed ? 'ml-20' : 'ml-72'">

        {{-- Project Navbar (icons only) --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="min-w-0">
                <div class="text-xs text-gray-500">TaskForge</div>
                <div class="font-semibold text-gray-900 truncate">{{ $project->name }}</div>
            </div>

            <div class="flex items-center gap-2">

                {{-- All Projects --}}
                <a href="{{ route('projects.index') }}"
                   title="All Projects"
                   class="h-10 w-10 flex items-center justify-center rounded-xl border border-gray-300 bg-white hover:bg-gray-100 transition">
                    <svg class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                </a>

                {{-- Profile --}}
                <a href="{{ route('dashboard') }}"
                   title="Profile"
                   class="h-10 w-10 flex items-center justify-center rounded-xl border border-gray-300 bg-white hover:bg-gray-100 transition">
                    <svg class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Logout"
                            class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>

            </div>
        </header>

        {{-- Scrollable project content only --}}
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
