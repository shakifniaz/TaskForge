<x-app-layout>
    <x-projects.layout :project="$project" active="manage">

        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Manage Project</h1>
                <p class="text-sm text-gray-500">Owner-only settings</p>
            </div>

            {{-- Status messages --}}
            @if(session('status') === 'project-renamed')
                <div class="p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm">
                    Project renamed successfully.
                </div>
            @endif

            @if(session('status') === 'github-updated')
                <div class="p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm">
                    GitHub repository link updated.
                </div>
            @endif

            @if(session('status') === 'github-removed')
                <div class="p-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm">
                    GitHub repository link removed.
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Rename project --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Rename project</h2>
                <p class="text-sm text-gray-500">Change the project name shown to members.</p>

                <form method="POST" action="{{ route('projects.manage.rename', $project) }}" class="mt-4 flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('PATCH')

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $project->name) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-gray-400 focus:ring-gray-300"
                        required
                    />

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                        Save
                    </button>
                </form>
            </div>

            {{-- GitHub repo link --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">GitHub repository</h2>
                <p class="text-sm text-gray-500">
                    Add or change the GitHub repo link (format: <span class="font-semibold">https://github.com/username/repo</span>)
                </p>

                <form method="POST" action="{{ route('projects.manage.github', $project) }}" class="mt-4 flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('PATCH')

                    <input
                        type="text"
                        name="github_repo"
                        value="{{ old('github_repo', $project->github_repo) }}"
                        placeholder="https://github.com/username/repo"
                        class="w-full rounded-xl border-gray-300 focus:border-gray-400 focus:ring-gray-300"
                        required
                    />

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                        {{ $project->github_repo ? 'Update' : 'Add' }}
                    </button>
                </form>

                @if($project->github_repo)
                    <div class="mt-3 flex items-center justify-between gap-4 flex-wrap">
                        <a href="{{ $project->github_repo }}" target="_blank" class="text-sm text-blue-700 underline break-all">
                            {{ $project->github_repo }}
                        </a>

                        <form method="POST" action="{{ route('projects.manage.github.remove', $project) }}"
                              onsubmit="return confirm('Remove GitHub repo link?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-semibold">
                                Remove link
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Delete project --}}
            <div class="bg-white border border-red-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-red-700">Delete project</h2>
                <p class="text-sm text-red-600">
                    This will permanently delete the project and everything inside it. This cannot be undone.
                </p>

                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      class="mt-4"
                      onsubmit="return confirm('Are you sure you want to delete this project? This cannot be undone.');">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                        Delete project
                    </button>
                </form>
            </div>
        </div>

    </x-projects.layout>
</x-app-layout>
