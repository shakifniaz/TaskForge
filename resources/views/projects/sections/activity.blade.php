<x-app-layout>
    <x-projects.layout :project="$project" active="activity">
        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Activity</h1>
                    <p class="text-sm text-gray-500">Project updates + GitHub commits</p>
                </div>
            </div>

            {{-- Connect GitHub --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">GitHub Repository</h2>
                        <p class="text-sm text-gray-500">
                            Connect a repository to show commits by branch.
                            For private repos, add a token (fine-grained, read-only).
                        </p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="text-sm text-red-600">{{ $errors->first() }}</div>
                @endif

                @if(session('status') === 'github-connected')
                    <div class="text-sm text-green-700">GitHub connected.</div>
                @endif
                @if(session('status') === 'github-disconnected')
                    <div class="text-sm text-gray-700">GitHub disconnected.</div>
                @endif

                <form method="POST" action="{{ route('projects.activity.connect', $project) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf

                    <div class="md:col-span-1">
                        <label class="block text-sm text-gray-700 mb-1">Repo (owner/repo or URL)</label>
                        <input name="github_repo"
                               value="{{ old('github_repo', $project->github_repo) }}"
                               placeholder="octocat/Hello-World"
                               class="w-full rounded-md border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Leave empty + save to disconnect.</p>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm text-gray-700 mb-1">Default branch (optional)</label>
                        <input name="github_default_branch"
                               value="{{ old('github_default_branch', $project->github_default_branch) }}"
                               placeholder="main"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm text-gray-700 mb-1">Token (optional)</label>
                        <input name="github_token"
                               value=""
                               placeholder="(Only needed for private repos)"
                               class="w-full rounded-md border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">We don’t display stored tokens.</p>
                    </div>

                    <div class="md:col-span-3">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                            Save Connection
                        </button>
                    </div>
                </form>
            </div>

            {{-- Commits --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Commits</h2>
                        <p class="text-sm text-gray-500">Select a branch to view latest commits</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-700">Branch:</label>
                        <select id="branchSelect" class="rounded-md border-gray-300">
                            <option value="">—</option>
                            @foreach($branches as $b)
                                <option value="{{ $b['name'] }}" {{ ($selectedBranch === $b['name']) ? 'selected' : '' }}>
                                    {{ $b['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <button id="refreshBtn"
                                class="px-3 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                            Refresh
                        </button>
                    </div>
                </div>

                @if($error)
                    <div class="mt-4 text-sm text-red-600">
                        GitHub error: {{ $error }}
                    </div>
                @endif

                <div id="commitsWrap" class="mt-5 space-y-3">
                    @if(!$project->github_repo)
                        <p class="text-sm text-gray-500">Connect a GitHub repository above to show commits.</p>
                    @elseif(empty($commits))
                        <p class="text-sm text-gray-500">No commits to show (select a branch).</p>
                    @else
                        @foreach($commits as $c)
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900 truncate">
                                            {{ $c['message'] }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ $c['author'] }} ·
                                            {{ $c['date'] ? \Carbon\Carbon::parse($c['date'])->format('Y-m-d H:i') : '' }}
                                            · <span class="font-mono">{{ $c['sha'] }}</span>
                                        </div>
                                    </div>
                                    @if($c['url'])
                                        <a href="{{ $c['url'] }}" target="_blank"
                                           class="text-sm underline text-gray-700 shrink-0">
                                            Open →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <p id="commitStatus" class="mt-4 text-sm text-gray-500"></p>
            </div>
        </div>

        <script>
            const branchSelect = document.getElementById('branchSelect');
            const refreshBtn = document.getElementById('refreshBtn');
            const commitsWrap = document.getElementById('commitsWrap');
            const commitStatus = document.getElementById('commitStatus');

            function setStatus(msg) {
                commitStatus.textContent = msg || '';
            }

            function escapeHtml(str) {
                return (str || '').replace(/[&<>"']/g, (m) => ({
                    "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
                }[m]));
            }

            function renderCommits(commits) {
                commitsWrap.innerHTML = '';
                if (!commits.length) {
                    commitsWrap.innerHTML = `<p class="text-sm text-gray-500">No commits found for this branch.</p>`;
                    return;
                }

                commits.forEach(c => {
                    const date = c.date ? new Date(c.date).toLocaleString() : '';
                    const url = c.url ? `<a href="${c.url}" target="_blank" class="text-sm underline text-gray-700 shrink-0">Open →</a>` : '';

                    const card = document.createElement('div');
                    card.className = 'p-4 rounded-xl border border-gray-200 bg-gray-50';
                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">${escapeHtml(c.message)}</div>
                                <div class="mt-1 text-sm text-gray-600">
                                    ${escapeHtml(c.author)} · ${escapeHtml(date)} · <span class="font-mono">${escapeHtml(c.sha)}</span>
                                </div>
                            </div>
                            ${url}
                        </div>
                    `;
                    commitsWrap.appendChild(card);
                });
            }

            async function fetchCommits() {
                const branch = branchSelect.value;
                if (!branch) {
                    commitsWrap.innerHTML = `<p class="text-sm text-gray-500">Select a branch to view commits.</p>`;
                    return;
                }

                setStatus('Loading commits…');

                try {
                    const url = `{{ route('projects.activity.commits', $project) }}?branch=${encodeURIComponent(branch)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) {
                        setStatus('Failed to load commits.');
                        return;
                    }

                    const data = await res.json();
                    renderCommits(Array.isArray(data) ? data : []);
                    setStatus('');
                } catch (e) {
                    setStatus('Failed to load commits.');
                }
            }

            refreshBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                fetchCommits();
            });

            branchSelect?.addEventListener('change', () => {
                fetchCommits();
            });
        </script>
    </x-projects.layout>
</x-app-layout>
