<x-app-layout>
    @section('content')
    <x-modal name="create.coas" :show="$errors->userDeletion->isNotEmpty()" maxWidth="sm" focusable>
        <form method="post" action="{{ route('coas.store') }}" class="p-6">
            @csrf
            @method('POST')
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Tambah Akun') }}
            </h2>

            <div class="mt-6">
                <div class="relative">
                    <label for="search-dropdown" class="block text-sm font-medium text-gray-700">
                        {{ __('Pilih Parent Akun') }}
                    </label>
                    <div class="mt-1">
                        <input
                            type="text"
                            id="search-dropdown"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Cari nomor akun atau nama akun..."
                        />
                        <div id="dropdown-options" class="absolute mt-1 w-full bg-white shadow-lg rounded-md z-10 hidden">
                            <!-- Options will be populated here by jQuery -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <x-input-label for="nomor_akun" value="{{ __('Nomor Akun') }}" class="sr-only" />

                <x-text-input
                    id="nomor_akun"
                    name="nomor_akun"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Nomor Akun') }}"
                />
            </div>
            <div class="mt-6">
                <x-input-label for="nama_akun" value="{{ __('Nama Akun') }}" class="sr-only" />

                <x-text-input
                    id="nama_akun"
                    name="nama_akun"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Nama Akun') }}"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <x-primary-button type="submit">
                    {{ __('Simpan') }}
                </x-primary-button>
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
            </div>
        </form>
    </x-modal>
        <div class="px-4 pt-4">
            <p class="font-lg text-emerald-500 text-lg">Chart Of Accounts</p>
        </div>
        <div class="card bg-gray-50 rounded-xl border border-zinc-200">
            <div class="flex flex-wrap gap-2 p-1 justify-between">
                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <div id="filter_akun" class="rounded bg-gray-100 py-1 p-1 flex flex-wrap gap-2">
                        <button id="btn-filter-semua" class="btn-akun py-1 bg-gray-200 rounded p-1">Semua</button>
                        <button id="btn-filter-neraca" class="btn-akun py-1 bg-gray-200 rounded p-1">Neraca</button>
                        <button id="btn-filter-labarugi" class="btn-akun py-1 bg-gray-200 rounded p-1">Laba Rugi</button>
                    </div>
                    <div id="filter_level" class="rounded bg-gray-100 p-1 flex flex-wrap gap-2">
                        <button id="btn-filter-all" class="btn-level bg-gray-200 rounded py-1 p-2">Semua</button>
                        <button id="btn-filter-1" class="btn-level bg-gray-200 rounded py-1 p-2">1</button>
                        <button id="btn-filter-2" class="btn-level bg-gray-200 rounded py-1 p-2">2</button>
                        <button id="btn-filter-3" class="btn-level bg-gray-200 rounded py-1 p-2">3</button>
                        <button id="btn-filter-4" class="btn-level bg-gray-200 rounded py-1 p-2">4</button>
                        <button id="btn-filter-5" class="btn-level bg-gray-200 rounded py-1 p-2">5</button>
                    </div>
                    <div class="cari flex items-center space-x-2 relative bg-gray-100 w-full md:w-auto rounded">
                        <label for="cari" class="text-sm font-medium text-gray-900 dark:text-white">Cari:</label>
                        <div class="relative w-full">
                            <input type="text" id="cari" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 pl-2 pr-8 py-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari Akun..." required />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 absolute right-2 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button id="btn-filter-cari" class="btn bg-gray-200 rounded py-1 p-2">Cari</button>
                    </div>
                    <div class="flex items-center space-x-2 relative bg-gray-100 w-full md:w-auto mt-2 md:mt-0">
                        <button id="btn-import-coa" class="btn bg-gray-200 rounded py-1 p-2 hover:bg-emerald-500">Import</button>
                        <button id="btn-export-coa" class="btn bg-gray-200 rounded py-1 p-2 hover:bg-emerald-500">Export</button>
                    </div>
                </div>
                <div class="flex justify-end w-full md:w-auto mt-2 md:mt-0">
                    <x-primary-button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create.coas')"
                    >{{ __('Tambah Akun') }}</x-primary-button>
                </div>
            </div>
            <div class="card-body">
                <table class="w-full" id="coaTable">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTableByColumn('nomor_akun')">
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
                        </tr>
                    </thead>
                    <tbody id="coaTableBody">
                    </tbody>
                </table>
                <div class="pagination flex justify-center p-4 space-x-2">
                    <button id="prevPage" class="prev bg-emerald-600 text-white py-1 px-3 rounded">Previous</button>
                    <div id="pageNumbers" class="flex space-x-2"></div>
                    <button id="nextPage" class="next bg-emerald-600 text-white py-1 px-3 rounded">Next</button>
                </div>
            </div>
        </div>
    @endsection
    @push('script')
        <script type="module">


            let currentPage = 1;
            const rowsPerPage = 11;
            const tableBody = document.getElementById('coaTableBody');
            let totalRows = 0;
            let totalPage = 0;
            const pageNumbers = document.getElementById('pageNumbers');
            let sortDirection = 'asc';
            let filter = 'all';
            let searchInput = document.getElementById('cari');
            const overlay = document.getElementById('overlay');
            let allData = [];

            $('#prevPage').on('click', function() {
                console.log(currentPage)
                if (currentPage > 1) {
                    currentPage--;
                    changePage(currentPage);
                }
            })

            $('#nextPage').on('click', function() {
                if (currentPage < totalPage) {
                    currentPage++;
                    changePage(currentPage);
                }
            })

            $('#btn-filter-semua').on('click', function() {
                filter = 'all';
                renderCoaTable(allData);
                updateFilterButtonStyles($(this));
            })

            $('#btn-filter-neraca').on('click', function() {
                filter = 'neraca';
                renderCoaTable(allData);
                updateFilterButtonStyles($(this));
            })

            $('#btn-filter-labarugi').on('click', function() {
                filter = 'labarugi';
                renderCoaTable(allData);
                updateFilterButtonStyles($(this));
            })

            $('#btn-filter-all').on('click', function() {
                filter = 'all';
                renderCoaTable(allData);
                updateFilterButtonStyles($(this));
            })

            for (let i = 1; i <= 5; i++) {
                $('#btn-filter-' + i).on('click', function() {
                    let filteredData = allData.filter(coa => coa.level.startsWith(i.toString()));
                    renderCoaTable(filteredData);
                    updateFilterButtonStyles($('#btn-filter-' + i));
                })
            }

            $('#btn-filter-cari').on('click', function() {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredData = allData.filter(coa => coa.nama_akun.toLowerCase().includes(searchTerm));
                renderCoaTable(filteredData);
            });

            function updateFilterButtonStyles(activeButton) {
                $('.btn-akun').removeClass('bg-emerald-500').addClass('bg-gray-200');
                $('.btn-level').removeClass('bg-emerald-500').addClass('bg-gray-200');
                activeButton.removeClass('bg-gray-200').addClass('bg-emerald-500');
            }

            async function fetchCoaData() {
                overlay.style.display = 'flex';
                try {
                    const response = await fetch('/api/coas');
                    allData = await response.json();
                    renderCoaTable(allData);
                    overlay.style.display = 'none';
                } catch (error) {
                    console.error('Error fetching COA data:', error);
                    overlay.style.display = 'none';
                }
            }

            function renderCoaTable(data) {
                tableBody.innerHTML = '';
                data.forEach(coa => {
                    const row = document.createElement('tr');
                    let firstNumber = coa.nomor_akun.substring(0, 1);
                    row.innerHTML = `
                        <td class="text-left px-4 py-1">${coa.nomor_akun}</td>
                        <td class="text-left px-4 py-1">${coa.nama_akun}</td>
                        <td class="text-left px-4 py-1">${coa.level}</td>
                        <td class="text-left px-4 py-1">${coa.saldo_normal}</td>
                    `;
                    row.addEventListener('mouseover', function() {
                        this.classList.add('hover:bg-gray-100');
                    });
                    row.addEventListener('mouseout', function() {
                        this.classList.remove('hover:bg-gray-100');
                    });
                    if ((filter === 'all' || filter === 'semua') || (filter === 'neraca' && ['1', '2', '3'].includes(firstNumber)) || (filter === 'labarugi' && ['4', '5', '6'].includes(firstNumber))) {
                        tableBody.appendChild(row);
                    }
                });
                totalRows = data.length;
                totalPage = Math.ceil(totalRows / rowsPerPage);
                setupPagination();
            }

            function changePage(page) {
                currentPage = page;
                document.querySelectorAll('.page-number').forEach(el => el.classList.remove('bg-emerald-600', 'text-gray-600'));
                document.getElementById('page-' + page).classList.add('bg-emerald-600', 'text-gray-600');

                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                Array.from(tableBody.children).forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
            }

            function setupPagination() {
                pageNumbers.innerHTML = '';
                for (let i = 1; i <= totalPage; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.id = 'page-' + i;
                    pageButton.className = 'page-number py-1 px-3 rounded';
                    pageButton.textContent = i;
                    pageButton.onclick = () => changePage(i);
                    pageNumbers.appendChild(pageButton);
                }
                changePage(1);
            }

            fetchCoaData();
        </script>
    @endpush
</x-app-layout>

