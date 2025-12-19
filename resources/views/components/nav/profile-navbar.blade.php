<div class="h-16 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">

        {{-- Left --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="h-9 w-9 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold">
                TF
            </div>
            <div class="min-w-0">
                <div class="text-xs text-gray-500">TaskForge</div>
                <div class="font-semibold text-gray-900 truncate">Profile</div>
            </div>
        </div>

        {{-- Right --}}
        <div class="flex items-center gap-2">

            {{-- Projects --}}
            <a href="{{ route('projects.index') }}"
               title="Projects"
               class="h-9 w-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition">
                <svg class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                </svg>
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        title="Logout"
                        class="h-9 w-9 flex items-center justify-center rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                    </svg>
                </button>
            </form>

        </div>
    </div>
</div>
