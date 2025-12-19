<x-app-layout>
    <x-projects.layout :project="$project" active="reports">

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Reports</h1>
                    <p class="text-sm text-gray-500">Insights over time (not just current status)</p>
                </div>

                {{-- Date range filter --}}
                <div class="flex gap-2">
                    @php
                        $btnBase = "px-4 py-2 rounded-xl border text-sm font-semibold transition";
                        $active = "bg-gray-900 text-white border-gray-900";
                        $idle = "bg-white border-gray-200 text-gray-800 hover:bg-gray-50";
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

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">On-time completion</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $onTimeRate }}%</div>
                    <div class="mt-1 text-sm text-gray-600">
                        {{ $onTimeCompleted }} on-time out of {{ $completedWithDue }} completed with due dates
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date range</div>
                    <div class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $from->format('Y-m-d') }} → {{ $to->format('Y-m-d') }}
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                        Filter: last {{ $days }} days
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Milestones</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $milestoneSummary->count() }}</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Progress tracked via linked tasks
                    </div>
                </div>
            </div>

            {{-- Completed tasks per week --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Completed tasks per week</h2>

                <div class="mt-4 space-y-2">
                    @forelse($completedPerWeek as $w)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="text-sm text-gray-700 font-medium">
                                Week: {{ $w['year_week'] }}
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $w['count'] }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No completed tasks in this range.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tasks per member --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Tasks per member</h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">Member</th>
                                <th class="py-2 pr-4">Assigned</th>
                                <th class="py-2 pr-4">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasksPerMember as $m)
                                <tr class="border-b last:border-0">
                                    <td class="py-3 pr-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $m['name'] }}
                                            @if($m['username'])
                                                <span class="text-gray-400 font-medium">
                                                    ({{ '@'.$m['username'] }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-800 font-semibold">{{ $m['assigned'] }}</td>
                                    <td class="py-3 pr-4 text-gray-800 font-semibold">{{ $m['completed'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-3 text-gray-500">No members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Milestone progress summary --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Milestone progress summary</h2>

                <div class="mt-4 space-y-3">
                    @forelse($milestoneSummary as $ms)
                        <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $ms['title'] }}</div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        @if($ms['due_date'])
                                            Due: <span class="font-semibold">{{ \Carbon\Carbon::parse($ms['due_date'])->format('Y-m-d') }}</span>
                                        @else
                                            <span class="text-gray-500">No due date</span>
                                        @endif
                                        · Tasks: <span class="font-semibold">{{ $ms['done'] }}/{{ $ms['total'] }}</span>
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $ms['pct'] }}%
                                </div>
                            </div>

                            <div class="mt-3 h-3 rounded-full bg-gray-200 overflow-hidden">
                                <div class="h-3 bg-gray-900" style="width: {{ $ms['pct'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No milestones yet. Create one in Roadmap.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </x-projects.layout>
</x-app-layout>
