<x-app-layout>
    <x-projects.layout :project="$project" active="tasks">
        <div class="max-w-3xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Task</h1>
                    <p class="text-sm text-gray-500">Update task details</p>
                </div>

                <a href="{{ route('projects.tasks', $project) }}" class="text-sm underline text-gray-600">
                    Back to Tasks
                </a>
            </div>

            @if ($errors->any())
                <div class="text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Title</label>
                        <input name="title" value="{{ old('title', $task->title) }}" required
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 mb-1">Description (optional)</label>
                        <textarea name="description" rows="4"
                                  class="w-full rounded-md border-gray-300">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Assign to (optional)</label>
                        <select name="assigned_to" class="w-full rounded-md border-gray-300">
                            <option value="">Unassigned</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" {{ (string)old('assigned_to', $task->assigned_to) === (string)$m->id ? 'selected' : '' }}>
                                    {{ $m->name }}{{ $m->username ? ' (@'.$m->username.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ✅ Milestone dropdown --}}
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Milestone (optional)</label>
                        <select name="milestone_id" class="w-full rounded-md border-gray-300">
                            <option value="">No milestone</option>
                            @foreach($milestones as $ms)
                                <option value="{{ $ms->id }}"
                                    {{ (string)old('milestone_id', $task->milestone_id) === (string)$ms->id ? 'selected' : '' }}>
                                    {{ $ms->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Due date (optional)</label>
                        <input type="date" name="due_date"
                               value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300">
                            <option value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>To do</option>
                            <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In progress</option>
                            <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full rounded-md border-gray-300">
                            <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
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
