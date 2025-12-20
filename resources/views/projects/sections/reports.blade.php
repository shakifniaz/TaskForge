<x-app-layout>
    <x-projects.layout :project="$project" active="reports">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tf-h">Reports</h1>
                    <p class="text-sm tf-sub">Insights over time (not just current status)</p>
                </div>

                {{-- Date range filter --}}
                <div class="flex gap-2">
                    @php
                        $btnBase = "tf-btn";
                        $active = "tf-btn-active";
                        $idle = "tf-btn-idle";
                    @endphp

                    <a href="{{ route('projects.reports', $project) }}?days=7"
                       class="{{ $btnBase }} {{ $days === 7 ? $active : $idle }}">
                        Last 7 days
                    </a>

                    <a href="{{ route('projects.reports', $project) }}?days=30"
                       class="{{ $btnBase }} {{ $days === 30 ? $active : $idle }}">
                        Last 30 days
                    </a>

                    <a href="{{ route('projects.reports', $project) }}?days=90"
                       class="{{ $btnBase }} {{ $days === 90 ? $active : $idle }}">
                        Last 90 days
                    </a>
                </div>
            </div>

            {{-- ===== Summary cards ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="tf-card tf-hovercard tf-gradient">
                    <div class="tf-kicker">On-time completion</div>
                    <div class="mt-2 text-3xl font-bold tf-h">{{ $onTimeRate }}%</div>
                    <div class="mt-1 text-sm tf-sub">
                        {{ $onTimeCompleted }} on-time out of {{ $completedWithDue }}
                    </div>
                </div>

                <div class="tf-card tf-hovercard tf-gradient">
                    <div class="tf-kicker">Date range</div>
                    <div class="mt-2 text-lg font-semibold tf-h">
                        {{ $from->format('Y-m-d') }} → {{ $to->format('Y-m-d') }}
                    </div>
                    <div class="mt-1 text-sm tf-sub">Last {{ $days }} days</div>
                </div>

                <div class="tf-card tf-hovercard tf-gradient">
                    <div class="tf-kicker">Milestones</div>
                    <div class="mt-2 text-3xl font-bold tf-h">{{ $milestoneSummary->count() }}</div>
                    <div class="mt-1 text-sm tf-sub">Progress tracked via tasks</div>
                </div>
            </div>

            {{-- ===== Completed tasks per week ===== --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <h2 class="text-lg font-semibold tf-h">Completed tasks per week</h2>

                <div class="mt-4 space-y-2">
                    @forelse($completedPerWeek as $w)
                        <div class="tf-row tf-hoverrow">
                            <div class="text-sm font-medium tf-sub">Week {{ $w['year_week'] }}</div>
                            <div class="font-semibold tf-h">{{ $w['count'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm tf-sub">No completed tasks.</p>
                    @endforelse
                </div>
            </div>

            {{-- ===== Tasks per member ===== --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <h2 class="text-lg font-semibold tf-h">Tasks per member</h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="tf-table-head">
                                <th class="tf-sub">Member</th>
                                <th class="tf-sub">Assigned</th>
                                <th class="tf-sub">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasksPerMember as $m)
                                <tr class="tf-table-row">
                                    <td>
                                        <div class="font-semibold tf-h">
                                            {{ $m['name'] }}
                                            @if($m['username'])
                                                <span class="tf-sub">
                                                    ({{ '@'.$m['username'] }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="font-semibold tf-h">{{ $m['assigned'] }}</td>
                                    <td class="font-semibold tf-h">{{ $m['completed'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="tf-sub">No members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== Milestone summary ===== --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <h2 class="text-lg font-semibold tf-h">Milestone progress</h2>

                <div class="mt-4 space-y-3">
                    @forelse($milestoneSummary as $ms)
                        <div class="tf-card-inner tf-hoverrow">
                            <div class="flex justify-between">
                                <div>
                                    <div class="font-semibold tf-h">{{ $ms['title'] }}</div>
                                    <div class="text-sm tf-sub">
                                        Tasks {{ $ms['done'] }}/{{ $ms['total'] }}
                                    </div>
                                </div>
                                <div class="font-semibold tf-h">{{ $ms['pct'] }}%</div>
                            </div>

                            <div class="mt-3 h-3 rounded-full tf-progress-bg">
                                <div class="tf-progress-bar" style="width: {{ $ms['pct'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="tf-sub">No milestones yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ===== Styles (unchanged) ===== --}}
        <style>
            /* Base */
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; color:rgba(15,23,42,.5); }

            /* Buttons */
            .tf-btn {
                padding:.6rem 1.25rem;
                border-radius:1rem;
                font-weight:600;
                transition:.2s;
                border:1px solid rgba(0,0,0,.12);
            }
            .tf-btn-idle { background:#fff; }
            .tf-btn-active { background:#57A773; color:#fff; border-color:#57A773; }

            /* Cards */
            .tf-card {
                background:#fff;
                border:1px solid rgba(0,0,0,.1);
                border-radius:1.5rem;
                padding:1.5rem;
                position:relative;
                overflow:hidden;
            }

            .tf-gradient::before {
                content:'';
                position:absolute;
                inset:0;
                background:linear-gradient(120deg, rgba(87,167,115,.12), transparent 60%);
                opacity:0;
                transition:.3s;
            }

            .tf-hovercard:hover::before { opacity:1; }

            .tf-hovercard {
                transition:transform .25s, box-shadow .25s;
            }
            .tf-hovercard:hover {
                transform:translateY(-4px);
                box-shadow:0 20px 40px rgba(0,0,0,.12);
            }

            .tf-card-inner {
                padding:1rem;
                border-radius:1rem;
                background:rgba(0,0,0,.03);
            }

            .tf-row {
                display:flex;
                justify-content:space-between;
                padding:.75rem 1rem;
                border-radius:.75rem;
                background:rgba(0,0,0,.03);
            }

            .tf-hoverrow {
                transition:.2s;
            }
            .tf-hoverrow:hover {
                background:rgba(87,167,115,.12);
            }

            /* Table */
            .tf-table-head th {
                text-align:left;
                padding:.75rem;
                font-size:.75rem;
                text-transform:uppercase;
            }
            .tf-table-row td {
                padding:.75rem;
                border-top:1px solid rgba(0,0,0,.08);
            }
            .tf-table-row:hover {
                background:rgba(87,167,115,.08);
            }

            /* Progress */
            .tf-progress-bg {
                background:rgba(0,0,0,.1);
                overflow:hidden;
            }
            .tf-progress-bar {
                height:100%;
                background:#57A773;
                transition:width .6s ease;
            }

            /* ===== DARK MODE ===== */
            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub,
            html.dark .tf-kicker { color:rgba(233,238,245,.7); }

            html.dark .tf-card {
                background:#121a22;
                border-color:rgba(255,255,255,.08);
            }

            html.dark .tf-btn-idle {
                background:#121a22;
                color:#fff;
                border-color:rgba(255,255,255,.15);
            }

            html.dark .tf-row,
            html.dark .tf-card-inner {
                background:rgba(255,255,255,.04);
            }

            html.dark .tf-progress-bg {
                background:rgba(255,255,255,.15);
            }
        </style>

    </x-projects.layout>
</x-app-layout>
