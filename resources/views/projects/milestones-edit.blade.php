<x-app-layout>
    <x-projects.layout :project="$project" active="roadmap">

        {{-- ===== PAGE WRAPPER ===== --}}
        <div class="flex justify-center">
            <div class="w-full max-w-3xl space-y-6">

                {{-- ===== Header ===== --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tf-h">Edit Milestone</h1>
                        <p class="text-sm tf-sub">Update roadmap milestone details</p>
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
                          action="{{ route('projects.roadmap.update', [$project, $milestone]) }}"
                          class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf
                        @method('PATCH')

                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs tf-kicker mb-1">Title</label>
                            <input name="title"
                                   value="{{ old('title', $milestone->title) }}"
                                   required
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs tf-kicker mb-1">Description (optional)</label>
                            <textarea name="description"
                                      rows="4"
                                      class="w-full rounded-xl border-black/10 tf-input resize-none">{{ trim(old('description', $milestone->description)) }}</textarea>
                        </div>

                        {{-- Start date --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Start date</label>
                            <input type="date"
                                   name="start_date"
                                   value="{{ old('start_date', optional($milestone->start_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        {{-- Target date --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Target date</label>
                            <input type="date"
                                   name="target_date"
                                   value="{{ old('target_date', optional($milestone->target_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border-black/10 tf-input">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs tf-kicker mb-1">Status</label>
                            <select name="status" class="w-full rounded-xl border-black/10 tf-input">
                                <option value="planned" {{ old('status', $milestone->status) === 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ old('status', $milestone->status) === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option value="completed" {{ old('status', $milestone->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        {{-- Actions --}}
                        <div class="md:col-span-2 flex gap-3 pt-4">
                            {{-- Save --}}
                            <button type="submit"
                                    class="flex-1 px-5 py-3 rounded-2xl text-white font-semibold
                                           hover:-translate-y-0.5 hover:shadow-lg transition tf-primary">
                                Save changes
                            </button>

                            {{-- Cancel --}}
                            <a href="{{ route('projects.roadmap', $project) }}"
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
