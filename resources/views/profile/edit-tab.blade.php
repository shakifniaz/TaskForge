<x-app-layout>
    <div class="min-h-screen tf-page" style="background-color:#EFEFEF;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

            {{-- Header --}}
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                {{-- Logo + Title --}}
                <div class="flex items-center gap-4">
                    <img
                        src="{{ asset('assets/logos/TaskForgeGreen.svg') }}"
                        alt="TaskForge"
                        class="h-16 w-16"
                    />

                    <div>
                        <h1 class="text-3xl font-bold tracking-tight tf-title" style="color:#157145;">
                            Edit Profile
                        </h1>
                        <p class="mt-1 text-sm tf-subtitle text-gray-600">
                            Update your details, password, and account settings.
                        </p>
                    </div>
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-3">
                    {{-- Back to Profile --}}
                    <a href="{{ route('dashboard') }}"
                       title="Back to Profile"
                       class="h-14 px-5 inline-flex items-center justify-center rounded-2xl
                              border border-black/10 bg-white
                              hover:-translate-y-0.5 hover:shadow-lg transition tf-theme-btn">
                        <span class="text-sm font-semibold tf-h text-gray-900">Back</span>
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                title="Logout"
                                class="h-14 w-14 inline-flex items-center justify-center rounded-2xl
                                       bg-gray-900 text-white
                                       hover:bg-gray-800 hover:-translate-y-0.5 hover:shadow-lg transition tf-logout-btn">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Main content --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Left: Update Profile Info (flashy card like create-project) --}}
                <div class="lg:col-span-7">
                    <div class="relative overflow-hidden rounded-3xl border border-black/10 shadow-sm bg-white tf-card">
                        {{-- Decorative gradient --}}
                        <div class="absolute inset-0 opacity-60 pointer-events-none tf-grad"
                             style="background:
                                radial-gradient(800px 200px at 15% 20%, rgba(87,167,115,0.22), transparent 55%),
                                radial-gradient(700px 220px at 85% 10%, rgba(155,209,229,0.28), transparent 55%),
                                radial-gradient(800px 260px at 50% 110%, rgba(106,142,174,0.18), transparent 60%);">
                        </div>

                        {{-- Subtle animated shine --}}
                        <div class="absolute -top-24 -left-24 h-64 w-64 rotate-12 opacity-20 pointer-events-none
                                    animate-pulse tf-shine"
                             style="background: linear-gradient(135deg, rgba(209,250,255,0.0), rgba(209,250,255,1), rgba(209,250,255,0.0));">
                        </div>

                        <div class="relative p-6 sm:p-8">
                            <h2 class="text-xl font-bold tf-h text-gray-900">Profile Information</h2>
                            <p class="mt-1 text-sm tf-sub text-gray-600">
                                Update your name, username, email, and profile details.
                            </p>

                            <div class="mt-6 tf-form">
                                @include('profile.partials.update-profile-information-form', ['user' => $user])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Password + Delete --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Update Password --}}
                    <div class="rounded-3xl border border-black/10 bg-white p-6 sm:p-8 shadow-sm tf-card">
                        <h2 class="text-lg font-bold tf-h text-gray-900">Change Password</h2>
                        <p class="mt-1 text-sm tf-sub text-gray-600">
                            Use a strong password you don’t reuse elsewhere.
                        </p>

                        <div class="mt-6 tf-form">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Delete Account --}}
                    <div class="rounded-3xl border border-black/10 bg-white p-6 sm:p-8 shadow-sm tf-card">
                        <h2 class="text-lg font-bold tf-h text-gray-900">Delete Account</h2>
                        <p class="mt-1 text-sm tf-sub text-gray-600">
                            This action is permanent. Please proceed carefully.
                        </p>

                        <div class="mt-6 tf-form">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Dark mode styling (matches index.blade.php behavior) --}}
    <style>
        /* Dark theme uses class on <html> */
        html.dark body { background: #0b0f14; }

        /* Override page background in dark mode */
        html.dark .tf-page { background-color: #0f141a !important; }

        /* Typography */
        html.dark .tf-title,
        html.dark .tf-h { color: #e9eef5 !important; }
        html.dark .tf-subtitle,
        html.dark .tf-sub { color: rgba(233, 238, 245, 0.70) !important; }

        /* Cards */
        html.dark .tf-card {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.08) !important;
        }

        /* Buttons */
        html.dark .tf-theme-btn {
            background: #121a22 !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #e9eef5 !important;
        }
        html.dark .tf-logout-btn { background: #0b0f14 !important; }

        /* Decorative gradient/shines toned down in dark */
        html.dark .tf-grad { opacity: 0.25 !important; }
        html.dark .tf-shine { opacity: 0.10 !important; }

        /*
          Make included Breeze/Jetstream form fields look correct in dark mode
          without editing the partials.
        */
        html.dark .tf-form input,
        html.dark .tf-form textarea,
        html.dark .tf-form select {
            background: rgba(15, 20, 26, 0.85) !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #e9eef5 !important;
        }
        html.dark .tf-form input::placeholder,
        html.dark .tf-form textarea::placeholder {
            color: rgba(233, 238, 245, 0.45) !important;
        }
        html.dark .tf-form label,
        html.dark .tf-form p,
        html.dark .tf-form span,
        html.dark .tf-form h3 {
            color: rgba(233, 238, 245, 0.80) !important;
        }
    </style>

    {{-- Apply theme from localStorage (no toggle needed here) --}}
    <script>
        (function initThemeFromStorage() {
            const saved = localStorage.getItem('tf_theme'); // 'dark' | 'light'
            const isDark = saved === 'dark';
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>
</x-app-layout>
