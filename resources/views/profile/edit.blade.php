<x-app-layout>
    @php
        // da(old('name'));
    @endphp
    @section('content')
        <div class="container mx-auto px-2 py-2 rounded-lg bg-gray-100">
            <form action="{{ route('profile.update') }}" method="POST" class="bg-white rounded-lg shadow-lg w-full p-6 space-y-6">
                @csrf
                @method('PATCH')
                <div class="flex flex-wrap md:flex-nowrap">
                    <div class="md:w-1/2 p-6 space-y-6">
                        <h1 class="text-4xl font-bold text-gray-900">Profil Pengguna</h1>
                        <div class="space-y-4">
                            <label for="profile_image" class="block text-sm font-medium text-gray-800">Gambar Profil</label>
                            <div class="flex items-center">
                                <input type="file" name="profile" id="profile_image" class="block w-full px-4 py-3 file:border file:border-gray-400 file:rounded-lg file:text-sm file:font-medium file:bg-white file:shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <img id="profile_image_preview" src="{{ asset('storage/' . $user->profile) }}" alt="Preview Image" class="hidden w-20 h-20 rounded-md object-cover ml-4">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label for="name" class="block text-sm font-medium text-gray-800">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="email" class="block text-sm font-medium text-gray-800">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                    </div>
                    {{-- <div class="md:w-1/2 p-6 space-y-6">
                        <h1 class="text-4xl font-bold text-gray-900">Profil Perusahaan</h1>
                        <div class="space-y-4">
                            <label for="company_logo" class="block text-sm font-medium text-gray-800">Logo Perusahaan</label>
                            <div class="flex items-center">
                                <img id="company_logo_preview" src="#" alt="Preview Logo" class="hidden w-20 h-20 rounded-md object-cover ml-4">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label for="company_name" class="block text-sm font-medium text-gray-800">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="company_name" value="{{ auth()->user()->company->name }}" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" @if(!auth()->user()['roles'] == (['superadmin', 'admin'])) disabled @endif>
                        </div>
                        <div class="space-y-4">
                            <label for="company_desc" class="block text-sm font-medium text-gray-800">Deskripsi Perusahaan</label>
                            <textarea name="company_desc" id="company_desc" rows="4" class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" @if(!auth()->user()['roles'] == (['superadmin', 'admin'])) disabled @endif>{{ auth()->user()->company->desc }}</textarea>
                        </div>
                    </div> --}}
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 transition ease-in-out duration-150">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    @endsection
    @push('script')
    <script type="module">
        $('#profile_image').change(function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $('#profile_image_preview');
                    preview.attr('src', e.target.result);
                    preview.removeClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
        $('#company_logo').change(function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $('#company_logo_preview');
                    preview.attr('src', e.target.result);
                    preview.removeClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
</x-app-layout>
