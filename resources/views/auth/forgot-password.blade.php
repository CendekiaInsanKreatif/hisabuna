<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <main class="flex h-screen w-full flex-wrap">
        <!-- Left Illustration -->
        <div class="flex-1 bg-emerald-500 flex items-center justify-center">
            <img class="max-w-md w-full drop-shadow-lg" src="/images/login-artwork.png" alt="Login artwork Hisabuna" />
        </div>

        <!-- Right Form Section -->
        <div class="flex-1 flex flex-col bg-emerald-50 items-center justify-center relative">
            <!-- Logo -->
            <div class="absolute top-6 left-0 right-0 flex justify-center">
                <img src="/images/brand/logo-hisabuna-color.svg" height="32" width="120" alt="Logo Hisabuna" />
            </div>

            <!-- Alert Section -->
            <div class="h-16 flex items-center w-full px-5 py-2 max-w-md">
                @if (session('error'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        class="flex items-center gap-3 border border-{{ session('color') }}-400 bg-{{ session('color') }}-50 p-3 rounded-lg w-full shadow-sm"
                    >
                        <!-- Icon -->
                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-{{ session('color') }}-100 border border-{{ session('color') }}-400 text-{{ session('color') }}-500 font-bold">
                            !
                        </div>

                        <!-- Message -->
                        <p class="text-sm text-{{ session('color') }}-600 font-medium">
                            {{ session('error') }}
                        </p>

                        <!-- Close Button -->
                        <button
                            @click="show = false"
                            class="ml-auto text-{{ session('color') }}-500 hover:text-{{ session('color') }}-700 focus:outline-none"
                        >
                            ✕
                        </button>
                    </div>
                @elseif (session('status'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        class="flex items-center gap-3 border border-emerald-400 bg-emerald-50 p-3 rounded-lg w-full shadow-sm"
                    >
                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 border border-emerald-400 text-emerald-600 font-bold">✓</div>
                        <p class="text-sm text-emerald-700 font-medium">
                            {{ session('status') }}
                        </p>
                        <button
                            @click="show = false"
                            class="ml-auto text-emerald-500 hover:text-emerald-700 focus:outline-none"
                        >
                            ✕
                        </button>
                    </div>
                @endif
            </div>

            <!-- Form Card -->
            <div class="flex flex-col p-6 gap-5 w-full max-w-md bg-white rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold text-emerald-700 text-center">
                    Lupa Password?
                </h2>
                <p class="text-sm text-gray-500 text-center">
                    Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang password Anda.
                </p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500">
                            {{ __('Kirim Tautan Reset Password') }}
                        </x-primary-button>
                    </div>

                    <!-- Back to Login -->
                    <div class="flex items-center justify-center mt-4 space-x-2 text-sm">
                        <p class="text-gray-500">Sudah punya akun?</p>
                        <a href="{{ route('login') }}" class="text-emerald-600 font-medium hover:underline">
                            Masuk sekarang
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-gray-400 text-xs">
                &copy; {{ date('Y') }} <strong>Hisabuna</strong>. All rights reserved.
            </div>
        </div>
    </main>
</x-guest-layout>
