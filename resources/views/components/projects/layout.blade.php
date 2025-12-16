@props([
    'project',
    'active' => 'overview', // overview|chat|tasks|board|roadmap|activity|files|reports|members|manage
])

<div class="min-h-screen bg-gray-50">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside x-data="{ collapsed: false }"
               class="relative border-r border-black/10 transition-all duration-200 ease-in-out"
               :class="collapsed ? 'w-14' : 'w-80'"
               style="background-color:#D1FAFF;">

            {{-- Top bar --}}
            <div class="h-16 flex items-center justify-between border-b border-black/10"
                 :class="collapsed ? 'px-2' : 'px-4'">

                <div class="min-w-0" x-show="!collapsed" x-transition>
                    <div class="text-[11px] font-semibold tracking-wider text-black/60 uppercase">
                        Project
                    </div>
                    <div class="mt-0.5 font-semibold text-black truncate">
                        {{ $project->name }}
                    </div>
                </div>

                <button type="button"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-black/10 bg-white/70 hover:bg-white transition"
                        @click="collapsed = !collapsed"
                        :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                    <svg class="h-5 w-5 text-black/70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z"/>
                    </svg>
                </button>
            </div>

            @php
                $linkBase = "group relative flex items-center rounded-xl transition";
                $activeCls = "bg-white/85 shadow-sm border border-black/10 text-black font-semibold";
                $idleCls   = "text-black/80 hover:bg-white/60 hover:text-black";

                // When collapsed we center icons and reduce padding
                $linkPadExpanded = "px-3 py-2.5 mx-3";
                $linkPadCollapsed = "px-2 py-2 mx-2 justify-center";

                $iconWrapExpanded = "h-8 w-8 rounded-lg flex items-center justify-center border border-black/10 bg-white/60 group-hover:bg-white transition shrink-0";
                $iconWrapCollapsed = "h-8 w-8 rounded-lg flex items-center justify-center border border-black/10 bg-white/60 group-hover:bg-white transition";

                $labelWrap = "truncate";
                $sectionTitle = "mt-6 mb-2 text-[11px] font-semibold tracking-wider text-black/50 uppercase";
                $divider = "my-4 border-t border-black/10";
            @endphp

            <nav class="py-3">
                {{-- MAIN label --}}
                <div class="px-6 {{ $sectionTitle }}" x-show="!collapsed" x-transition>Main</div>

                {{-- Overview --}}
                <a href="{{ route('projects.overview', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'overview' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r bg-black/25"
                          x-show="!collapsed && '{{ $active }}' === 'overview'"></span>

                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">🏠</span>
                    </span>

                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Overview</span>
                </a>

                {{-- Chat --}}
                <a href="{{ route('projects.chat', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'chat' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">💬</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Chat</span>
                </a>

                {{-- Tasks --}}
                <a href="{{ route('projects.tasks', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'tasks' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">✅</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Tasks</span>
                </a>

                {{-- Board --}}
                <a href="{{ route('projects.board', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'board' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">🗂️</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Board</span>
                </a>

                {{-- Roadmap --}}
                <a href="{{ route('projects.roadmap', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'roadmap' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">🗺️</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Roadmap</span>
                </a>

                {{-- Activity --}}
                <a href="{{ route('projects.activity', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'activity' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">📌</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Activity</span>
                </a>

                {{-- Files --}}
                <a href="{{ route('projects.files', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'files' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">📎</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Files</span>
                </a>

                {{-- Reports --}}
                <a href="{{ route('projects.reports', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'reports' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">📊</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Reports</span>
                </a>

                <div class="{{ $divider }} mx-4"></div>

                {{-- ADMIN label --}}
                <div class="px-6 {{ $sectionTitle }}" x-show="!collapsed" x-transition>Project</div>

                {{-- Members --}}
                <a href="{{ route('projects.members', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'members' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">👥</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Members</span>
                </a>

                {{-- Manage Project --}}
                <a href="{{ route('projects.manage', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'manage' ? $activeCls : $idleCls }}"
                   :class="collapsed ? '{{ $linkPadCollapsed }}' : '{{ $linkPadExpanded }}'">
                    <span :class="collapsed ? '{{ $iconWrapCollapsed }}' : '{{ $iconWrapExpanded }}'">
                        <span class="text-base leading-none">⚙️</span>
                    </span>
                    <span class="ml-3 {{ $labelWrap }}" x-show="!collapsed" x-transition>Manage Project</span>
                </a>
            </nav>

            {{-- Bottom hint --}}
            <div class="absolute bottom-3 left-0 right-0 px-3">
                <div class="rounded-xl border border-black/10 bg-white/60 px-3 py-2 text-xs text-black/60"
                     x-show="!collapsed" x-transition>
                    Tip: Collapse the sidebar to focus.
                </div>
            </div>
        </aside>

        {{-- Content --}}
        <main class="flex-1 min-h-screen bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
