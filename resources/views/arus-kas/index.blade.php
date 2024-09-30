<x-app-layout>
    @section('content')
        <div class="container mx-auto px-4" x-data="coaTable">
            <div class="mb-6">
                <p class="text-2xl font-semibold text-emerald-500">Arus Kas</p>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2 w-full md:w-auto">
                <div class="container mx-auto p-4">
                    <div class="flex flex-wrap gap-4 md:gap-6 items-center">
                        <div class="cari flex items-center space-x-2 mt-4 md:mt-0">
                            <button @click="resetSearch" style="background-color: #f87171;" class="text-white px-2 py-1 rounded-md">Reset</button>
                            <div class="relative">
                                <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 pl-3 pr-10 py-1 w-full md:w-64 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari Akun..." @keydown.enter="searchCoaTable">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-x-auto mt-1">
                    <table class="w-full min-w-full" id="coaTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nomor Akun
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nama Akun
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Level Akun
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Golongan
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Saldo Normal
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Aktivitas
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="coaTableBody">
                            <template x-for="coa in filteredData" :key="coa.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false">
                                    <td class="text-left px-4 py-1" x-text="formatNomorAkun(coa.nomor_akun)"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.nama_akun"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.level"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.golongan"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.saldo_normal"></td>
                                    <td class="text-left px-4 py-1 items-center text-center mt-1">
                                        <form :action="'/arus-kas/' + coa.id" method="POST" x-ref="form">
                                            @csrf
                                            @method('PUT')
                                            <select class="form-select block w-full mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" x-model="coa.arus_kas" @change="submitForm(coa.id, $event.target.value)">
                                                <option value="aktifitas_operasional" :selected="coa.arus_kas === 'aktifitas_operasional'">Aktivitas Operasional</option>
                                                <option value="aktifitas_investasi" :selected="coa.arus_kas === 'aktifitas_investasi'">Aktivitas Investasi</option>
                                                <option value="aktifitas_pendanaan" :selected="coa.arus_kas === 'aktifitas_pendanaan'">Aktivitas Pendanaan</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
    @push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('coaTable', () => ({
                searchInput: '',
                allData: [],
                filteredData: [],
                hover: false,
                async fetchCoaData() {
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/arus-kas');
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            this.allData = data;
                            this.filteredData = data;
                        } else {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format.');
                        }
                    } catch (error) {
                        console.error('Error fetching COA data:', error);
                        alert('Error fetching COA data.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },

                searchCoaTable() {
                    const searchTerm = this.searchInput.toLowerCase();
                    this.filteredData = this.allData.filter(coa => {
                        const matchesNamaAkun = coa.nama_akun.toLowerCase().includes(searchTerm);
                        const matchesNomorAkun = coa.nomor_akun.toLowerCase().includes(searchTerm);
                        return matchesNamaAkun || matchesNomorAkun;
                    });
                },

                resetSearch() {
                    this.searchInput = '';
                    this.filteredData = this.allData;
                },

                async submitForm(id, value) {
                    const form = this.$refs.form;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        const response = await fetch(`/arus-kas/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ value: value, _method: 'PUT' }),
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        alert('Update successful');
                    } catch (error) {
                        console.error('Error updating Arus Kas:', error);
                        alert('Error updating Arus Kas.');
                    }
                },

                formatNomorAkun(nomor_akun) {
                    let formatted = nomor_akun.replace(/\D/g, '');
                    if (formatted.length > 6) {
                        formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' + formatted.slice(5);
                    } else if (formatted.length > 4) {
                        formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
                    } else {
                        formatted = formatted.slice(0, 3);
                    }
                    return formatted;
                },

                init() {
                    this.fetchCoaData();
                }
                
            }));
        });
    </script>
    @endpush
</x-app-layout>
