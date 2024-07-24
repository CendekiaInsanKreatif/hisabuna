<x-app-layout>
    @section('content')
        @php
            $fields = [
                [
                    'name' => 'nomor_akun',
                    'type' => 'text',
                    'label' => 'Nomer Akun',
                    'required' => true,
                ],
                [
                    'name' => 'nama_akun',
                    'type' => 'text',
                    'label' => 'Nama Akun',
                    'required' => true,
                ],
            ]
        @endphp
        <x-modal :field="$fields" maxWidth="sm" focusable />
        <div class="container mx-auto px-4" x-data="coaTable">
            <div class="mb-6">
                <p class="text-2xl font-semibold text-emerald-500">Bagan Akun</p>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2 w-full md:w-auto">
                <div class="container mx-auto p-4">
                    <div class="flex flex-wrap gap-4 md:gap-6 items-center">
                        <div id="filter_akun" class="flex-grow py-1 px-2 flex flex-wrap gap-2">
                            <button @click="filterCategory('all')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Semua</button>
                            <button @click="filterCategory('neraca')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Neraca</button>
                            <button @click="filterCategory('labarugi')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Laba Rugi</button>
                        </div>
                        <div id="filter_level" class="flex-grow py-1 px-2 flex flex-wrap gap-2 mt-4 md:mt-0">
                            <button @click="filterLevel('all')" class="btn-level bg-gray-200 rounded py-1 px-3 hover:bg-emerald-500 transition duration-300">Semua</button>
                            <template x-for="i in 5" :key="i">
                                <button @click="filterLevel(i.toString())" class="btn-level bg-gray-200 rounded py-1 px-3 hover:bg-emerald-500 transition duration-300" x-text="i"></button>
                            </template>
                        </div>
                        <div class="cari flex items-center space-x-2 mt-4 md:mt-0">
                            <label for="cari" class="text-sm font-medium text-gray-900 dark:text-white">Cari:</label>
                            <div class="relative">
                                <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 pl-3 pr-10 py-1 w-full md:w-64 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari Akun..." @keydown.enter="searchCoaTable">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <button class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.store') }}', name: 'coas.create', title: 'Tambah Akun', type: 'form' })">
                                Tambah Akun
                            </button>
                        </div>
                        <div class="flex items-center space-x-2 ml-auto mt-4 md:mt-0">
                            <a href="{{ route('report.print-coa') }}" class="btn bg-gray-200 rounded py-1 px-3 hover:bg-emerald-500 transition duration-300">Print</a>
                            <button x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.import') }}', name: 'coas.import', title: 'Import Akun', type: 'custom' })" class="btn bg-gray-200 rounded py-1 px-3 hover:bg-emerald-500 transition duration-300">Import</button>
                            <form method="POST" action="{{ route('coas.export') }}">
                                @csrf
                                <button type="submit" class="btn bg-gray-200 rounded py-1 px-3 hover:bg-emerald-500 transition duration-300">
                                  Sample
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-x-auto mt-1">
                    <table class="w-full min-w-full" id="coaTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nomer Akun
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
                                        Saldo Normal
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="coaTableBody">
                            <template x-for="coa in paginatedData" :key="coa.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false">
                                    <td class="text-left px-4 py-1" x-text="formatNomorAkun(coa.nomor_akun)"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.nama_akun"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.level"></td>
                                    <td class="text-left px-4 py-1" x-text="coa.saldo_normal"></td>
                                    <td class="text-left px-4 py-1 items-center text-center mt-1">
                                        <x-primary-button
                                            class="w-full md:w-auto lg:w-auto md:mt-0 mt-1"
                                            x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('coas.show', '') }}/${coa.id}`, name: 'coas.show', title: 'Lihat Akun', data: coa, type: 'form' })"
                                        >{{ __('View') }}</x-primary-button>
                                        <x-primary-button
                                            class="w-full md:w-auto lg:w-auto md:mt-0 mt-1"
                                            x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('coas.update', '') }}/${coa.id}`, name: 'coas.update', title: 'Edit Akun', data: coa, method: 'PUT', type: 'form' })"
                                        >{{ __('Edit') }}</x-primary-button>
                                        <x-primary-button
                                            class="w-full md:w-auto lg:w-auto md:mt-0 mt-1"
                                            x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('coas.destroy', '') }}/${coa.id}`, name: 'coas.destroy', title: 'Hapus Akun', data: coa, method: 'DELETE', type: 'delete' })"
                                        >{{ __('Delete') }}</x-primary-button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="pagination flex justify-center p-4 space-x-2">
                        <button @click="prevPage" class="prev bg-emerald-600 text-white py-1 px-3 rounded">Previous</button>
                        <div id="pageNumbers" class="flex space-x-2">
                            <template x-for="page in pagesToShow" :key="page">
                                <button @click="changePage(page)" :class="{'bg-emerald-600 text-white': page === currentPage, 'bg-gray-200': page !== currentPage}" class="page-number py-1 px-3 rounded" x-text="page"></button>
                            </template>
                        </div>
                        <button @click="nextPage" class="next bg-emerald-600 text-white py-1 px-3 rounded">Next</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('coaTable', () => ({
                currentPage: 1,
                rowsPerPage: 7,
                totalRows: 0,
                totalPage: 0,
                sortDirection: 'asc',
                filter: 'all',
                searchInput: '',
                allData: [],
                hover: false,
                get paginatedData() {
                    const filteredData = this.filteredData();
                    const start = (this.currentPage - 1) * this.rowsPerPage;
                    const end = start + this.rowsPerPage;
                    return filteredData.slice(start, end);
                },
                get pagesToShow() {
                    const startPage = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
                    const endPage = Math.min(startPage + 4, this.totalPage);
                    return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
                },
                changePage(page) {
                    this.currentPage = page;
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },
                nextPage() {
                    if (this.currentPage < this.totalPage) {
                        this.currentPage++;
                    }
                },
                async fetchCoaData() {
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/coas');
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            this.allData = data;
                            this.totalRows = data.length;
                            this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                        } else {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format. Please check the data returned from the server.');
                        }
                    } catch (error) {
                        console.error('Error fetching COA data:', error);
                        alert('Error fetching COA data. Please try again later.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },
                renderCoaTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchCoaTable() {
                    this.renderCoaTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderCoaTable();
                },
                filterLevel(level) {
                    this.filter = level;
                    this.renderCoaTable();
                },
                filteredData() {
                    return this.allData.filter(coa => {
                        const matchesCategory = (this.filter === 'all' ||
                            (this.filter === 'neraca' && ['1', '2', '3'].includes(coa.nomor_akun.charAt(0))) ||
                            (this.filter === 'labarugi' && ['4', '5', '6'].includes(coa.nomor_akun.charAt(0))) ||
                            (coa.level.startsWith(this.filter))
                        );
                        const matchesSearch = coa.nama_akun.toLowerCase().includes(this.searchInput.toLowerCase());
                        return matchesCategory && matchesSearch;
                    });
                },
                formatNomorAkun(nomor_akun) {
                    let formatted = nomor_akun.replace(/\D/g, ''); // Hapus karakter non-digit
                    if (formatted.length > 6) {
                        // Format untuk level 5, misalnya 111-11-011
                        formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' + formatted.slice(5);
                    } else if (formatted.length > 4) {
                        // Format untuk level 4, misalnya 111-11
                        formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
                    } else {
                        // Format untuk level 3 atau kurang, misalnya 111
                        formatted = formatted.slice(0, 3);
                    }
                    return formatted;
                },
                exportCoaTable() {
                    window.location.href = '{{ route('coas.export') }}';
                },
                init() {
                    this.fetchCoaData();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
