@props([
    'project',
    'active' => 'overview', // overview|chat|tasks|board|roadmap|activity|files|reports|members|manage
])

<div class="min-h-screen">
    <div class="flex">
        {{-- Sidebar --}}
        <aside x-data="{ collapsed: false }"
               class="border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-200"
               :class="collapsed ? 'w-16' : 'w-64'">

            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700">
                <div class="overflow-hidden" x-show="!collapsed" x-transition>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Project</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $project->name }}
                    </div>
                </div>

                <button type="button"
                        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-900 text-gray-600 dark:text-gray-300"
                        @click="collapsed = !collapsed"
                        :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z"/>
                    </svg>
                </button>
            </div>

            <nav class="py-4 space-y-1">
                @php
                    $linkBase = "flex items-center gap-3 px-4 py-2 text-sm rounded-md mx-2 transition";
                    $activeCls = "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-semibold";
                    $idleCls = "text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900";
                    $iconCls = "h-5 w-5 shrink-0";
                    $labelWrap = "overflow-hidden";
                @endphp

                <a href="{{ route('projects.overview', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'overview' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">🏠</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Overview</span>
                </a>

                <a href="{{ route('projects.chat', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'chat' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">💬</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Chat</span>
                </a>

                <a href="{{ route('projects.tasks', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'tasks' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">✅</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Tasks</span>
                </a>

                <a href="{{ route('projects.board', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'board' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">🗂️</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Board</span>
                </a>

                <a href="{{ route('projects.roadmap', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'roadmap' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">🗺️</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Roadmap</span>
                </a>

                <a href="{{ route('projects.activity', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'activity' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">📌</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Activity</span>
                </a>

                <a href="{{ route('projects.files', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'files' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">📎</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Files</span>
                </a>

                <a href="{{ route('projects.reports', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'reports' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">📊</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Reports</span>
                </a>

                <div class="my-3 border-t border-gray-200 dark:border-gray-700"></div>

                <a href="{{ route('projects.members', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'members' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">👥</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Members</span>
                </a>

                <a href="{{ route('projects.manage', $project) }}"
                   class="{{ $linkBase }} {{ $active === 'manage' ? $activeCls : $idleCls }}">
                    <span class="{{ $iconCls }}">⚙️</span>
                    <span class="{{ $labelWrap }}" x-show="!collapsed" x-transition>Manage Project</span>
                </a>
            </nav>
        </aside>

        {{-- Content --}}
        <main class="flex-1 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
