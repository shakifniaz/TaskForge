<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Profile Photo --}}
        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />

            <div class="mt-2 flex items-center gap-4">
                @php
                    $photoUrl = $user->profile_photo_path
                        ? asset('storage/' . $user->profile_photo_path)
                        : null;
                @endphp

                <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Profile photo" class="h-full w-full object-cover" />
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400">No Photo</span>
                    @endif
                </div>

                <div class="flex-1">
                    <input
                        id="profile_photo"
                        name="profile_photo"
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-gray-700 dark:text-gray-200
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-md file:border-0
                               file:text-sm file:font-semibold
                               file:bg-gray-100 file:text-gray-700
                               hover:file:bg-gray-200
                               dark:file:bg-gray-900 dark:file:text-gray-200 dark:hover:file:bg-gray-800"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        JPG/PNG recommended. Max 2MB.
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                </div>
            </div>
        </div>

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Username --}}
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input
                id="username"
                name="username"
                type="text"
                class="mt-1 block w-full"
                :value="old('username', $user->username)"
                autocomplete="username"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Use letters, numbers, dashes, or underscores.
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        {{-- Description --}}
        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea
                id="description"
                name="description"
                rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500
                       dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
            >{{ old('description', $user->description) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="email"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
