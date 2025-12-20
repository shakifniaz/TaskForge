<x-app-layout>
    <x-projects.layout :project="$project" active="tasks">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tf-h">Tasks</h1>
                    <p class="text-sm tf-sub">Manage and track project work</p>
                </div>

                <a href="{{ route('projects.tasks.create', $project) }}"
                   class="px-5 py-3 rounded-2xl text-white font-semibold
                          hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                    + Create Task
                </a>
            </div>

            {{-- ===== Filters ===== --}}
            <div class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm p-5 tf-card tf-hovercard">

                {{-- Gradient --}}
                <div class="absolute inset-0 pointer-events-none tf-grad"
                     style="background:
                        radial-gradient(700px 200px at 12% 15%, rgba(87,167,115,0.16), transparent 60%),
                        radial-gradient(600px 200px at 90% 20%, rgba(155,209,229,0.18), transparent 60%);">
                </div>

                <form method="GET"
                      action="{{ route('projects.tasks', $project) }}"
                      class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start">

                    {{-- Assigned to me --}}
                    <div class="flex flex-col">
                        <div class="h-5"></div>
                        <label class="tf-check-btn">
                            <input type="checkbox"
                                   name="mine"
                                   value="1"
                                   class="rounded border-black/30"
                                   {{ request()->boolean('mine') ? 'checked' : '' }}>
                            Assigned to me
                        </label>
                    </div>

                    {{-- Due soon --}}
                    <div class="flex flex-col">
                        <div class="h-5"></div>
                        <label class="tf-check-btn">
                            <input type="checkbox"
                                   name="due"
                                   value="soon"
                                   class="rounded border-black/30"
                                   {{ request('due') === 'soon' ? 'checked' : '' }}>
                            Due in 7 days
                        </label>
                    </div>

                    {{-- Status --}}
                    <div class="flex flex-col">
                        <label class="block text-xs tf-kicker mb-1">Status</label>
                        <select name="status" class="h-11 w-full rounded-xl border-black/10 tf-input">
                            <option value="">All</option>
                            <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To do</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    {{-- Milestone --}}
                    <div class="flex flex-col">
                        <label class="block text-xs tf-kicker mb-1">Milestone</label>
                        <select name="milestone_id" class="h-11 w-full rounded-xl border-black/10 tf-input">
                            <option value="">All</option>
                            @foreach($milestones as $ms)
                                <option value="{{ $ms->id }}"
                                    {{ (string)request('milestone_id') === (string)$ms->id ? 'selected' : '' }}>
                                    {{ $ms->title }}
                                </option>
                            @endforeach
                            <option value="0" {{ request('milestone_id') === '0' ? 'selected' : '' }}>
                                No milestone
                            </option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 lg:col-span-4 pt-4">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl border border-black/10 bg-white
                                       hover:bg-black/5 transition font-semibold text-sm tf-action-btn">
                            Apply filters
                        </button>

                        <a href="{{ route('projects.tasks', $project) }}"
                           class="px-5 py-2.5 rounded-xl border border-black/10 bg-white
                                  hover:bg-black/5 transition font-semibold text-sm tf-action-btn">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ===== Task List ===== --}}
            <div class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm p-6 tf-card tf-hovercard">

                {{-- Gradient --}}
                <div class="absolute inset-0 pointer-events-none tf-grad"
                     style="background:
                        radial-gradient(900px 260px at 14% 20%, rgba(87,167,115,0.14), transparent 60%),
                        radial-gradient(800px 260px at 88% 16%, rgba(155,209,229,0.18), transparent 60%);">
                </div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold tf-h">Task List</h2>
                        <span class="text-sm tf-sub">{{ $tasks->count() }} tasks</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($tasks as $t)
                            <div class="rounded-2xl border border-black/10 bg-white p-5 tf-item tf-hoveritem">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                                    <div class="min-w-0 space-y-1">
                                        <div class="font-semibold tf-h truncate">{{ $t->title }}</div>

                                        <div class="text-sm tf-sub">
                                            Status: <span class="font-semibold">{{ str_replace('_',' ', $t->status) }}</span>
                                            · Priority: <span class="font-semibold">{{ $t->priority }}</span>
                                            @if($t->milestone)
                                                · Milestone: <span class="font-semibold">{{ $t->milestone->title }}</span>
                                            @endif
                                            @if($t->due_date)
                                                · Due: <span class="font-semibold">{{ $t->due_date->format('Y-m-d') }}</span>
                                            @endif
                                        </div>

                                        <div class="text-sm tf-sub">
                                            Assigned to:
                                            <span class="font-semibold">{{ $t->assignee?->name ?? 'Unassigned' }}</span>
                                        </div>

                                        @if($t->description)
                                            <p class="mt-2 text-sm tf-desc whitespace-pre-wrap">{{ trim($t->description) }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('projects.tasks.edit', [$project, $t]) }}"
                                           class="px-4 py-2 rounded-xl border border-black/10 bg-white
                                                  hover:bg-black/5 transition text-sm font-semibold tf-action-btn">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('projects.tasks.destroy', [$project, $t]) }}"
                                              onsubmit="return confirm('Delete this task?');">
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
                        @empty
                            <p class="text-sm tf-sub opacity-70">
                                No tasks yet. Click “Create Task” to add one.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Styles ===== --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { color:rgba(15,23,42,.5); }
            .tf-desc { color:rgba(15,23,42,.85); }

            .tf-primary { background:#57A773; }
            .tf-primary:hover { background:#4d9a69; }

            .tf-input { padding:.6rem .75rem; background:#fff; }

            .tf-hovercard {
                transition: transform .2s ease, box-shadow .2s ease;
            }
            .tf-hovercard:hover {
                transform: translateY(-3px);
                box-shadow:0 16px 36px rgba(0,0,0,.10);
            }

            .tf-hoveritem {
                transition: transform .15s ease, box-shadow .15s ease;
            }
            .tf-hoveritem:hover {
                transform: translateY(-2px);
                box-shadow:0 12px 26px rgba(0,0,0,.10);
            }

            .tf-check-btn {
                display:flex;
                align-items:center;
                gap:.75rem;
                height:2.75rem;
                padding:0 1rem;
                border-radius:.75rem;
                border:1px solid rgba(0,0,0,.1);
                background:#fff;
                cursor:pointer;
                transition:background .15s ease;
            }
            .tf-check-btn:hover { background:rgba(0,0,0,.05); }

            .tf-delete-btn {
                transition: background .15s ease, color .15s ease;
            }
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

            html.dark .tf-input {
                background:#0f141a;
                color:#e9eef5;
                border-color:rgba(255,255,255,.12);
            }

            html.dark .tf-check-btn {
                background:#0f141a;
                border-color:rgba(255,255,255,.12);
                color:#e9eef5;
            }

            html.dark .tf-check-btn:hover { background:#1a2430; }
            html.dark .tf-delete-btn:hover { background:#ef4444; }
            html.dark .tf-action-btn:hover { color:#fff; }
            html.dark .tf-grad { opacity:.22; }
        </style>

    </x-projects.layout>
</x-app-layout>
