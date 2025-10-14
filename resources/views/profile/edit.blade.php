<x-app-layout>
    @section('content')
    @php
        $minYear = \Carbon\Carbon::parse(
            \Illuminate\Support\Facades\DB::table('coas')
                ->where('created_by', auth()->id())
                ->min('created_at') ?? now()
        )->year;

        $currentYear = \Carbon\Carbon::now()->year;
        $startYear = min($minYear, $currentYear - 10);
    @endphp


    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900">Pengaturan Profil</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data pengguna dan informasi perusahaan Anda.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            @csrf
            @method('PATCH')

            <!-- Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- Kolom Kiri: Profil Pengguna -->
                <section class="p-6 md:p-8 border-b lg:border-b-0 lg:border-r border-gray-100 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Profil Pengguna</h2>
                        <p class="mt-1 text-sm text-gray-500">Nama, email, dan kontak Anda.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No HP -->
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">No HP</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" id="no_hp" name="no_hp" required
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_hp', $user->no_hp) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <p class="mt-1 text-xs text-gray-500">Hanya angka, tanpa spasi atau tanda baca.</p>
                            @error('no_hp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No Telp -->
                        <div>
                            <label for="no_telp" class="block text-sm font-medium text-gray-700">No Telp</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" id="no_telp" name="no_telp" required
                                   placeholder="0274xxxxxxx"
                                   value="{{ old('no_telp', $user->no_telp) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            @error('no_telp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Kolom Kanan: Profil Perusahaan -->
                <section class="p-6 md:p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Profil Perusahaan</h2>
                        <p class="mt-1 text-sm text-gray-500">Logo, nama, dan periode akuntansi.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Logo Perusahaan -->
                        <div x-data="{
                                preview: '{{ $user->company_logo ? asset('storage/'.$user->company_logo) : '' }}',
                                fileChosen(e){ const f=e.target.files[0]; if(!f) return; const r=new FileReader(); r.onload = ev => this.preview = ev.target.result; r.readAsDataURL(f); }
                            }"
                            class="space-y-3">
                            <label for="company_logo" class="block text-sm font-medium text-gray-700">Logo Perusahaan</label>

                            <div class="flex items-start gap-4">
                                <div class="w-24 h-24 rounded-xl ring-1 ring-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                                    <template x-if="preview">
                                        <img :src="preview" alt="Preview Logo" class="w-full h-full object-contain" />
                                    </template>
                                    <template x-if="!preview">
                                        <span class="text-xs text-gray-400">No Logo</span>
                                    </template>
                                </div>

                                <div class="flex-1">
                                    <input
                                        id="company_logo"
                                        name="company_logo"
                                        type="file"
                                        accept=".jpg,.jpeg,.png"
                                        @change="fileChosen"
                                        class="block w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:ring-1 file:ring-gray-200 file:bg-white file:text-gray-700 hover:file:bg-gray-50 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    >
                                    <p class="mt-2 text-xs text-gray-500">PNG/JPG, disarankan rasio kotak, maksimal ~1MB.</p>
                                    @error('company_logo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Nama Perusahaan -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input type="text" id="company_name" name="company_name" required
                                   value="{{ old('company_name', auth()->user()->company_name) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Periode Akuntansi -->
                        <div>
                            <label for="periode" class="block text-sm font-medium text-gray-700">Periode Akuntansi</label>
                            <select id="periode" name="periode" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                                @for ($year = $startYear; $year <= $currentYear; $year++)
                                    <option value="{{ $year }}" {{ (int)$year === (int)old('periode', auth()->user()->periode) ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('periode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Tahun berjalan untuk laporan & penomoran.</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500">Pastikan data sudah benar sebelum menyimpan.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 bg-white ring-1 ring-gray-200 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    @endsection

    @push('script')
    <script type="module">
        // Sanitasi angka untuk HP & Telp
        const onlyDigits = (el) => el.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        const hp = document.getElementById('no_hp');
        const telp = document.getElementById('no_telp');
        if (hp) onlyDigits(hp);
        if (telp) onlyDigits(telp);
    </script>
    @endpush
</x-app-layout>
