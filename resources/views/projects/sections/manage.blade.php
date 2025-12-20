<x-app-layout>
    <x-projects.layout :project="$project" active="manage">

        @if(auth()->id() !== $project->owner_id)

            {{-- ===== Non-owner message ===== --}}
            <div class="min-h-[40vh] flex items-center justify-center">
                <div class="text-center tf-card max-w-md">
                    <h1 class="text-xl font-semibold tf-h">
                        Restricted Access
                    </h1>
                    <p class="mt-2 text-sm tf-sub">
                        Only owners have access to this tab.
                    </p>
                </div>
            </div>

        @else

        {{-- ================= OWNER VIEW ================= --}}
        <div class="space-y-6">

            {{-- Header --}}
            <div>
                <h1 class="text-2xl font-semibold tf-h">Manage Project</h1>
                <p class="text-sm tf-sub">Owner-only settings</p>
            </div>

            {{-- Rename project --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <h2 class="text-lg font-semibold tf-h">Rename project</h2>
                <p class="text-sm tf-sub">Change the project name shown to members.</p>

                <form method="POST"
                      action="{{ route('projects.manage.rename', $project) }}"
                      class="mt-4 flex flex-col sm:flex-row gap-3 relative z-10">
                    @csrf
                    @method('PATCH')

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $project->name) }}"
                        class="tf-input"
                        required
                    />

                    <button type="submit" class="tf-primary-btn">
                        Save
                    </button>
                </form>
            </div>

            {{-- GitHub repo --}}
            <div class="tf-card tf-hovercard tf-gradient">
                <h2 class="text-lg font-semibold tf-h">GitHub repository</h2>
                <p class="text-sm tf-sub">
                    Format:
                    <span class="font-semibold">https://github.com/username/repo</span>
                </p>

                <form method="POST"
                      action="{{ route('projects.manage.github', $project) }}"
                      class="mt-4 flex flex-col sm:flex-row gap-3 relative z-10">
                    @csrf
                    @method('PATCH')

                    <input
                        type="text"
                        name="github_repo"
                        value="{{ old('github_repo', $project->github_repo) }}"
                        placeholder="https://github.com/username/repo"
                        class="tf-input"
                        required
                    />

                    <button type="submit" class="tf-primary-btn">
                        {{ $project->github_repo ? 'Update' : 'Add' }}
                    </button>
                </form>

                @if($project->github_repo)
                    <div class="mt-3 flex items-center justify-between gap-4 flex-wrap relative z-10">
                        <a href="{{ $project->github_repo }}"
                           target="_blank"
                           class="text-sm text-blue-600 underline break-all">
                            {{ $project->github_repo }}
                        </a>

                        <form method="POST"
                              action="{{ route('projects.manage.github.remove', $project) }}"
                              onsubmit="return confirm('Remove GitHub repo link?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="tf-danger-btn-sm">
                                Remove link
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Delete project --}}
            <div class="tf-card tf-hovercard tf-danger">
                <h2 class="text-lg font-semibold text-red-600">Delete project</h2>
                <p class="text-sm text-red-500">
                    This will permanently delete the project and everything inside it.
                </p>

                <form method="POST"
                      action="{{ route('projects.destroy', $project) }}"
                      class="mt-4 relative z-10"
                      onsubmit="return confirm('Are you sure? This cannot be undone.');">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="tf-danger-btn">
                        Delete project
                    </button>
                </form>
            </div>

        </div>
        {{-- ============== END OWNER VIEW ============== --}}

        @endif

        {{-- ===== Styles (LOCAL ONLY – safe) ===== --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }

            .tf-input {
                width:100%;
                padding:.65rem .9rem;
                border-radius:.75rem;
                border:1px solid rgba(0,0,0,.15);
                background:#fff;
            }

            html.dark .tf-input {
                background:#0f141a;
                color:#e9eef5;
                border-color:rgba(255,255,255,.2);
            }

            .tf-card {
                background:#fff;
                border:1px solid rgba(0,0,0,.1);
                border-radius:1.5rem;
                padding:1.5rem;
                position:relative;
                overflow:hidden;
            }

            .tf-gradient::before {
                content:'';
                position:absolute;
                inset:0;
                background:linear-gradient(120deg, rgba(87,167,115,.18), transparent 60%);
                opacity:0;
                transition:.3s;
                pointer-events:none;
            }

            .tf-hovercard:hover::before { opacity:1; }

            .tf-hovercard {
                transition:transform .25s, box-shadow .25s;
            }

            .tf-hovercard:hover {
                transform:translateY(-4px);
                box-shadow:0 18px 35px rgba(0,0,0,.15);
            }

            .tf-primary-btn {
                padding:.65rem 1.25rem;
                border-radius:.75rem;
                background:#57A773;
                color:#fff;
                font-weight:600;
                transition:.2s;
            }

            .tf-primary-btn:hover {
                background:#4d9a69;
                transform:translateY(-1px);
            }

            .tf-danger-btn {
                padding:.65rem 1.25rem;
                border-radius:.75rem;
                background:#dc2626;
                color:#fff;
                font-weight:600;
                transition:.2s;
            }

            .tf-danger-btn:hover {
                background:#b91c1c;
                transform:translateY(-1px);
            }

            .tf-danger-btn-sm {
                padding:.35rem .75rem;
                border-radius:.6rem;
                background:#fee2e2;
                border:1px solid #fecaca;
                color:#b91c1c;
                font-size:.75rem;
                font-weight:600;
                transition:.2s;
            }

            .tf-danger-btn-sm:hover {
                background:#fecaca;
            }

            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub { color:rgba(233,238,245,.7); }

            html.dark .tf-card {
                background:#121a22;
                border-color:rgba(255,255,255,.1);
            }
        </style>

    </x-projects.layout>
</x-app-layout>
