<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input
                id="username"
                class="block mt-1 w-full"
                type="text"
                name="username"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />

            {{-- Real-time availability status --}}
            <p id="usernameHelp" class="mt-2 text-xs text-gray-500"></p>
        </div>

        <!-- First Name -->
        <div class="mt-4">
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input
                id="first_name"
                class="block mt-1 w-full"
                type="text"
                name="first_name"
                :value="old('first_name')"
                required
                autocomplete="given-name"
            />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input
                id="last_name"
                class="block mt-1 w-full"
                type="text"
                name="last_name"
                :value="old('last_name')"
                required
                autocomplete="family-name"
            />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="email"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Username availability AJAX --}}
    <script>
        const usernameEl = document.getElementById('username');
        const helpEl = document.getElementById('usernameHelp');

        let t = null;
        let lastValue = '';
        let lastRequestId = 0;

        function setHelp(text, type = 'neutral') {
            if (!helpEl) return;
            helpEl.textContent = text || '';
            helpEl.className = 'mt-2 text-xs ' + (
                type === 'ok' ? 'text-green-600' :
                type === 'bad' ? 'text-red-600' :
                'text-gray-500'
            );
        }

        async function checkUsername(value) {
            const requestId = ++lastRequestId;
            const url = `{{ route('username.check') }}?username=${encodeURIComponent(value)}`;

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

                // ✅ show error if route not working
                if (!res.ok) {
                    setHelp('Could not check username (route error).', 'bad');
                    return;
                }

                const data = await res.json();

                // ignore stale response
                if (requestId !== lastRequestId) return;

                if (data.available) {
                    setHelp('Username is available ✅', 'ok');
                } else {
                    if (data.reason === 'taken') setHelp('Username is already taken ❌', 'bad');
                    else setHelp('Username must be 3–50 chars, letters/numbers/_/- only.', 'bad');
                }
            } catch (e) {
                setHelp('Could not check username (network error).', 'bad');
            }
        }

        usernameEl?.addEventListener('input', () => {
            const value = usernameEl.value.trim();

            if (!value) {
                setHelp('');
                lastValue = '';
                return;
            }

            if (value.length < 3) {
                setHelp('Username must be at least 3 characters.', 'bad');
                return;
            }

            clearTimeout(t);
            t = setTimeout(() => {
                if (value === lastValue) return;
                lastValue = value;

                setHelp('Checking availability…', 'neutral');
                checkUsername(value);
            }, 350);
        });
    </script>

</x-guest-layout>
