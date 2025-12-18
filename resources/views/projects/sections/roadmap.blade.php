<x-app-layout>
    <x-projects.layout :project="$project" active="roadmap">
        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Roadmap</h1>
                    <p class="text-sm text-gray-500">Milestones and long-term planning</p>
                </div>

                <a href="{{ route('projects.roadmap.create', $project) }}"
                   class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                    + Create Milestone
                </a>
            </div>

            {{-- Timeline View --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Timeline</h2>
                    <span class="text-sm text-gray-500">{{ $milestones->count() }} milestones</span>
                </div>

                <div class="mt-5 space-y-6">
                    @forelse($timeline as $month => $items)
                        @php
                            $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');
                        @endphp

                        <div>
                            <div class="text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                {{ $monthLabel }}
                            </div>

                            <div class="mt-3 space-y-3 border-l-2 border-gray-200 pl-4">
                                @foreach($items as $m)
                                    @php
                                        $statusBadge = match($m->status) {
                                            'planned' => 'bg-gray-100 text-gray-700',
                                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <div class="relative">
                                        <div class="absolute -left-[9px] top-2 h-4 w-4 rounded-full bg-gray-900"></div>

                                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $m->title }}</div>
                                                        <span class="text-xs px-2 py-1 rounded-lg {{ $statusBadge }}">
                                                            {{ str_replace('_',' ', $m->status) }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-1 text-xs text-gray-500">
                                                        @if($m->start_date) Start: {{ $m->start_date->format('Y-m-d') }} @endif
                                                        @if($m->start_date && $m->target_date) · @endif
                                                        @if($m->target_date) Target: {{ $m->target_date->format('Y-m-d') }} @endif
                                                    </div>

                                                    @if($m->description)
                                                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ $m->description }}</p>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2 shrink-0">
                                                    <a href="{{ route('projects.roadmap.edit', [$project, $m]) }}"
                                                       class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">
                                                        Edit
                                                    </a>

                                                    <form method="POST" action="{{ route('projects.roadmap.destroy', [$project, $m]) }}"
                                                          onsubmit="return confirm('Delete this milestone?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-red-50 text-sm">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No milestones yet. Click “Create Milestone” to start planning.</p>
                    @endforelse
                </div>
            </div>

            {{-- Simple List View --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">All milestones</h2>

                <div class="mt-4 space-y-3">
                    @forelse($milestones as $m)
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $m->title }}</div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Status: <span class="font-medium">{{ str_replace('_',' ', $m->status) }}</span>
                                        @if($m->target_date)
                                            · Target: <span class="font-medium">{{ $m->target_date->format('Y-m-d') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('projects.roadmap.edit', [$project, $m]) }}"
                                       class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No milestones yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-projects.layout>
</x-app-layout>
