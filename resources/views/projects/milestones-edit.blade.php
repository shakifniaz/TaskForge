<x-app-layout>
    <x-projects.layout :project="$project" active="roadmap">
        <div class="max-w-3xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Milestone</h1>
                    <p class="text-sm text-gray-500">Update roadmap milestone details</p>
                </div>

                <a href="{{ route('projects.roadmap', $project) }}" class="text-sm underline text-gray-600">
                    Back to Roadmap
                </a>
            </div>

            @if ($errors->any())
                <div class="text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <form method="POST" action="{{ route('projects.roadmap.update', [$project, $milestone]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Title</label>
                        <input name="title" value="{{ old('title', $milestone->title) }}" required class="w-full rounded-md border-gray-300">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Description (optional)</label>
                        <textarea name="description" rows="4" class="w-full rounded-md border-gray-300">{{ old('description', $milestone->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Start date</label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', optional($milestone->start_date)->format('Y-m-d')) }}"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Target date</label>
                        <input type="date" name="target_date"
                               value="{{ old('target_date', optional($milestone->target_date)->format('Y-m-d')) }}"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300">
                            <option value="planned" {{ old('status',$milestone->status) === 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="in_progress" {{ old('status',$milestone->status) === 'in_progress' ? 'selected' : '' }}>In progress</option>
                            <option value="completed" {{ old('status',$milestone->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-projects.layout>
</x-app-layout>
