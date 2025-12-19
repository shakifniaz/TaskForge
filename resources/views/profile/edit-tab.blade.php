<x-app-layout>
    <x-nav.profile-navbar />

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Profile</h1>
                    <p class="text-sm text-gray-500">Update your details, password, or delete your account</p>
                </div>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                    Back to Profile
                </a>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form', ['user' => $user])
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
