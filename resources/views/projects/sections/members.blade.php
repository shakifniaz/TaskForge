<x-app-layout>
    <x-projects.layout :project="$project" active="members">

        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tf-h">Members</h1>
                    <p class="text-sm tf-sub">View members and manage invitations</p>
                </div>
            </div>

            {{-- Owner-only: Add members --}}
            @if($isOwner)
                <div class="tf-card tf-hovercard tf-gradient">
                    <div>
                        <h2 class="text-lg font-semibold tf-h">Add new members</h2>
                        <p class="text-sm tf-sub">Search by name or username</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input
                                id="userSearchInput"
                                type="text"
                                class="tf-input w-full"
                                placeholder="Type a name or @username…"
                                autocomplete="off"
                            />
                            <button
                                id="userSearchBtn"
                                type="button"
                                class="tf-btn tf-btn-primary">
                                Search
                            </button>
                        </div>

                        {{-- Dynamic results (expands card) --}}
                        <div id="searchResults" class="space-y-2"></div>
                    </div>
                </div>
            @endif

            {{-- Members --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold tf-h">Project members</h2>
                    <span class="text-sm tf-sub">{{ $members->count() }} members</span>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($members as $m)
                        @php
                            $isProjectOwner = (int)$m->id === (int)$project->owner_id;
                            $handle = $m->username ? '@'.$m->username : null;
                        @endphp

                        <div class="tf-card-inner tf-hoverrow">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <div class="font-semibold tf-h truncate">
                                            {{ $m->name }}
                                        </div>

                                        @if($isProjectOwner)
                                            <span class="tf-owner-badge">Owner</span>
                                        @endif
                                    </div>

                                    <div class="text-sm tf-sub truncate">
                                        {{ $handle ?? '—' }}
                                    </div>

                                    <div class="mt-1 text-sm tf-sub truncate">
                                        {{ $m->email }}
                                    </div>
                                </div>
                            </div>

                            @if($isOwner && !$isProjectOwner)
                                <div class="mt-4 flex justify-end">
                                    <form method="POST"
                                          action="{{ route('projects.members.remove', [$project, $m]) }}"
                                          onsubmit="return confirm('Remove this member?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="tf-btn tf-btn-danger">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm tf-sub">No members found.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- JS --}}
        @if($isOwner)
            <script>
                const input = document.getElementById('userSearchInput');
                const btn = document.getElementById('userSearchBtn');
                const results = document.getElementById('searchResults');

                function esc(s) {
                    return String(s ?? '').replace(/[&<>"']/g, m => ({
                        "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
                    }[m]));
                }

                function render(users) {
                    results.innerHTML = '';
                    if (!users.length) {
                        results.innerHTML = `<div class="text-sm tf-sub">No users found.</div>`;
                        return;
                    }

                    users.forEach(u => {
                        const card = document.createElement('div');
                        card.className = 'tf-search-row';

                        card.innerHTML = `
                            <div class="min-w-0">
                                <div class="font-semibold tf-h truncate">${esc(u.name)}</div>
                                <div class="text-sm tf-sub truncate">
                                    ${u.username ? '@'+esc(u.username) : ''}
                                </div>
                            </div>

                            <form method="POST" action="{{ route('projects.users.add', $project) }}">
                                @csrf
                                <input type="hidden" name="user_id" value="${u.id}">
                                <button class="tf-btn tf-btn-primary text-sm">
                                    Add
                                </button>
                            </form>
                        `;

                        results.appendChild(card);
                    });
                }

                async function search() {
                    const q = input.value.trim();
                    results.innerHTML = '';

                    if (q.length < 2) return;

                    try {
                        const res = await fetch(
                            `{{ route('projects.users.search', $project) }}?q=${encodeURIComponent(q)}`
                        );
                        if (!res.ok) return;
                        render(await res.json());
                    } catch {}
                }

                btn.onclick = search;
                input.oninput = search;
            </script>
        @endif

        {{-- Styles --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }

            .tf-card {
                background:#fff;
                border:1px solid rgba(0,0,0,.1);
                border-radius:1.5rem;
                padding:1.5rem;
                position:relative;
            }

            .tf-gradient::before {
                content:'';
                position:absolute;
                inset:0;
                background:linear-gradient(120deg, rgba(87,167,115,.15), transparent 60%);
                opacity:0;
                transition:.3s;
                pointer-events:none;
            }

            .tf-hovercard:hover::before { opacity:1; }

            .tf-hovercard {
                transition:.25s;
            }
            .tf-hovercard:hover {
                transform:translateY(-4px);
                box-shadow:0 20px 40px rgba(0,0,0,.12);
            }

            .tf-card-inner {
                padding:1.25rem;
                border-radius:1rem;
                background:rgba(0,0,0,.03);
            }

            .tf-hoverrow:hover {
                background:rgba(87,167,115,.12);
            }

            .tf-input {
                padding:.7rem .9rem;
                border-radius:.75rem;
                border:1px solid rgba(0,0,0,.15);
            }

            .tf-btn {
                padding:.55rem 1.1rem;
                border-radius:.75rem;
                font-weight:600;
                transition:.2s;
            }

            .tf-btn-primary {
                background:#57A773;
                color:#fff;
            }
            .tf-btn-primary:hover {
                transform:translateY(-2px);
                box-shadow:0 8px 20px rgba(87,167,115,.35);
            }

            .tf-btn-danger {
                background:#fee2e2;
                color:#b91c1c;
                border:1px solid #fecaca;
            }

            .tf-owner-badge {
                font-size:.65rem;
                padding:.15rem .45rem;
                border-radius:.5rem;
                background:#dcfce7;
                color:#166534;
                font-weight:600;
            }

            .tf-search-row {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:1rem;
                padding:.75rem 1rem;
                border-radius:.75rem;
                background:rgba(0,0,0,.03);
            }

            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub { color:rgba(233,238,245,.7); }
            html.dark .tf-card { background:#121a22; border-color:rgba(255,255,255,.08); }
            html.dark .tf-card-inner,
            html.dark .tf-search-row { background:rgba(255,255,255,.05); }
            html.dark .tf-input { background:#0f141a; color:#fff; border-color:rgba(255,255,255,.2); }
        </style>

    </x-projects.layout>
</x-app-layout>
