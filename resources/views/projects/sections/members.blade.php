<x-app-layout>
    <x-projects.layout :project="$project" active="members">

        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Members</h1>
                    <p class="text-sm text-gray-500">View members and manage invitations</p>
                </div>

                @if($isOwner)
                    <span class="text-xs px-3 py-1 rounded-full bg-green-50 border border-green-200 text-green-800 font-semibold">
                        You are the owner
                    </span>
                @endif
            </div>

            {{-- Flash messages --}}
            @if (session('status') === 'member-removed')
                <div class="p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm">
                    Member removed successfully.
                </div>
            @endif

            @if (session('status') === 'member-added')
                <div class="p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm">
                    Member added successfully.
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Owner-only: Add new members --}}
            @if($isOwner)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Add new members</h2>
                            <p class="text-sm text-gray-500">Search by name or username, then add to this project.</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input
                                id="userSearchInput"
                                type="text"
                                class="w-full rounded-xl border-gray-300 focus:border-gray-400 focus:ring-gray-300"
                                placeholder="Search users… (e.g. Shake or @shake)"
                                autocomplete="off"
                            />
                            <button
                                id="userSearchBtn"
                                type="button"
                                class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                                Search
                            </button>
                        </div>

                        <p id="searchHint" class="text-xs text-gray-500">
                            Tip: type at least 2 characters to search.
                        </p>

                        <div id="searchResults" class="mt-2 space-y-2"></div>
                    </div>
                </div>
            @endif

            {{-- Members cards --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Project members</h2>
                    <span class="text-sm text-gray-500">{{ $members->count() }} members</span>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($members as $m)
                        @php
                            $isProjectOwner = (int)$m->id === (int)$project->owner_id;
                            $displayHandle = $m->username ? '@'.$m->username : null;
                            $photoUrl = $m->profile_photo_path ? asset('storage/'.$m->profile_photo_path) : null;
                        @endphp

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="shrink-0">
                                    @if($photoUrl)
                                        <img
                                            src="{{ $photoUrl }}"
                                            alt="Profile photo"
                                            class="h-12 w-12 rounded-xl object-cover border border-gray-200"
                                        />
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-700 font-semibold">
                                            {{ strtoupper(mb_substr($m->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 truncate">
                                                {{ $m->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 truncate">
                                                {{ $displayHandle ?? '—' }}
                                            </div>
                                        </div>

                                        @if($isProjectOwner)
                                            <span class="text-xs px-2 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 font-semibold">
                                                Owner
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 text-sm text-gray-600 truncate">
                                        {{ $m->email }}
                                    </div>

                                    @if(!empty($m->description))
                                        <div class="mt-2 text-sm text-gray-700">
                                            {{ $m->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Owner-only actions --}}
                            @if($isOwner && !$isProjectOwner)
                                <div class="mt-4 flex justify-end">
                                    <form method="POST"
                                          action="{{ route('projects.members.remove', [$project, $m]) }}"
                                          onsubmit="return confirm('Remove this member from the project?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-semibold">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No members found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Owner-only JS for searching + adding users --}}
        @if($isOwner)
            <script>
                const input = document.getElementById('userSearchInput');
                const btn = document.getElementById('userSearchBtn');
                const resultsEl = document.getElementById('searchResults');

                function escapeHtml(str) {
                    return String(str ?? '').replace(/[&<>"']/g, (m) => ({
                        "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
                    }[m]));
                }

                function renderResults(users) {
                    resultsEl.innerHTML = '';

                    if (!Array.isArray(users) || users.length === 0) {
                        resultsEl.innerHTML = `<div class="text-sm text-gray-500">No users found.</div>`;
                        return;
                    }

                    users.forEach(u => {
                        const name = escapeHtml(u.name);
                        const username = u.username ? `@${escapeHtml(u.username)}` : '';
                        const email = escapeHtml(u.email ?? '');

                        const card = document.createElement('div');
                        card.className = 'p-4 rounded-xl border border-gray-200 bg-white flex items-center justify-between gap-4';

                        card.innerHTML = `
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">${name}</div>
                                <div class="text-sm text-gray-500 truncate">${username} ${email ? '· ' + email : ''}</div>
                            </div>
                            <form method="POST" action="{{ route('projects.users.add', $project) }}">
                                @csrf
                                <input type="hidden" name="user_id" value="${u.id}">
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition text-sm">
                                    Add
                                </button>
                            </form>
                        `;

                        resultsEl.appendChild(card);
                    });
                }

                async function searchUsers() {
                    const q = (input.value || '').trim();
                    resultsEl.innerHTML = '';

                    if (q.length < 2) {
                        resultsEl.innerHTML = `<div class="text-sm text-gray-500">Type at least 2 characters.</div>`;
                        return;
                    }

                    try {
                        const url = `{{ route('projects.users.search', $project) }}?q=${encodeURIComponent(q)}`;
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) {
                            resultsEl.innerHTML = `<div class="text-sm text-red-600">Search failed.</div>`;
                            return;
                        }

                        const data = await res.json();
                        renderResults(data);
                    } catch (e) {
                        resultsEl.innerHTML = `<div class="text-sm text-red-600">Search error.</div>`;
                    }
                }

                btn.addEventListener('click', searchUsers);
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchUsers();
                    }
                });
            </script>
        @endif

    </x-projects.layout>
</x-app-layout>
