<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <main class="flex h-screen w-full flex-wrap">
        <div class="flex-1 bg-emerald-500 flex items-center justify-center md:flex-1/2">
            <img class="max-w-md w-full" src="/images/login-artwork.png" alt="Login artwork Hisabuna" />
        </div>
        <div class="flex-1 flex flex-col bg-emerald-50 items-center justify-center md:flex-1/2">
            <div class="w-full p-5 max-w-md grid place-items-center">
                <div class="relative">
                    <img
                        src="/images/brand/logo-hisabuna-color.svg"
                        height="32"
                        width="120"
                        alt="logo hisabuna"
                    />
                </div>
            </div>
            <div class="h-16 flex items-center w-full px-5 py-2 max-w-md">
                @if (session('error'))
                    <div class="flex gap-2 border border-{{ session('color') }}-400 bg-{{ session('color') }}-100 p-2 rounded w-full">
                        <div class="border border-{{ session('color') }}-400 rounded-full size-5 text-xs text-{{ session('color') }}-400 font-bold grid place-items-center">!</div>
                        <p class="text-sm text-{{ session('color') }}-400">{{ session('error') }}</p>
                    </div>
                @endif
            </div>
            <div class="flex flex-col p-5 gap-5 w-full max-w-md">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>


                    <div class="flex items-center justify-end mt-4 space-x-4">
                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                                {{ __('Already registered?') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Register') }}
                            </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-guest-layout>
