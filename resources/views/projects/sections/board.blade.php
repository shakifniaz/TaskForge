<x-app-layout>
    <x-projects.layout :project="$project" active="board">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tf-h">Board</h1>
                    <p class="text-sm tf-sub">Drag and drop tasks between columns</p>
                </div>

                <a href="{{ route('projects.tasks', $project) }}"
                   class="px-5 py-3 rounded-2xl text-white font-semibold
                          hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                    View Tasks →
                </a>
            </div>

            {{-- ===== Summary ===== --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-4 p-5">
                    <div>
                        <div class="text-xs tf-kicker">Pending</div>
                        <div id="pendingCount" class="text-3xl font-extrabold tf-h">{{ $pendingCount }}</div>
                        <div class="text-sm tf-sub">To do + In progress</div>
                    </div>

                    <div>
                        <div class="text-xs tf-kicker">Completed</div>
                        <div id="completedCount" class="text-3xl font-extrabold tf-h">{{ $completedCount }}</div>
                        <div class="text-sm tf-sub">Done</div>
                    </div>

                    <div class="max-h-28 overflow-y-auto pr-1">
                        <div class="text-xs tf-kicker mb-2">Tasks per member</div>
                        @forelse($tasksPerMember as $m)
                            <div class="flex justify-between text-sm tf-sub">
                                <span class="truncate">
                                    {{ $m['name'] }}
                                </span>
                                <span class="font-semibold tf-h">{{ $m['count'] }}</span>
                            </div>
                        @empty
                            <div class="text-sm tf-sub opacity-70">No members</div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- ===== Board ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                @php
                    $cols = [
                        'todo' => ['Pending','To do'],
                        'in_progress' => ['In progress','Working on it'],
                        'completed' => ['Completed','Done'],
                    ];
                @endphp

                @foreach($cols as $key => [$title,$subtitle])
                    <div class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card">
                        <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                        <div class="relative p-4 border-b border-black/10">
                            <div class="flex justify-between">
                                <div>
                                    <div class="font-bold tf-h">{{ $title }}</div>
                                    <div class="text-xs tf-sub">{{ $subtitle }}</div>
                                </div>
                                <span data-col-count="{{ $key }}" class="text-sm tf-sub">
                                    {{ $columns[$key]->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="kanban-col space-y-3 min-h-[260px]"
                                 data-column="{{ $key }}">
                                @foreach($columns[$key] as $t)
                                    <div class="kanban-card tf-hoveritem"
                                         data-task-id="{{ $t->id }}">
                                        <div class="font-semibold tf-h">{{ $t->title }}</div>

                                        <div class="mt-1 text-xs tf-sub">
                                            {{ $t->assignee?->name ?? 'Unassigned' }}
                                            @if($t->due_date)
                                                · Due {{ $t->due_date->format('Y-m-d') }}
                                            @endif
                                        </div>

                                        @if($t->priority)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs rounded-lg tf-pill">
                                                Priority: {{ ucfirst($t->priority) }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p id="boardStatus" class="text-sm tf-sub"></p>
        </div>

        {{-- Sortable --}}
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

        <script>
            const statusEl = document.getElementById('boardStatus');

            function setStatus(t){ statusEl.textContent = t || '' }

            function count(col){
                return col.querySelectorAll('[data-task-id]').length;
            }

            function updateCounts(){
                const t = document.querySelector('[data-column="todo"]');
                const p = document.querySelector('[data-column="in_progress"]');
                const d = document.querySelector('[data-column="completed"]');

                document.querySelector('[data-col-count="todo"]').textContent = count(t);
                document.querySelector('[data-col-count="in_progress"]').textContent = count(p);
                document.querySelector('[data-col-count="completed"]').textContent = count(d);

                document.getElementById('pendingCount').textContent = count(t)+count(p);
                document.getElementById('completedCount').textContent = count(d);
            }

            async function save(col){
                await fetch(`{{ route('projects.board.move',$project) }}`,{
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },
                    body:JSON.stringify({
                        column:col.dataset.column,
                        ordered_ids:[...col.children].map(e=>e.dataset.taskId)
                    })
                });
            }

            document.querySelectorAll('.kanban-col').forEach(col=>{
                new Sortable(col,{
                    group:'kanban',
                    animation:180,
                    ghostClass:'opacity-50',
                    draggable:'.kanban-card',
                    onEnd:async e=>{
                        updateCounts();
                        setStatus('Saving…');
                        await save(e.to);
                        if(e.from!==e.to) await save(e.from);
                        setStatus('Saved');
                        setTimeout(()=>setStatus(''),1200);
                    }
                });
            });

            updateCounts();
        </script>

        {{-- ===== Styles ===== --}}
        <style>
            .tf-h{color:#0f172a}
            .tf-sub{color:rgba(15,23,42,.7)}
            .tf-kicker{color:rgba(15,23,42,.55)}

            .tf-primary{background:#57A773}
            .tf-primary:hover{background:#4d9a69}

            .kanban-card{
                cursor:grab;
                padding:1rem;
                border-radius:1rem;
                border:1px solid rgba(0,0,0,.08);
                background:#f8fafc;
                transition:transform .15s, box-shadow .15s;
            }
            .kanban-card:active{cursor:grabbing}

            .tf-hoveritem:hover{
                transform:translateY(-2px);
                box-shadow:0 12px 24px rgba(0,0,0,.1);
            }

            .tf-grad{
                background:
                    radial-gradient(900px 240px at 14% 18%, rgba(87,167,115,.18), transparent 58%),
                    radial-gradient(760px 240px at 88% 14%, rgba(155,209,229,.22), transparent 58%);
            }

            /* ===== Dark Mode ===== */
            html.dark .tf-h{color:#e9eef5}
            html.dark .tf-sub,
            html.dark .tf-kicker{color:rgba(233,238,245,.7)}

            html.dark .tf-card{
                background:#121a22;
                border-color:rgba(255,255,255,.1);
            }

            html.dark .kanban-card{
                background:#1a2430;
                border-color:rgba(255,255,255,.12);
                color:#e9eef5;
            }

            html.dark .tf-grad{opacity:.25}
        </style>

    </x-projects.layout>
</x-app-layout>
