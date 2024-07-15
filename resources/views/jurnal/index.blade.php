<x-app-layout>
    @section('content')
    @php
        $fields = [
            [
                'name' => 'jenis',
                'label' => 'Jenis',
                'type' => 'text',
                'disabled' => true,
            ],
            [
                'name' => 'keterangan',
                'label' => 'Keterangan',
                'type' => 'text',
                'disabled' => true,
            ],
        ];
    @endphp
    <x-modal :field="$fields" maxWidth="lg" focusable />
    <div class="container mx-auto px-4" x-data="jurnalTable">
        <div class="mb-6">
            <p class="text-2xl font-semibold text-emerald-500">Jurnal</p>
        </div>
        <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2">
            <div class="flex flex-wrap items-center justify-between">
                <div id="filterAkun" class="flex-grow rounded bg-gray-100 py-1 px-2 flex flex-wrap gap-2 w-full md:w-auto justify-between">
                    <div class="flex flex-wrap gap-2">
                        <button @click="filterCategory('all')" class="btn-akun py-1 bg-gray-200 rounded px-2 hover:bg-emerald-500 transition duration-300">Semua</button>
                        <button @click="filterCategory('rv')" class="btn-akun py-1 bg-gray-200 rounded px-2 hover:bg-emerald-500 transition duration-300">RV</button>
                        <button @click="filterCategory('pv')" class="btn-akun py-1 bg-gray-200 rounded px-2 hover:bg-emerald-500 transition duration-300">PV</button>
                        <button @click="filterCategory('jv')" class="btn-akun py-1 bg-gray-200 rounded px-2 hover:bg-emerald-500 transition duration-300">JV</button>
                        <div class="relative w-full md:w-auto flex-grow md:flex-grow-0">
                            <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 w-full pl-3 pr-10 py-1" placeholder="Cari Jurnal . . ." @keydown.enter="searchJurnalTable">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="{{ route('report.daftarjurnal') }}" class="btn bg-gray-200 rounded py-1 px-4 hover:bg-emerald-500 transition duration-300">Daftar Jurnal</a>
                    </div>
                    <a class="py-2 px-4 inline-flex items-center justify-center bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong" href="{{ route('jurnal.create') }}">Tambah Jurnal</a>
                </div>
                </div>
                <div class="card-body overflow-x-auto">
                    <table class="w-full min-w-full" id="jurnalTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Jenis
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-2 py-1 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nomor Jurnal
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Tanggal
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Keterangan
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
                        <tbody id="jurnalTableBody">
                            <template x-for="jurnal in paginatedData" :key="jurnal.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false">
                                    <td class="text-left text-sm font-sans px-2 py-1" x-text="jurnal.no_transaksi"></td>
                                    <td class="text-left text-sm font-sans px-2 py-1" x-text="jurnal.jenis"></td>
                                    <td class="text-left text-sm font-sans px-2 py-1" x-text="new Date(jurnal.jurnal_tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'long' })"></td>
                                    <td class="text-left text-sm font-sans px-2 py-1 break-words" x-text="jurnal.keterangan"></td>
                                    <td class="text-left text-sm font-sans px-2 py-1 flex items-center justify-center">
                                        <div class="inline-flex flex-col md:flex-row gap-1">
                                            <a :href="`{{ route('report.transaksi', '') }}/${jurnal.id}`" class="btn bg-gray-200 rounded py-1 px-4 hover:bg-emerald-500 transition duration-300">Print</a>
                                            <x-primary-button class="w-full md:w-auto"
                                                x-data="{data: jurnal}"
                                                x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('jurnal.show', '') }}/${jurnal.id}`, name: 'jurnal.show', title: 'Lihat Jurnal', data: jurnal, type: 'form' })"
                                            >{{ __('View') }}</x-primary-button>
                                            <a class="w-full md:w-auto py-1 px-2 inline-flex items-center justify-center bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong" :href="`{{ url('jurnal') }}/${jurnal.id}/edit`">
                                                edit
                                            </a>

                                        </div>
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
            Alpine.data('jurnalTable', () => ({
                currentPage: 1,
                rowsPerPage: 10,
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
                    const endPage = Math.min(startPage + 2, this.totalPage);
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
                async fetchJurnalData() {
                    try {
                        const response = await fetch('/api/jurnal');
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
                        console.error('Error fetching Jurnal data:', error);
                        alert('Error fetching Jurnal data. Please try again later.');
                    }
                },
                renderJurnalTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchJurnalTable() {
                    this.renderJurnalTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderJurnalTable();
                },
                filteredData() {
                    return this.allData.filter(jurnal => {
                        const matchesCategory = (this.filter === 'all' || jurnal.jenis.toLowerCase() === this.filter.toLowerCase());
                        const matchesSearch = jurnal.keterangan.toLowerCase().includes(this.searchInput.toLowerCase());
                        return matchesCategory && matchesSearch;
                    });
                },

                init() {
                    this.fetchJurnalData();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
