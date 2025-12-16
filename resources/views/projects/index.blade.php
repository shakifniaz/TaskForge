<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Projects') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create a new project</h3>

                <form method="POST" action="{{ route('projects.store') }}" class="mt-4 flex gap-3">
                    @csrf
                    <input
                        type="text"
                        name="name"
                        placeholder="Project name"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        value="{{ old('name') }}"
                        required
                    >
                    <x-primary-button>Create</x-primary-button>
                </form>

                @if ($errors->any())
                    <div class="mt-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your projects</h3>

                <div class="mt-4 space-y-3">
                    @forelse ($projects as $project)
                        <a href="{{ route('projects.overview', $project) }}"
                           class="block p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $project->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Owner: {{ $project->owner?->name }}
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Open →</span>
                            </div>
                        </a>
                    @empty
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            You’re not in any projects yet. Create one above.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
