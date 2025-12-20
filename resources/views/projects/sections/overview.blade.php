<x-app-layout>
    <x-projects.layout :project="$project" active="overview">

        <div class="space-y-6">

            {{-- 1) Project Summary --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                {{-- gradient overlay --}}
                <div class="absolute inset-0 pointer-events-none tf-grad"
                     style="background:
                        radial-gradient(900px 240px at 14% 18%, rgba(87,167,115,0.18), transparent 58%),
                        radial-gradient(760px 240px at 88% 14%, rgba(155,209,229,0.22), transparent 58%),
                        radial-gradient(900px 300px at 52% 120%, rgba(106,142,174,0.14), transparent 62%);">
                </div>

                {{-- subtle shine --}}
                <div class="absolute -top-24 -left-24 h-72 w-72 rotate-12 opacity-10 pointer-events-none tf-shine animate-pulse"
                     style="background: linear-gradient(135deg, rgba(209,250,255,0.0), rgba(209,250,255,1), rgba(209,250,255,0.0));">
                </div>

                <div class="relative p-5 sm:p-7 lg:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tf-h truncate">
                                {{ $project->name }}
                            </h1>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm tf-sub">
                                <div class="flex items-center justify-between sm:justify-start gap-2">
                                    <span class="font-semibold tf-h">Owner:</span>
                                    <span class="font-semibold">
                                        {{ $owner?->username ? '@'.$owner->username : ($owner?->name ?? 'Unknown') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between sm:justify-start gap-2">
                                    <span class="font-semibold tf-h">Members:</span>
                                    <span class="font-semibold">{{ $membersCount }}</span>
                                </div>

                                <div class="flex items-center justify-between sm:justify-start gap-2">
                                    <span class="font-semibold tf-h">Created:</span>
                                    <span class="font-semibold">{{ $project->created_at?->format('Y-m-d') }}</span>
                                </div>

                                {{-- GitHub --}}
                                <div class="sm:col-span-2">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold tf-h">GitHub:</span>

                                            @if($githubConnected)
                                                @php
                                                    $raw = trim((string)$project->github_repo);
                                                    $raw = preg_replace('#^https?://(www\.)?github\.com/#', '', $raw);
                                                    $raw = ltrim($raw, '/');
                                                    $repoUrl = 'https://github.com/' . $raw;
                                                @endphp

                                                <span class="px-2 py-1 rounded-xl border border-black/10 text-xs font-semibold tf-badge tf-badge-ok">
                                                    Connected
                                                </span>

                                                <div class="flex items-center gap-2 min-w-0">
                                                    <a href="{{ $repoUrl }}"
                                                       target="_blank"
                                                       class="tf-link underline break-all sm:break-normal sm:truncate"
                                                       style="max-width: 520px;"
                                                       id="githubRepoLink"
                                                       data-repo-url="{{ $repoUrl }}">
                                                        {{ $repoUrl }}
                                                    </a>

                                                    {{-- smaller copy button right next to link --}}
                                                    <button
                                                        type="button"
                                                        onclick="copyRepoLink()"
                                                        class="px-2 py-1 rounded-xl border border-black/10 bg-white/80
                                                               hover:bg-white hover:-translate-y-0.5 hover:shadow-sm transition
                                                               text-[11px] font-semibold tf-btn-mini">
                                                        Copy
                                                    </button>

                                                    <span id="copyStatus" class="text-[11px] font-semibold tf-copy hidden">
                                                        Copied!
                                                    </span>
                                                </div>
                                            @else
                                                <span class="px-2 py-1 rounded-xl border border-black/10 text-xs font-semibold tf-badge">
                                                    Not connected
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Desktop action column --}}
                        <div class="hidden lg:flex flex-col gap-2 w-56">
                            <a href="{{ route('projects.members', $project) }}"
                               class="px-4 py-3 rounded-2xl border border-black/10 bg-white/80
                                      hover:bg-white hover:-translate-y-0.5 hover:shadow-md transition tf-btn-soft text-sm font-semibold text-center">
                                Invite member
                            </a>

                            <a href="{{ route('projects.tasks.create', $project) }}"
                               class="px-4 py-3 rounded-2xl text-white text-sm font-semibold text-center
                                      hover:-translate-y-0.5 hover:shadow-lg transition tf-btn-primary"
                               style="background-color:#57A773;">
                                Create task
                            </a>

                            <a href="{{ route('projects.board', $project) }}"
                               class="px-4 py-3 rounded-2xl border border-black/10 bg-white/80
                                      hover:bg-white hover:-translate-y-0.5 hover:shadow-md transition tf-btn-soft text-sm font-semibold text-center">
                                Open board
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Mobile sticky action bar --}}
            <div class="lg:hidden sticky bottom-3 z-20">
                <div class="rounded-3xl border border-black/10 bg-white/80 backdrop-blur-xl shadow-lg p-3 tf-card tf-mobilebar tf-hovercard">
                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('projects.members', $project) }}"
                           class="px-3 py-3 rounded-2xl border border-black/10 bg-white/90
                                  hover:-translate-y-0.5 hover:shadow-md transition tf-btn-soft text-xs font-semibold text-center">
                            Invite
                        </a>

                        <a href="{{ route('projects.tasks.create', $project) }}"
                           class="px-3 py-3 rounded-2xl text-white text-xs font-semibold text-center
                                  hover:-translate-y-0.5 hover:shadow-lg transition tf-btn-primary"
                           style="background-color:#57A773;">
                            Create
                        </a>

                        <a href="{{ route('projects.board', $project) }}"
                           class="px-3 py-3 rounded-2xl border border-black/10 bg-white/90
                                  hover:-translate-y-0.5 hover:shadow-md transition tf-btn-soft text-xs font-semibold text-center">
                            Board
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2) Progress indicators --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 tf-card tf-hovercard">
                    <div class="text-xs font-semibold uppercase tracking-wider tf-kicker">Tasks</div>
                    <div class="mt-2 text-3xl font-extrabold tf-h">{{ $totalTasks }}</div>
                    <div class="mt-1 text-sm tf-sub">
                        Completed: <span class="font-semibold">{{ $completedCount }}</span>
                        · Rate: <span class="font-semibold">{{ $completionRate }}%</span>
                    </div>
                </div>

                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 tf-card tf-hovercard">
                    <div class="text-xs font-semibold uppercase tracking-wider tf-kicker">Work in progress</div>
                    <div class="mt-2 text-3xl font-extrabold tf-h">{{ $inProgressCount }}</div>
                    <div class="mt-1 text-sm tf-sub">
                        Pending: <span class="font-semibold">{{ $pendingCount }}</span>
                    </div>
                </div>

                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 tf-card tf-hovercard">
                    <div class="text-xs font-semibold uppercase tracking-wider tf-kicker">Milestones</div>
                    <div class="mt-2 text-3xl font-extrabold tf-h">{{ $milestonesTotal }}</div>
                    <div class="mt-1 text-sm tf-sub">
                        @if($activeMilestone)
                            Next: <span class="font-semibold">{{ $activeMilestone->title }}</span>
                        @else
                            <span class="opacity-70">No upcoming milestones</span>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 tf-card tf-hovercard">
                    <div class="text-xs font-semibold uppercase tracking-wider tf-kicker">Uploads</div>
                    <div class="mt-2 text-3xl font-extrabold tf-h">{{ $uploadsCount }}</div>
                    <div class="mt-1 text-sm tf-sub">Project & task attachments</div>
                </div>
            </section>

            {{-- 3) Task Status Breakdown --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                <div class="absolute inset-0 pointer-events-none tf-grad"
                     style="background:
                        radial-gradient(900px 220px at 12% 18%, rgba(87,167,115,0.14), transparent 60%),
                        radial-gradient(840px 260px at 92% 12%, rgba(155,209,229,0.18), transparent 62%);">
                </div>

                <div class="relative p-5 sm:p-7 lg:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold tf-h">Task status</h2>
                            <p class="text-sm tf-sub">Quick breakdown</p>
                        </div>
                        <div class="text-sm tf-sub">
                            Assigned to me: <span class="font-semibold tf-h">{{ $assignedToMe }}</span>
                        </div>
                    </div>

                    @php
                        $total = max($totalTasks, 1);
                        $todoPct = round(($todoCount / $total) * 100);
                        $progPct = round(($inProgressCount / $total) * 100);
                        $donePct = round(($completedCount / $total) * 100);
                    @endphp

                    <div class="mt-6 space-y-5">
                        <div>
                            <div class="flex justify-between text-sm tf-sub">
                                <span class="font-medium">To do</span>
                                <span class="font-semibold tf-h">{{ $todoCount }} ({{ $todoPct }}%)</span>
                            </div>
                            <div class="mt-2 h-3 rounded-full tf-bar-bg overflow-hidden">
                                <div class="h-3 tf-bar-todo" style="width: {{ $todoPct }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm tf-sub">
                                <span class="font-medium">In progress</span>
                                <span class="font-semibold tf-h">{{ $inProgressCount }} ({{ $progPct }}%)</span>
                            </div>
                            <div class="mt-2 h-3 rounded-full tf-bar-bg overflow-hidden">
                                <div class="h-3 tf-bar-prog" style="width: {{ $progPct }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm tf-sub">
                                <span class="font-medium">Completed</span>
                                <span class="font-semibold tf-h">{{ $completedCount }} ({{ $donePct }}%)</span>
                            </div>
                            <div class="mt-2 h-3 rounded-full tf-bar-bg overflow-hidden">
                                <div class="h-3 tf-bar-done" style="width: {{ $donePct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4) Deadlines overview --}}
            <section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 sm:p-7 lg:p-8 tf-card tf-hovercard">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold tf-h">Overdue</h2>
                            <p class="text-sm tf-sub">Tasks past their due date</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold tf-pill tf-pill-danger">
                            {{ $overdueTasks->count() }}
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($overdueTasks as $t)
                            <div class="p-4 rounded-2xl border border-black/10 tf-item tf-danger tf-hoveritem">
                                <div class="font-semibold tf-h truncate">{{ $t->title }}</div>
                                <div class="mt-1 text-sm tf-sub">
                                    Due: <span class="font-semibold tf-h">{{ $t->due_date }}</span>
                                    · Assignee:
                                    <span class="font-semibold tf-h">
                                        {{ $t->assignee?->username ? '@'.$t->assignee->username : ($t->assignee?->name ?? 'Unassigned') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm tf-sub opacity-75">No overdue tasks 🎉</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-5 sm:p-7 lg:p-8 tf-card tf-hovercard">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold tf-h">Due soon</h2>
                            <p class="text-sm tf-sub">Next 7 days</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold tf-pill tf-pill-neutral">
                            {{ $dueSoonTasks->count() }}
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($dueSoonTasks as $t)
                            <div class="p-4 rounded-2xl border border-black/10 tf-item tf-neutral tf-hoveritem">
                                <div class="font-semibold tf-h truncate">{{ $t->title }}</div>
                                <div class="mt-1 text-sm tf-sub">
                                    Due: <span class="font-semibold tf-h">{{ $t->due_date }}</span>
                                    · Assignee:
                                    <span class="font-semibold tf-h">
                                        {{ $t->assignee?->username ? '@'.$t->assignee->username : ($t->assignee?->name ?? 'Unassigned') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm tf-sub opacity-75">No upcoming deadlines.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <script>
            function copyRepoLink() {
                const link = document.getElementById('githubRepoLink');
                const status = document.getElementById('copyStatus');
                if (!link) return;

                const url = link.dataset.repoUrl || link.textContent.trim();

                navigator.clipboard.writeText(url)
                    .then(() => {
                        status.classList.remove('hidden');
                        setTimeout(() => status.classList.add('hidden'), 1500);
                    })
                    .catch(() => alert('Failed to copy link'));
            }
        </script>

        {{-- Page styles (NO theme toggle IDs here; relies on html.dark from layout) --}}
        <style>
            .tf-h { color: #0f172a; }
            .tf-sub { color: rgba(15, 23, 42, 0.72); }
            .tf-kicker { color: rgba(15, 23, 42, 0.55); }
            .tf-link { color: #1d4ed8; }
            .tf-copy { color: #157145; }

            .tf-card {
                position: relative;
                overflow: hidden;
            }

            section.tf-grad-card.tf-hovercard {
                transform: none !important;
                box-shadow: none !important;
            }

            section.tf-grad-card.tf-hovercard:hover {
                transform: none !important;
                box-shadow: none !important;
            }

            .tf-card:not(.tf-grad-card)::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    120deg,
                    rgba(87,167,115,0.18),
                    rgba(155,209,229,0.14),
                    transparent 70%
                );
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .tf-card:not(.tf-grad-card):hover::before {
                opacity: 1;
            }

            .tf-grad-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(900px 220px at 12% 18%, rgba(87,167,115,0.18), transparent 60%),
                    radial-gradient(840px 260px at 92% 12%, rgba(155,209,229,0.22), transparent 62%);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .tf-grad-card:hover::before {
                opacity: 1;
            }

            .tf-hoveritem {
                transition: transform 200ms ease, box-shadow 200ms ease;
            }

            .tf-hoveritem:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 24px rgba(0,0,0,0.10);
            }

            .tf-badge { background: rgba(155,209,229,0.18); color: rgba(15,23,42,0.85); }
            .tf-badge-ok { background: rgba(87,167,115,0.16); color: #0b3d2a; }

            .tf-pill { border: 1px solid rgba(0,0,0,0.08); }
            .tf-pill-danger { background: rgba(239,68,68,0.12); color: rgba(127,29,29,0.95); }
            .tf-pill-neutral { background: rgba(106,142,174,0.12); color: rgba(15,23,42,0.75); }

            .tf-bar-bg { background: rgba(15, 23, 42, 0.08); }
            .tf-bar-todo { background: rgba(106,142,174,0.65); }
            .tf-bar-prog { background: rgba(21,113,69,0.85); }
            .tf-bar-done { background: rgba(15, 23, 42, 0.88); }

            .tf-btn-mini {
                line-height: 1;
            }

            html.dark .tf-h { color: #e9eef5 !important; }
            html.dark .tf-sub { color: rgba(233,238,245,0.74) !important; }
            html.dark .tf-kicker { color: rgba(233,238,245,0.48) !important; }
            html.dark .tf-link { color: #9bd1e5 !important; }
            html.dark .tf-copy { color: #9df3c4 !important; }

            html.dark .tf-card {
                background: #121a22 !important;
                border-color: rgba(255,255,255,0.10) !important;
            }

            html.dark .tf-btn-mini {
                background: #0f141a !important;
                color: #e9eef5 !important;
                border-color: rgba(255,255,255,0.15) !important;
            }

            html.dark .tf-btn-mini:hover {
                background: #1a2430 !important;
            }

            html.dark .tf-badge {
                background: rgba(155,209,229,0.12) !important;
                color: rgba(233,238,245,0.82) !important;
            }

            html.dark .tf-badge-ok {
                background: rgba(87,167,115,0.14) !important;
                color: rgba(233,238,245,0.90) !important;
            }

            html.dark .tf-bar-bg { background: rgba(233,238,245,0.10) !important; }
            html.dark .tf-bar-todo { background: rgba(155,209,229,0.55) !important; }
            html.dark .tf-bar-prog { background: rgba(87,167,115,0.80) !important; }
            html.dark .tf-bar-done { background: rgba(233,238,245,0.88) !important; }

            html.dark .tf-pill { border-color: rgba(255,255,255,0.10) !important; }
            html.dark .tf-pill-danger {
                background: rgba(239,68,68,0.14) !important;
                color: rgba(233,238,245,0.90) !important;
            }
            html.dark .tf-pill-neutral {
                background: rgba(155,209,229,0.12) !important;
                color: rgba(233,238,245,0.80) !important;
            }

            html.dark .tf-grad { opacity: 0.22 !important; }
            html.dark .tf-shine { opacity: 0.08 !important; }
        </style>

    </x-projects.layout>
</x-app-layout>
