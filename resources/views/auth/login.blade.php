<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <main class="flex h-screen w-full flex-wrap">
        <div class="flex-1 bg-emerald-500 hidden md:flex items-center justify-center md:flex-1/2">
            <img class="max-w-md w-full" src="/images/login-artwork.png" alt="Login artwork Hisabuna" />
        </div>
        <div class="flex-1 flex flex-col bg-emerald-50 items-center justify-center md:flex-1/2">
            <div class="w-full p-5 max-w-md grid place-items-center">
                <div class="relative">
                    <img src="/images/brand/logo-hisabuna-color.svg" class="w-40" alt="Official Logo of Hisabuna" />
                </div>
            </div>
            <div class="h-5 flex items-center w-full max-w-md">
                @if (session('error'))
                    <div
                        class="flex gap-2 border border-{{ session('color') }}-400 bg-{{ session('color') }}-100 p-2 rounded w-full">
                        <div
                            class="border border-{{ session('color') }}-400 rounded-full size-5 text-xs text-{{ session('color') }}-400 font-bold grid place-items-center">
                            !</div>
                        <p class="text-sm text-{{ session('color') }}-400">{{ session('error') }}</p>
                    </div>
                @endif
            </div>
            <div class="flex flex-col p-5 gap-5 w-full max-w-md">
                <form method="POST" action="{{ route('login') }}" class="flex flex-col">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex-1">
                            <label for="remember_me">
                                <input id="remember_me" type="checkbox"
                                    class="rounded text-emerald-500 border border-zinc-300  shadow-sm focus:ring-emerald-500"
                                    name="remember">
                                <span class="ml-2 text-sm ">{{ __('Stay signed-in') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center space-between space-x-4">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-emerald-600  hover:text-emerald-900  rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                    href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-4">
                        <x-primary-button class="block mt-1 w-full text-center">
                            {{ __('Login') }}
                        </x-primary-button>
                    </div>

                    <div class="mt-4">
                        <p>New to Hisabuna?
                            <a href="{{ route('register') }}" class="text-emerald-600 underline">Create new account</a>
                        </p>
                    </div>



                </form>
            </div>
        </div>
    </main>
</x-guest-layout>
