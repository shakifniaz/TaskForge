<x-app-layout>
    <x-projects.layout :project="$project" active="board">
        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Board</h1>
                    <p class="text-sm text-gray-500">Drag and drop tasks between columns</p>
                </div>
                <a href="{{ route('projects.tasks', $project) }}"
                   class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                    View Tasks →
                </a>
            </div>

            {{-- Summary row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending tasks</div>
                    <div id="pendingCount" class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingCount }}</div>
                    <div class="mt-1 text-sm text-gray-500">To do + In progress</div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed tasks</div>
                    <div id="completedCount" class="mt-2 text-3xl font-bold text-gray-900">{{ $completedCount }}</div>
                    <div class="mt-1 text-sm text-gray-500">Done</div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tasks per member</div>
                    <div class="mt-3 space-y-2 max-h-24 overflow-auto pr-1">
                        @forelse($tasksPerMember as $m)
                            <div class="flex items-center justify-between text-sm">
                                <div class="truncate text-gray-700">
                                    {{ $m['name'] }}
                                    @if($m['username']) <span class="text-gray-400">(@{{ $m['username'] }})</span> @endif
                                </div>
                                <div class="font-semibold text-gray-900">{{ $m['count'] }}</div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No members.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Kanban columns --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                @php
                    $colMeta = [
                        'todo' => ['title' => 'Pending', 'subtitle' => 'To do'],
                        'in_progress' => ['title' => 'In Progress', 'subtitle' => 'Working on it'],
                        'completed' => ['title' => 'Completed', 'subtitle' => 'Done'],
                    ];
                @endphp

                @foreach(['todo','in_progress','completed'] as $col)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $colMeta[$col]['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $colMeta[$col]['subtitle'] }}</div>
                                </div>
                                <div class="text-sm text-gray-500" data-col-count="{{ $col }}">
                                    {{ $columns[$col]->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="kanban-col space-y-3 min-h-[250px]"
                                 data-column="{{ $col }}">
                                @foreach($columns[$col] as $t)
                                    <div class="kanban-card p-4 rounded-xl border border-gray-200 bg-gray-50 cursor-grab"
                                         data-task-id="{{ $t->id }}">
                                        <div class="font-semibold text-gray-900">{{ $t->title }}</div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            @if($t->assignee)
                                                Assigned: {{ $t->assignee->name }}
                                            @else
                                                Unassigned
                                            @endif
                                            @if($t->due_date)
                                                · Due: {{ $t->due_date->format('Y-m-d') }}
                                            @endif
                                        </div>

                                        @if($t->priority)
                                            <div class="mt-2 text-xs">
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg border border-gray-200 bg-white text-gray-700">
                                                    Priority: {{ ucfirst($t->priority) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p id="boardStatus" class="text-sm text-gray-500"></p>
        </div>

        {{-- SortableJS (drag & drop) --}}
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

        <script>
            const statusEl = document.getElementById('boardStatus');
            const pendingCountEl = document.getElementById('pendingCount');
            const completedCountEl = document.getElementById('completedCount');

            function setStatus(msg) {
                statusEl.textContent = msg || '';
            }

            // ✅ Update counts instantly (no reload)
            function updateCountsFromDOM() {
                const todoCol = document.querySelector('.kanban-col[data-column="todo"]');
                const progCol = document.querySelector('.kanban-col[data-column="in_progress"]');
                const doneCol = document.querySelector('.kanban-col[data-column="completed"]');

                const todoCount = todoCol ? todoCol.querySelectorAll('[data-task-id]').length : 0;
                const progCount = progCol ? progCol.querySelectorAll('[data-task-id]').length : 0;
                const doneCount = doneCol ? doneCol.querySelectorAll('[data-task-id]').length : 0;

                // Column header counts
                const todoHdr = document.querySelector('[data-col-count="todo"]');
                const progHdr = document.querySelector('[data-col-count="in_progress"]');
                const doneHdr = document.querySelector('[data-col-count="completed"]');

                if (todoHdr) todoHdr.textContent = todoCount;
                if (progHdr) progHdr.textContent = progCount;
                if (doneHdr) doneHdr.textContent = doneCount;

                // Summary counts
                if (pendingCountEl) pendingCountEl.textContent = (todoCount + progCount);
                if (completedCountEl) completedCountEl.textContent = doneCount;
            }

            // ✅ Queue saves to avoid race conditions
            const saveQueue = new WeakMap();

            function getOrderedIds(columnEl) {
                return Array.from(columnEl.querySelectorAll('[data-task-id]'))
                    .map(el => parseInt(el.dataset.taskId, 10))
                    .filter(n => !Number.isNaN(n));
            }

            async function postMove(column, orderedIds) {
                const res = await fetch(`{{ route('projects.board.move', $project) }}`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        column,
                        ordered_ids: orderedIds
                    })
                });

                if (!res.ok) {
                    let serverMsg = '';
                    try {
                        const j = await res.json();
                        serverMsg = j?.message ? ` — ${j.message}` : '';
                    } catch (_) {}
                    throw new Error(`HTTP ${res.status}${serverMsg}`);
                }

                return true;
            }

            function saveColumnOrder(columnEl) {
                const column = columnEl.dataset.column;
                const orderedIds = getOrderedIds(columnEl);

                const current = saveQueue.get(columnEl) || Promise.resolve();

                const next = current
                    .catch(() => {}) // keep queue alive
                    .then(async () => {
                        setStatus('Saving…');
                        await postMove(column, orderedIds);
                        setStatus('Saved.');
                        setTimeout(() => setStatus(''), 1200);
                    })
                    .catch((err) => {
                        setStatus(`Failed to save (${err.message}).`);
                    });

                saveQueue.set(columnEl, next);
                return next;
            }

            // Initialize counts once on load
            updateCountsFromDOM();

            document.querySelectorAll('.kanban-col').forEach(col => {
                new Sortable(col, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: async (evt) => {
                        // ✅ Update counts immediately after drop (instant UI)
                        updateCountsFromDOM();

                        // Save destination ordering
                        await saveColumnOrder(evt.to);

                        // Save source ordering if moved across columns
                        if (evt.from !== evt.to) {
                            await saveColumnOrder(evt.from);
                        }
                    }
                });
            });
        </script>
    </x-projects.layout>
</x-app-layout>
