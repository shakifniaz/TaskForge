<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $project->name }}
            </h2>

            <a href="{{ route('projects.index') }}"
               class="text-sm underline text-gray-600 dark:text-gray-300">
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Members</h3>

                <ul class="mt-3 space-y-2">
                    @foreach ($members as $m)
                        <li class="text-sm text-gray-700 dark:text-gray-200">
                            {{ $m->name }}
                            @if ($m->username) <span class="text-gray-500">({{ $m->username }})</span> @endif
                            — <span class="text-gray-500">{{ $m->email }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Invite users</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Search by name / username / email, then add them to this project.
                </p>

                <div class="mt-4">
                    <input id="userSearch"
                           type="text"
                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                           placeholder="Type at least 2 characters…">

                    <div id="results" class="mt-3 space-y-2"></div>
                </div>

                <form id="addForm" method="POST" action="{{ route('projects.users.add', $project) }}" class="hidden">
                    @csrf
                    <input type="hidden" name="user_id" id="selectedUserId">
                </form>
            </div>

        </div>
    </div>

    <script>
        const input = document.getElementById('userSearch');
        const results = document.getElementById('results');
        const addForm = document.getElementById('addForm');
        const selectedUserId = document.getElementById('selectedUserId');

        let t = null;

        function render(users) {
            results.innerHTML = '';

            if (!users.length) {
                results.innerHTML = `<p class="text-sm text-gray-500">No users found.</p>`;
                return;
            }

            users.forEach(u => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition';
                btn.innerHTML = `
                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                        ${u.name} ${u.username ? `<span class="text-gray-500">(@${u.username})</span>` : ''}
                    </div>
                    <div class="text-sm text-gray-500">${u.email}</div>
                    <div class="mt-2 text-sm underline text-gray-600 dark:text-gray-300">Add to project</div>
                `;
                btn.addEventListener('click', () => {
                    selectedUserId.value = u.id;
                    addForm.submit();
                });

                results.appendChild(btn);
            });
        }

        input.addEventListener('input', () => {
            clearTimeout(t);
            const q = input.value.trim();

            if (q.length < 2) {
                results.innerHTML = '';
                return;
            }

            t = setTimeout(async () => {
                const url = `{{ route('projects.users.search', $project) }}?q=${encodeURIComponent(q)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                render(data);
            }, 250);
        });
    </script>
</x-app-layout>
