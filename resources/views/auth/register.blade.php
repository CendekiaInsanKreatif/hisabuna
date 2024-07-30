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
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Logo --> 
                    <div>
                        <x-input-label for="profile_image" :value="__('Logo')" />
                        <input type="file" name="image" id="profile_image" accept=".jpg,.png,.jpeg" class="block w-full px-4 py-3 file:border file:border-gray-400 file:rounded-lg file:text-sm file:font-medium file:bg-white file:shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        {{-- <x-input-error :messages="$errors->get('image')" class="mt-2" /> --}}
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="no_hp" :value="__('No HP')" />
                        <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp" :value="old('no_hp')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="no_telp" :value="__('No Telp')" />
                        <x-text-input id="no_telp" class="block mt-1 w-full" type="text" name="no_telp" :value="old('no_telp')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('no_telp')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="company_name" :value="__('Company Name')" />
                        <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autocomplete="organization" />
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
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
    @push('script')
        <script>
            $(document).ready(function() {
                $('#no_hp').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
                $('#no_telp').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
        </script>
    @endpush
</x-guest-layout>
