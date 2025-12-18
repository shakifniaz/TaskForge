<x-app-layout>
    <x-projects.layout :project="$project" active="overview">

        <div class="space-y-6">

            {{-- 1) Project Summary --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="min-w-0">
                        <h1 class="text-2xl font-semibold text-gray-900 truncate">
                            {{ $project->name }}
                        </h1>

                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <div>
                                <span class="font-semibold">Owner:</span>
                                <span class="font-medium">
                                    {{ $owner?->username ? '@'.$owner->username : ($owner?->name ?? 'Unknown') }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold">Members:</span>
                                <span class="font-medium">{{ $membersCount }}</span>
                            </div>

                            <div>
                                <span class="font-semibold">Created:</span>
                                <span class="font-medium">{{ $project->created_at?->format('Y-m-d') }}</span>
                            </div>

                            {{-- GitHub connected (full link + copy) --}}
                            <div>
                                <span class="font-semibold">GitHub:</span>

                                @if($githubConnected)
                                    @php
                                        // Force repo url to always look like: https://github.com/username/Project
                                        $raw = trim((string)$project->github_repo);
                                        $raw = preg_replace('#^https?://(www\.)?github\.com/#', '', $raw);
                                        $raw = ltrim($raw, '/');
                                        $repoUrl = 'https://github.com/' . $raw;
                                    @endphp

                                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-1 rounded-lg bg-green-50 border border-green-200 text-green-800 text-xs font-semibold">
                                            Connected
                                        </span>

                                        <a href="{{ $repoUrl }}"
                                           target="_blank"
                                           class="text-sm text-blue-700 underline break-all"
                                           id="githubRepoLink"
                                           data-repo-url="{{ $repoUrl }}">
                                            {{ $repoUrl }}
                                        </a>

                                        <button
                                            type="button"
                                            onclick="copyRepoLink()"
                                            class="px-2 py-1 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-xs font-semibold">
                                            Copy
                                        </button>

                                        <span id="copyStatus" class="text-xs text-green-600 hidden">
                                            Copied!
                                        </span>
                                    </div>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold">
                                        Not connected
                                    </span>
                                @endif
                            </div>

                            {{-- Optional later: Description --}}
                            {{-- <div class="pt-2 text-gray-600">{{ $project->description }}</div> --}}
                        </div>
                    </div>

                    {{-- Optional actions --}}
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('projects.members', $project) }}"
                           class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                            Invite member
                        </a>

                        <a href="{{ route('projects.tasks.create', $project) }}"
                           class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 text-sm font-semibold">
                            Create task
                        </a>

                        <a href="{{ route('projects.board', $project) }}"
                           class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                            Open board
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2) Progress indicators --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tasks</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalTasks }}</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Completed: <span class="font-semibold">{{ $completedCount }}</span>
                        · Rate: <span class="font-semibold">{{ $completionRate }}%</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Work in progress</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $inProgressCount }}</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Pending: <span class="font-semibold">{{ $pendingCount }}</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Milestones</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $milestonesTotal }}</div>
                    <div class="mt-1 text-sm text-gray-600">
                        @if($activeMilestone)
                            Next: <span class="font-semibold">{{ $activeMilestone->title }}</span>
                        @else
                            <span class="text-gray-500">No upcoming milestones</span>
                        @endif
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Uploads</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $uploadsCount }}</div>
                    <div class="mt-1 text-sm text-gray-600">Project & task attachments</div>
                </div>
            </div>

            {{-- 3) Task Status Breakdown --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Task status</h2>
                        <p class="text-sm text-gray-500">Quick breakdown</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Assigned to me: <span class="font-semibold text-gray-900">{{ $assignedToMe }}</span>
                    </div>
                </div>

                @php
                    $total = max($totalTasks, 1);
                    $todoPct = round(($todoCount / $total) * 100);
                    $progPct = round(($inProgressCount / $total) * 100);
                    $donePct = round(($completedCount / $total) * 100);
                @endphp

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>To do</span><span class="font-semibold">{{ $todoCount }} ({{ $todoPct }}%)</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-3 bg-gray-400" style="width: {{ $todoPct }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>In progress</span><span class="font-semibold">{{ $inProgressCount }} ({{ $progPct }}%)</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-3 bg-gray-700" style="width: {{ $progPct }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Completed</span><span class="font-semibold">{{ $completedCount }} ({{ $donePct }}%)</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-3 bg-black" style="width: {{ $donePct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4) Deadlines overview --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Overdue</h2>
                    <p class="text-sm text-gray-500">Tasks past their due date</p>

                    <div class="mt-4 space-y-3">
                        @forelse($overdueTasks as $t)
                            <div class="p-4 rounded-xl border border-gray-200 bg-red-50">
                                <div class="font-semibold text-gray-900">{{ $t->title }}</div>
                                <div class="mt-1 text-sm text-gray-700">
                                    Due: <span class="font-semibold">{{ $t->due_date }}</span>
                                    · Assignee:
                                    <span class="font-semibold">
                                        {{ $t->assignee?->username ? '@'.$t->assignee->username : ($t->assignee?->name ?? 'Unassigned') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No overdue tasks 🎉</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Due soon</h2>
                    <p class="text-sm text-gray-500">Next 7 days</p>

                    <div class="mt-4 space-y-3">
                        @forelse($dueSoonTasks as $t)
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                                <div class="font-semibold text-gray-900">{{ $t->title }}</div>
                                <div class="mt-1 text-sm text-gray-700">
                                    Due: <span class="font-semibold">{{ $t->due_date }}</span>
                                    · Assignee:
                                    <span class="font-semibold">
                                        {{ $t->assignee?->username ? '@'.$t->assignee->username : ($t->assignee?->name ?? 'Unassigned') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No upcoming deadlines.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 5) Recent Activity Snapshot --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Recent activity</h2>
                        <p class="text-sm text-gray-500">Quick snapshot</p>
                    </div>

                    <a href="{{ route('projects.activity', $project) }}"
                       class="text-sm font-semibold underline text-gray-700">
                        View all →
                    </a>
                </div>

                <div class="mt-4">
                    @if($githubConnected)
                        <div class="text-sm text-gray-600">
                            GitHub is connected. Activity details are available in the Activity tab.
                        </div>
                    @else
                        <div class="text-sm text-gray-600">
                            No activity yet. Connect GitHub in Activity or start creating tasks and milestones.
                        </div>
                    @endif
                </div>
            </div>

            {{-- 6) Quick links --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Quick links</h2>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('projects.tasks', $project) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                        Open Tasks
                    </a>

                    <a href="{{ route('projects.board', $project) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                        Open Board
                    </a>

                    <a href="{{ route('projects.roadmap', $project) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                        Open Roadmap
                    </a>

                    <a href="{{ route('projects.members', $project) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                        Open Members
                    </a>

                    <a href="{{ route('projects.files', $project) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                        Open Files
                    </a>
                </div>
            </div>

        </div>

        {{-- Copy-to-clipboard script --}}
        <script>
            function copyRepoLink() {
                const link = document.getElementById('githubRepoLink');
                const status = document.getElementById('copyStatus');
                if (!link) return;

                // copy the forced normalized url
                const url = link.dataset.repoUrl || link.textContent.trim();

                navigator.clipboard.writeText(url)
                    .then(() => {
                        status.classList.remove('hidden');
                        setTimeout(() => status.classList.add('hidden'), 1500);
                    })
                    .catch(() => {
                        alert('Failed to copy link');
                    });
            }
        </script>

    </x-projects.layout>
</x-app-layout>
