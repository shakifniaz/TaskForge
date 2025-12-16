<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile') }}
            </h2>

            <a href="{{ route('profile.edit.tab') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-semibold hover:bg-gray-800 transition">
                {{ __('Edit Profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                @php
                    $photoUrl = $user->profile_photo_path
                        ? asset('storage/' . $user->profile_photo_path)
                        : null;
                @endphp

                <div class="flex items-start gap-6">
                    <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center shrink-0">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="Profile photo" class="h-full w-full object-cover" />
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">No Photo</span>
                        @endif
                    </div>

                    <div class="flex-1 space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Username</p>
                            <p class="text-base text-gray-900 dark:text-gray-100 font-medium">
                                {{ $user->username ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                            <p class="text-base text-gray-900 dark:text-gray-100 font-medium">
                                {{ $user->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-base text-gray-900 dark:text-gray-100 font-medium">
                                {{ $user->email }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                            <p class="text-base text-gray-900 dark:text-gray-100">
                                {{ $user->description ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
