<x-app-layout>
    <x-projects.layout :project="$project" active="tasks">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Tasks</h1>
                    <p class="text-sm text-gray-500">Filter and view project tasks</p>
                </div>

                <a href="{{ route('projects.tasks.create', $project) }}"
                   class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                    + Create Task
                </a>
            </div>

            {{-- Filters --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                <form method="GET" action="{{ route('projects.tasks', $project) }}" class="flex flex-wrap gap-3 items-end">
                    <label class="text-sm text-gray-700">
                        <input type="checkbox" name="mine" value="1" class="rounded border-gray-300"
                               {{ request()->boolean('mine') ? 'checked' : '' }}>
                        Assigned to me
                    </label>

                    <label class="text-sm text-gray-700">
                        <input type="checkbox" name="due" value="soon" class="rounded border-gray-300"
                               {{ request('due') === 'soon' ? 'checked' : '' }}>
                        Due soon (7 days)
                    </label>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Status</label>
                        <select name="status" class="rounded-md border-gray-300">
                            <option value="">All</option>
                            <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To do</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Milestone</label>
                        <select name="milestone_id" class="rounded-md border-gray-300">
                            <option value="">All</option>
                            @foreach($milestones as $ms)
                                <option value="{{ $ms->id }}" {{ (string)request('milestone_id') === (string)$ms->id ? 'selected' : '' }}>
                                    {{ $ms->title }}
                                </option>
                            @endforeach
                            <option value="0" {{ request('milestone_id') === '0' ? 'selected' : '' }}>No milestone</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition text-sm font-semibold">
                        Apply
                    </button>

                    <a href="{{ route('projects.tasks', $project) }}" class="text-sm underline text-gray-600">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Task list --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Task list</h2>
                    <span class="text-sm text-gray-500">{{ $tasks->count() }} tasks</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($tasks as $t)
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $t->title }}</div>

                                    <div class="mt-1 text-sm text-gray-600">
                                        Status: <span class="font-medium">{{ str_replace('_',' ', $t->status) }}</span>
                                        · Priority: <span class="font-medium">{{ $t->priority }}</span>

                                        @if($t->milestone)
                                            · Milestone: <span class="font-medium">{{ $t->milestone->title }}</span>
                                        @endif

                                        @if($t->due_date)
                                            · Due: <span class="font-medium">{{ $t->due_date->format('Y-m-d') }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-1 text-sm text-gray-600">
                                        Assigned to:
                                        <span class="font-medium">{{ $t->assignee?->name ?? 'Unassigned' }}</span>
                                    </div>

                                    @if($t->description)
                                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ $t->description }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('projects.tasks.edit', [$project, $t]) }}"
                                       class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $t]) }}"
                                          onsubmit="return confirm('Delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-red-50 text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No tasks yet. Click “Create Task” to add one.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-projects.layout>
</x-app-layout>
