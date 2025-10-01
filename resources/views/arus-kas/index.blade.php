@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="arusKasApp" x-cloak>
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-3 sm:gap-4 mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-info-500 to-info-600 rounded-2xl flex items-center justify-center shadow-blue flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-secondary-800">Arus Kas</h1>
                <p class="text-sm sm:text-base text-secondary-600 font-medium">Pengaturan kategori arus kas untuk setiap akun</p>
            </div>
        </div>
    </div>
    <!-- Main Card -->
    <div class="panel overflow-hidden">
        <div class="panel-header">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <h2 class="text-lg sm:text-xl font-bold text-secondary-800">Daftar Chart of Accounts</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs sm:text-sm text-secondary-600 bg-secondary-100 px-2 sm:px-3 py-1 rounded-lg">
                        Total Akun: <span class="font-semibold" x-text="coaData.length"></span>
                    </span>
                    <span class="text-xs sm:text-sm text-secondary-600 bg-secondary-100 px-2 sm:px-3 py-1 rounded-lg sm:hidden" x-show="searchQuery">
                        Hasil: <span class="font-semibold" x-text="filteredData.length"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <!-- Search and Filter Bar -->
            <div class="bg-gradient-to-r from-secondary-50 to-white p-4 rounded-2xl border border-secondary-200/50 shadow-soft mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <!-- Search Section -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-auto">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                x-model="searchQuery"
                                @input="filterData()"
                                class="pl-12 pr-4 py-2 w-full sm:w-64 text-sm border border-secondary-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300"
                                placeholder="Cari nomor atau nama akun...">
                        </div>

                        <button @click="clearFilters" class="btn-secondary w-full sm:w-auto justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0V9a8 8 0 1115.356 2m-15.356 0H4"/>
                            </svg>
                            Reset
                        </button>
                    </div>

                    <!-- Info Section -->
                    <div class="flex items-center gap-2 text-sm text-secondary-600">
                        <svg class="w-4 h-4 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pilih kategori arus kas untuk setiap akun</span>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="overflow-hidden rounded-2xl border border-secondary-200/50 shadow-soft">
                <!-- Mobile View -->
                <div class="block lg:hidden">
                    <template x-for="coa in filteredData" :key="coa.id">
                        <div class="border-b border-secondary-100 p-4 bg-white hover:bg-secondary-50 transition-colors">
                            <!-- Account Info -->
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-primary-600" x-text="coa.nomor_akun.charAt(0)"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-mono font-semibold text-secondary-800 text-sm mb-1" x-text="formatNomorAkun(coa.nomor_akun)"></div>
                                    <div class="font-medium text-secondary-700 text-sm mb-2" x-text="coa.nama_akun"></div>
                                    <div class="flex gap-4 text-xs text-secondary-600">
                                        <span>Level: <span class="font-semibold" x-text="coa.level"></span></span>
                                        <span>Golongan: <span class="font-semibold" x-text="coa.golongan"></span></span>
                                        <span>Saldo: <span class="font-semibold" x-text="coa.saldo_normal"></span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Selection -->
                            <div>
                                <label class="block text-xs font-medium text-secondary-700 mb-2">Kategori Arus Kas</label>
                                <form :action="'/arus-kas/' + coa.id" method="POST" x-ref="form">
                                    @csrf
                                    @method('PUT')
                                    <select class="form-select w-full text-sm" x-model="coa.arus_kas" @change="submitForm(coa.id, $event.target.value)">
                                        <option value="aktifitas_operasional" :selected="coa.arus_kas === 'aktifitas_operasional'">Aktivitas Operasional</option>
                                        <option value="aktifitas_investasi" :selected="coa.arus_kas === 'aktifitas_investasi'">Aktivitas Investasi</option>
                                        <option value="aktifitas_pendanaan" :selected="coa.arus_kas === 'aktifitas_pendanaan'">Aktivitas Pendanaan</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full min-w-full bg-white" id="coaTable">
                        <thead>
                            <tr class="border-b border-secondary-200">
                                <th class="t-head text-left">
                                    <div class="flex items-center gap-2">
                                        Nomor Akun
                                        <span class="ml-auto">
                                            <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="t-head text-left">
                                    <div class="flex items-center gap-2">
                                        Nama Akun
                                        <span class="ml-auto">
                                            <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="t-head text-left">
                                    <div class="flex items-center gap-2">
                                        Golongan
                                        <span class="ml-auto">
                                            <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="t-head text-center">
                                    <div class="flex items-center gap-2 justify-center">
                                        Kategori Arus Kas
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="coa in filteredData" :key="coa.id">
                                <tr class="hover:bg-secondary-50 transition-colors border-b border-secondary-100 last:border-b-0">
                                    <td class="t-cell">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-semibold text-secondary-800" x-text="formatNomorAkun(coa.nomor_akun)"></span>
                                        </div>
                                    </td>
                                    <td class="t-cell">
                                        <span class="font-medium text-secondary-700" x-text="coa.nama_akun"></span>
                                    </td>
                                    <td class="t-cell">
                                        <span class="text-secondary-700 font-medium" x-text="coa.golongan"></span>
                                    </td>
                                    <td class="t-cell text-center">
                                        <form :action="'/arus-kas/' + coa.id" method="POST" x-ref="form">
                                            @csrf
                                            @method('PUT')
                                            <select class="form-select max-w-48" x-model="coa.arus_kas" @change="submitForm(coa.id, $event.target.value)">
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

            <!-- Empty State -->
            <!-- Empty State -->
            <div x-show="!loading && filteredData.length === 0" class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-6 bg-secondary-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-secondary-700 mb-2">Tidak Ada Data</h3>
                <p class="text-secondary-500 max-w-md mx-auto">Tidak ada data Chart of Account yang sesuai dengan filter pencarian.</p>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-6 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="animate-spin w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-secondary-700 mb-2">Memuat Data...</h3>
                <p class="text-secondary-500 max-w-md mx-auto">Sedang mengambil data Chart of Account dari server.</p>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('arusKasApp', () => ({
            coaData: [],
            filteredData: [],
            searchQuery: '',
            selectedGolongan: '',
            loading: false,
            showFilters: false,
            golonganOptions: [],

            init() {
                this.loadData();
            },

            loadData() {
                this.loading = true;

                // Try the route first, fallback to API endpoint
                const endpoints = [
                    '/arus-kas/data',
                    '/api/arus-kas'
                ];

                const tryEndpoint = (index = 0) => {
                    if (index >= endpoints.length) {
                        console.error('All endpoints failed');
                        this.loading = false;
                        return;
                    }

                    const endpoint = endpoints[index].trim();
                    if (!endpoint) {
                        tryEndpoint(index + 1);
                        return;
                    }

                    fetch(endpoint)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (Array.isArray(data)) {
                                // Store original values for reversion purposes
                                this.coaData = data.map(coa => ({
                                    ...coa,
                                    arus_kas_original: coa.arus_kas
                                }));
                                this.filteredData = [...this.coaData];

                                // Extract unique golongan options
                                const golongans = [...new Set(data.map(item => item.golongan))];
                                this.golonganOptions = golongans.sort();

                                this.loading = false;
                                console.log('Data loaded successfully:', data.length, 'records');
                            } else {
                                throw new Error('Data is not an array');
                            }
                        })
                        .catch(error => {
                            console.error(`Error with endpoint ${endpoint}:`, error);
                            tryEndpoint(index + 1);
                        });
                };

                tryEndpoint();
            },

            filterData() {
                let filtered = [...this.coaData];

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(coa =>
                        coa.nomor_akun.toLowerCase().includes(query) ||
                        coa.nama_akun.toLowerCase().includes(query)
                    );
                }

                // Filter by golongan
                if (this.selectedGolongan) {
                    filtered = filtered.filter(coa => coa.golongan === this.selectedGolongan);
                }

                this.filteredData = filtered;
            },

            clearFilters() {
                this.searchQuery = '';
                this.selectedGolongan = '';
                this.filteredData = [...this.coaData];
            },

            formatNomorAkun(nomor) {
                if (!nomor) return '';
                const nomorStr = nomor.toString();
                // Format as XXX-XX (3 digits, dash, 2 digits)
                if (nomorStr.length >= 5) {
                    return nomorStr.substring(0, 3) + '-' + nomorStr.substring(3, 5);
                } else if (nomorStr.length >= 3) {
                    return nomorStr.substring(0, 3) + '-' + nomorStr.substring(3);
                }
                return nomorStr;
            },

            async submitForm(coaId, arusKasValue) {
                // Update the local data immediately for better UX
                const coaIndex = this.coaData.findIndex(coa => coa.id === coaId);
                if (coaIndex !== -1) {
                    this.coaData[coaIndex].arus_kas = arusKasValue;
                    this.filterData(); // Re-filter to update the display
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const response = await fetch(`/arus-kas/${coaId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            arus_kas: arusKasValue,
                            _method: 'PUT'
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update the original value for future reverts
                        if (coaIndex !== -1) {
                            this.coaData[coaIndex].arus_kas_original = arusKasValue;
                        }

                        // Show success notification with SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: data.title || 'Sukses!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        // Revert the local change if server update failed
                        if (coaIndex !== -1) {
                            // Find the original value (you might want to store this)
                            const originalValue = this.coaData[coaIndex].arus_kas_original || '';
                            this.coaData[coaIndex].arus_kas = originalValue;
                            this.filterData();
                        }

                        Swal.fire({
                            icon: 'error',
                            title: data.title || 'Error!',
                            text: data.message || 'Terjadi kesalahan saat mengupdate data',
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.error('Error updating arus kas:', error);

                    // Revert the local change on error
                    if (coaIndex !== -1) {
                        const originalValue = this.coaData[coaIndex].arus_kas_original || '';
                        this.coaData[coaIndex].arus_kas = originalValue;
                        this.filterData();
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal terhubung ke server. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }));
    });
</script>
@endsection
