<x-app-layout>
    <x-projects.layout :project="$project" active="roadmap">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tf-h">Roadmap</h1>
                    <p class="text-sm tf-sub">Milestones and long-term planning</p>
                </div>

                <a href="{{ route('projects.roadmap.create', $project) }}"
                   class="px-5 py-3 rounded-2xl text-white font-semibold
                          hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                    + Create Milestone
                </a>
            </div>

            {{-- ===== Timeline View ===== --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">

                <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-bold tf-h">Timeline</h2>
                        <span class="text-sm tf-sub">{{ $milestones->count() }} milestones</span>
                    </div>

                    <div class="space-y-8">
                        @forelse($timeline as $month => $items)
                            @php
                                $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');
                            @endphp

                            <div>
                                <div class="text-xs font-semibold tracking-wider uppercase tf-kicker">
                                    {{ $monthLabel }}
                                </div>

                                <div class="mt-4 space-y-4 border-l-2 border-black/10 pl-5">
                                    @foreach($items as $m)
                                        @php
                                            $statusBadge = match($m->status) {
                                                'planned' => 'tf-badge-neutral',
                                                'in_progress' => 'tf-badge-warn',
                                                'completed' => 'tf-badge-ok',
                                                default => 'tf-badge-neutral',
                                            };
                                        @endphp

                                        <div class="relative">
                                            <div class="absolute -left-[9px] top-4 h-4 w-4 rounded-full tf-dot"></div>

                                            <div class="rounded-2xl border border-black/10 bg-white p-5 tf-item tf-hoveritem">
                                                <div class="flex flex-col sm:flex-row sm:justify-between gap-4">

                                                    <div class="min-w-0">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <div class="font-semibold tf-h truncate">
                                                                {{ $m->title }}
                                                            </div>

                                                            <span class="text-xs px-2 py-1 rounded-xl font-semibold {{ $statusBadge }}">
                                                                {{ str_replace('_',' ', $m->status) }}
                                                            </span>
                                                        </div>

                                                        <div class="mt-1 text-xs tf-sub">
                                                            @if($m->start_date)
                                                                Start: {{ $m->start_date->format('Y-m-d') }}
                                                            @endif
                                                            @if($m->start_date && $m->target_date) · @endif
                                                            @if($m->target_date)
                                                                Target: {{ $m->target_date->format('Y-m-d') }}
                                                            @endif
                                                        </div>

                                                        @if($m->description)
                                                            <p class="mt-2 text-sm tf-desc whitespace-pre-wrap">{{ trim($m->description) }}</p>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <a href="{{ route('projects.roadmap.edit', [$project, $m]) }}"
                                                           class="px-4 py-2 rounded-xl border border-black/10 bg-white
                                                                  hover:bg-black/5 transition text-sm font-semibold tf-action-btn">
                                                            Edit
                                                        </a>

                                                        <form method="POST"
                                                              action="{{ route('projects.roadmap.destroy', [$project, $m]) }}"
                                                              onsubmit="return confirm('Delete this milestone?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-4 py-2 rounded-xl border border-black/10 bg-white
                                                                           transition text-sm font-semibold tf-delete-btn">
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
                            <p class="text-sm tf-sub opacity-70">
                                No milestones yet. Click “Create Milestone” to start planning.
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- ===== All Milestones ===== --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                <div class="relative p-6">
                    <h2 class="text-lg font-bold tf-h mb-4">All milestones</h2>

                    <div class="space-y-3">
                        @forelse($milestones as $m)
                            <div class="p-5 rounded-2xl border border-black/10 bg-white tf-item tf-hoveritem">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold tf-h truncate">{{ $m->title }}</div>
                                        <div class="mt-1 text-sm tf-sub">
                                            Status:
                                            <span class="font-semibold">{{ str_replace('_',' ', $m->status) }}</span>
                                            @if($m->target_date)
                                                · Target:
                                                <span class="font-semibold">{{ $m->target_date->format('Y-m-d') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ route('projects.roadmap.edit', [$project, $m]) }}"
                                       class="px-4 py-2 rounded-xl border border-black/10 bg-white
                                              hover:bg-black/5 transition text-sm font-semibold tf-action-btn">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm tf-sub opacity-70">No milestones yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        {{-- ===== Styles ===== --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { color:rgba(15,23,42,.5); }
            .tf-desc { color:rgba(15,23,42,.85); }

            .tf-primary { background:#57A773; }
            .tf-primary:hover { background:#4d9a69; }

            .tf-dot { background:#0f172a; }

            .tf-grad {
                background:
                    radial-gradient(900px 240px at 14% 18%, rgba(87,167,115,0.18), transparent 58%),
                    radial-gradient(760px 240px at 88% 14%, rgba(155,209,229,0.22), transparent 58%),
                    radial-gradient(900px 300px at 52% 120%, rgba(106,142,174,0.14), transparent 62%);
            }

            .tf-hovercard {
                transition: transform .25s ease, box-shadow .25s ease;
            }
            .tf-hovercard:hover {
                transform: translateY(-4px);
                box-shadow:0 18px 40px rgba(0,0,0,.10);
            }

            .tf-hoveritem {
                transition: transform .15s ease, box-shadow .15s ease;
            }
            .tf-hoveritem:hover {
                transform: translateY(-2px);
                box-shadow:0 12px 26px rgba(0,0,0,.10);
            }

            .tf-badge-neutral { background:rgba(106,142,174,.15); color:#1f2937; }
            .tf-badge-warn { background:rgba(234,179,8,.18); color:#92400e; }
            .tf-badge-ok { background:rgba(34,197,94,.18); color:#14532d; }

            .tf-delete-btn:hover {
                background:#dc2626;
                color:#fff;
                border-color:#dc2626;
            }

            /* ===== Dark Mode ===== */
            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub,
            html.dark .tf-kicker { color:rgba(233,238,245,.7); }
            html.dark .tf-desc { color:rgba(233,238,245,.85); }

            html.dark .tf-card,
            html.dark .tf-item {
                background:#121a22;
                border-color:rgba(255,255,255,.08);
            }

            html.dark .tf-dot { background:#e9eef5; }

            html.dark .tf-badge-neutral,
            html.dark .tf-badge-warn,
            html.dark .tf-badge-ok {
                color:#e9eef5;
            }

            html.dark .tf-delete-btn:hover {
                background:#ef4444;
                color:#fff;
            }

            html.dark .tf-action-btn:hover {
                color:#ffffff;
            }

            html.dark .tf-grad { opacity:.22; }
        </style>

    </x-projects.layout>
</x-app-layout>
