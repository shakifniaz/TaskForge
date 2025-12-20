<x-app-layout>
    <x-projects.layout :project="$project" active="activity">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tf-h">Activity</h1>
                    <p class="text-sm tf-sub">Project updates & GitHub commits</p>
                </div>
            </div>

            {{-- ===== GitHub Connection ===== --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                <div class="relative p-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold tf-h">GitHub Repository</h2>
                        <p class="text-sm tf-sub">
                            Connect a repository to show commits by branch.
                            For private repos, add a read-only token.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if(session('status') === 'github-connected')
                        <div class="text-sm text-green-700 font-semibold">GitHub connected.</div>
                    @endif
                    @if(session('status') === 'github-disconnected')
                        <div class="text-sm tf-sub">GitHub disconnected.</div>
                    @endif

                    <form method="POST"
                          action="{{ route('projects.activity.connect', $project) }}"
                          class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf

                        <div>
                            <label class="block text-xs tf-kicker mb-1">Repository</label>
                            <input name="github_repo"
                                   value="{{ old('github_repo', $project->github_repo) }}"
                                   placeholder="owner/repo"
                                   class="w-full rounded-xl border-black/10 tf-input">
                            <p class="mt-1 text-xs tf-sub">Leave empty to disconnect.</p>
                        </div>

                        <div>
                            <label class="block text-xs tf-kicker mb-1">Default branch</label>
                            <input name="github_default_branch"
                                   value="{{ old('github_default_branch', $project->github_default_branch) }}"
                                   placeholder="main"
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        <div>
                            <label class="block text-xs tf-kicker mb-1">Token (optional)</label>
                            <input name="github_token"
                                   placeholder="Private repos only"
                                   class="w-full rounded-xl border-black/10 tf-input">
                            <p class="mt-1 text-xs tf-sub">Stored securely, never displayed.</p>
                        </div>

                        <div class="md:col-span-3 pt-2">
                            <button type="submit"
                                    class="px-5 py-3 rounded-2xl text-white font-semibold
                                           hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                                Save connection
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- ===== Commits ===== --}}
            <section class="relative overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm tf-card tf-grad-card tf-hovercard">
                <div class="absolute inset-0 pointer-events-none tf-grad"></div>

                <div class="relative p-6 space-y-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold tf-h">Commits</h2>
                            <p class="text-sm tf-sub">Select a branch to view activity</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="text-sm tf-sub">Branch</label>
                            <select id="branchSelect"
                                    class="rounded-xl border-black/10 tf-input">
                                <option value="">—</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b['name'] }}"
                                        {{ ($selectedBranch === $b['name']) ? 'selected' : '' }}>
                                        {{ $b['name'] }}
                                    </option>
                                @endforeach
                            </select>

                            <button id="refreshBtn"
                                    class="px-4 py-2 rounded-xl border border-black/10 bg-white
                                           hover:bg-black/5 transition text-sm font-semibold tf-action-btn">
                                Refresh
                            </button>
                        </div>
                    </div>

                    @if($error)
                        <div class="text-sm text-red-600">GitHub error: {{ $error }}</div>
                    @endif

                    <div id="commitsWrap" class="space-y-3">
                        @if(!$project->github_repo)
                            <p class="text-sm tf-sub">Connect a repository above to see commits.</p>
                        @elseif(empty($commits))
                            <p class="text-sm tf-sub">No commits to show.</p>
                        @else
                            @foreach($commits as $c)
                                <div class="p-4 rounded-2xl border border-black/10 bg-white tf-item tf-hoveritem">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="font-semibold tf-h truncate">
                                                {{ $c['message'] }}
                                            </div>
                                            <div class="mt-1 text-sm tf-sub">
                                                {{ $c['author'] }}
                                                · {{ $c['date'] ? \Carbon\Carbon::parse($c['date'])->format('Y-m-d H:i') : '' }}
                                                · <span class="font-mono">{{ $c['sha'] }}</span>
                                            </div>
                                        </div>
                                        @if($c['url'])
                                            <a href="{{ $c['url'] }}" target="_blank"
                                               class="text-sm underline tf-link shrink-0">
                                                Open →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <p id="commitStatus" class="text-sm tf-sub"></p>
                </div>
            </section>
        </div>

        {{-- ===== Styles ===== --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { color:rgba(15,23,42,.5); }
            .tf-link { color:#1d4ed8; }

            .tf-primary { background:#57A773; }
            .tf-primary:hover { background:#4d9a69; }

            .tf-input {
                padding:.6rem .75rem;
                background:#fff;
            }

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

            /* ===== Dark Mode ===== */
            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub,
            html.dark .tf-kicker { color:rgba(233,238,245,.7); }
            html.dark .tf-link { color:#9bd1e5; }

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

            html.dark .tf-action-btn:hover {
                color:#ffffff;
            }

            html.dark .tf-grad { opacity:.22; }
        </style>

    </x-projects.layout>
</x-app-layout>
