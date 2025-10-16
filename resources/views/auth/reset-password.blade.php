<x-guest-layout>
    <main class="flex h-screen w-full flex-wrap">
        <!-- Ilustrasi (opsional) -->
        <div class="hidden md:flex flex-1 bg-emerald-500 items-center justify-center">
            <img src="/images/login-artwork.png" alt="Reset Password" class="max-w-md w-full">
        </div>

        <!-- Form -->
        <div class="flex-1 flex flex-col bg-emerald-50 items-center justify-center">
            <div class="w-full p-5 max-w-md text-center space-y-6">
                <div>
                    <img src="/images/brand/logo-hisabuna-color.svg" alt="Hisabuna" class="mx-auto h-10" />
                    <h1 class="text-2xl font-bold text-emerald-700 mt-4">Buat Password Baru</h1>
                    <p class="text-sm text-gray-600 mt-1">Masukkan password baru Anda di bawah ini.</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="bg-white shadow-md rounded-lg p-6 space-y-5 border border-gray-100">
                    @csrf
                    <!-- Token dari URL -->
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <!-- Email dari URL -->
                    <input type="hidden" name="email" value="{{ request()->get('email') }}">

                    <!-- Password baru -->
                    <div>
                        <x-input-label for="password" :value="__('Password Baru')" />
                        <x-text-input id="password" class="block mt-1 w-full"
                            type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-3">
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 w-full justify-center">
                            {{ __('Perbarui Password') }}
                        </x-primary-button>
                    </div>
                </form>

                <div class="text-center text-sm text-gray-500 mt-4">
                    <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                        Kembali ke login
                    </a>
                </div>
            </div>
        </div>
    </main>
</x-guest-layout>
