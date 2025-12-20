<x-app-layout>
    <x-projects.layout :project="$project" active="tasks">

        {{-- ===== PAGE WRAPPER ===== --}}
        <div class="flex justify-center">
            <div class="w-full max-w-3xl space-y-6">

                {{-- ===== Header ===== --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tf-h">Create Task</h1>
                        <p class="text-sm tf-sub">Add a new task to this project</p>
                    </div>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- ===== CARD ===== --}}
                <div class="rounded-3xl border border-black/10 bg-white shadow-sm p-6 tf-card tf-hovercard">

                    <form method="POST"
                          action="{{ route('projects.tasks.store', $project) }}"
                          class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf

                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs tf-kicker mb-1">Title</label>
                            <input name="title"
                                   value="{{ old('title') }}"
                                   required
                                   placeholder="Task title"
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs tf-kicker mb-1">Description (optional)</label>
                            <textarea name="description"
                                      rows="4"
                                      placeholder="Describe the task…"
                                      class="w-full rounded-xl border-black/10 tf-input resize-none">{{ old('description') }}</textarea>
                        </div>

                        {{-- Assign --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Assign to</label>
                            <select name="assigned_to" class="w-full rounded-xl border-black/10 tf-input">
                                <option value="">Unassigned</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}"
                                        {{ old('assigned_to') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }}{{ $m->username ? ' (@'.$m->username.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Milestone --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Milestone</label>
                            <select name="milestone_id" class="w-full rounded-xl border-black/10 tf-input">
                                <option value="">No milestone</option>
                                @foreach($milestones as $ms)
                                    <option value="{{ $ms->id }}"
                                        {{ (string)old('milestone_id') === (string)$ms->id ? 'selected' : '' }}>
                                        {{ $ms->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Due Date --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Due date</label>
                            <input type="date"
                                   name="due_date"
                                   value="{{ old('due_date') }}"
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Status</label>
                            <select name="status" class="w-full rounded-xl border-black/10 tf-input">
                                <option value="todo" {{ old('status','todo') === 'todo' ? 'selected' : '' }}>To do</option>
                                <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Priority</label>
                            <select name="priority" class="w-full rounded-xl border-black/10 tf-input">
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority','medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>

                        {{-- Actions --}}
                        <div class="md:col-span-2 flex gap-3 pt-4">
                            {{-- Create --}}
                            <button type="submit"
                                    class="flex-1 px-5 py-3 rounded-2xl text-white font-semibold
                                           hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                                Create Task
                            </button>

                            {{-- Cancel --}}
                            <a href="{{ route('projects.tasks', $project) }}"
                               class="flex-1 text-center px-5 py-3 rounded-2xl font-semibold
                                      border border-black/10 bg-white text-black
                                      hover:-translate-y-0.5 hover:shadow transition tf-cancel">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ===== STYLES ===== --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { color:rgba(15,23,42,.5); }

            .tf-primary { background:#57A773; }
            .tf-primary:hover { background:#4d9a69; }

            .tf-input {
                padding:.65rem .75rem;
                background:#fff;
            }

            .tf-hovercard {
                transition: transform .2s ease, box-shadow .2s ease;
            }
            .tf-hovercard:hover {
                transform: translateY(-3px);
                box-shadow:0 18px 40px rgba(0,0,0,.12);
            }

            /* ===== Dark Mode ===== */
            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub,
            html.dark .tf-kicker { color:rgba(233,238,245,.7); }

            html.dark .tf-card {
                background:#121a22;
                border-color:rgba(255,255,255,.08);
            }

            html.dark .tf-input {
                background:#0f141a;
                color:#e9eef5;
                border-color:rgba(255,255,255,.12);
            }

            html.dark .tf-cancel {
                background:#0f141a;
                color:#ffffff;
                border-color:rgba(255,255,255,.15);
            }
        </style>

    </x-projects.layout>
</x-app-layout>
