<x-app-layout>
    @section('content')
        @php
            $fields = [
                [
                    'name' => 'name',
                    'type' => 'text',
                    'label' => 'Nama User',
                    'required' => true,
                ],
                [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email',
                    'required' => true,
                ],
                [
                    'name' => 'no_hp',
                    'type' => 'number',
                    'label' => 'Nomor HP',
                    'required' => true,
                ],
                [
                    'name' => 'no_telp',
                    'type' => 'number',
                    'label' => 'Nomor Telepon',
                    'required' => false,
                ],
                [
                    'name' => 'company_name',
                    'type' => 'text',
                    'label' => 'Nama Perusahaan',
                    'required' => true,
                ],
                [
                    'name' => 'company_logo',
                    'type' => 'file',
                    'label' => 'Logo Perusahaan',
                    'required' => true,
                ],
                [
                    'name' => 'periode',
                    'type' => 'number',
                    'label' => 'Periode',
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'label' => 'Password',
                    'required' => true,
                ],
                [
                    'name' => 'profile',
                    'type' => 'select',
                    'label' => 'Profile',
                    'required' => true,
                    'options' => [
                        'trial' => 'Trial',
                        'standard' => 'Standard',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ],
                ],
                [
                    'name' => 'is_active',
                    'type' => 'select',
                    'label' => 'Status',
                    'required' => true,
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ],
                ],
            ];
        @endphp
        <x-modal :field="$fields" maxWidth="2xl" focusable />
        <style>
            table th, table td {
                min-width: 150px; 
                word-wrap: break-word; 
            }
        </style>
        <div class="container mx-auto px-2" x-data="userTable">
            <div class="mb-6">
                <p class="text-2xl font-semibold text-emerald-500">Akun Pengguna</p>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2 w-full md:w-auto">
                <div class="container mx-auto p-4">
                    <div class="flex flex-wrap gap-4 md:gap-6 items-center">
                        <div class="cari flex items-center space-x-2 mt-4 md:mt-0">
                            <label for="cari" class="text-sm font-medium text-gray-900 dark:text-white">Cari:</label>
                            <div class="relative">
                                <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-200 pl-3 pr-10 py-1 w-full md:w-64 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-200" placeholder="Cari User..." @keydown.enter="searchUserTable">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <button class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-2" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('users.store') }}', name: 'users.create', title: 'Tambah User', type: 'form' })">
                                Tambah User
                            </button>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <label for="langganan" class="text-sm font-medium text-gray-900 dark:text-white">Langganan:</label>
                            <select id="langganan" x-model="selectLangganan" @change="handleLanggananChange(selectLangganan)" class="ml-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-200">
                                <option value="all">Semua</option>
                                <option value="trial">Trial</option>
                                <option value="standard">Standard</option>
                                <option value="pro">Pro</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <label for="status" class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                            <select id="status" x-model="filter" @change="handleStatusChange(filter)" class="ml-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-200">
                                <option value="all">Semua</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total User: </span>
                            <span class="ml-2 text-lg font-semibold text-emerald-500" x-text="filteredData().length"></span>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-x-auto mt-1">
                    <table class="w-full min-w-full" id="userTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nama
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Email
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nomor HP
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                {{-- <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nama Perusahaan
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th> --}}
                                <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Langganan
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-2 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Status
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-2 py-2 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <template x-for="user in paginatedData" :key="user.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false" class="cursor-pointer hover:bg-gray-100" x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('users.show', '') }}/${user.id}`, name: 'users.show', title: 'Lihat User', data: user, type: 'form' })">
                                    <td class="text-left px-2 py-1" x-text="user.name"></td>
                                    <td class="text-left px-2 py-1" x-text="user.email"></td>
                                    <td class="text-left px-2 py-1" x-text="user.no_hp"></td>
                                    {{-- <td class="text-left px-2 py-1" x-text="user.company_name"></td> --}}
                                    <td class="text-left px-2 py-1 text-center">
                                        <span x-text="user.profile" 
                                              :class="{
                                                'trial': 'inline-flex items-center rounded-lg bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800 shadow-sm ring-1 ring-inset ring-yellow-400',
                                                'standard': 'inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800 shadow-sm ring-1 ring-inset ring-blue-400',
                                                'pro': 'inline-flex items-center rounded-lg bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-800 shadow-sm ring-1 ring-inset ring-purple-400',
                                                'enterprise': 'inline-flex items-center rounded-lg bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800 shadow-sm ring-1 ring-inset ring-emerald-400'
                                              }[user.profile || 'enterprise']">
                                        </span>
                                    </td>
                                    <td class="text-left px-2 py-1 text-center">
                                        <span x-text="user.is_active == 1 ? 'Aktif' : 'Tidak'"
                                              :class="user.is_active == 1 
                                                ? 'inline-flex items-center px-3 py-1 rounded-lg bg-emerald-500 text-sm font-semibold text-white shadow-sm' 
                                                : 'inline-flex items-center px-3 py-1 rounded-lg bg-red-500 text-sm font-semibold text-white shadow-sm'">
                                        </span>
                                    </td>                                    
                                    <td class="text-left px-2 py-1 items-center text-center mt-1">
                                        <x-primary-button
                                            class="w-full md:w-auto lg:w-auto md:mt-0 mt-1"
                                            x-on:click.prevent.stop="$dispatch('open-modal', { route: `{{ route('users.update', '') }}/${user.id}`, name: 'users.update', title: 'Edit User', data: user, method: 'PUT', type: 'form' })"
                                        >{{ __('Edit') }}</x-primary-button>
                                        <x-primary-button
                                            class="w-full md:w-auto lg:w-auto md:mt-0 mt-1"
                                            x-on:click.prevent.stop="$dispatch('open-modal', { route: `{{ route('users.destroy', '') }}/${user.id}`, name: 'users.destroy', title: 'Hapus User', data: user, method: 'DELETE', type: 'delete' })"
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
            Alpine.data('userTable', () => ({
                currentPage: 1,
                rowsPerPage: 7,
                totalRows: 0,
                totalPage: 0,
                sortDirection: 'asc',
                filter: 'all',
                searchInput: '',
                allData: [],
                hover: false,
                selectedStatus: 'all',
                selectLangganan: 'all',
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
                async fetchUserData() {
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/users');
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
                        console.error('Error fetching User data:', error);
                        alert('Error fetching User data. Please try again later.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },
                renderUserTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchUserTable() {
                    this.renderUserTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderUserTable();
                },
                filteredData() {
                    return this.allData.filter(user => {
                        const matchesSearch = user.email.toLowerCase().includes(this.searchInput.toLowerCase()) || user.name.toLowerCase().includes(this.searchInput.toLowerCase()) || user.company_name.toLowerCase().includes(this.searchInput.toLowerCase());
                        const matchesStatus = this.filter === 'all' ? true : user.is_active === this.filter;
                        const matchesLangganan = this.selectLangganan === 'all' ? true : user.profile === this.selectLangganan;
                        return matchesSearch && matchesStatus && matchesLangganan;
                    });
                },
                handleStatusChange(status) {
                    console.log(status)
                    this.filter = status;
                    this.renderUserTable();
                },
                handleLanggananChange(langganan) {
                    console.log(langganan)
                    this.selectLangganan = langganan;
                    this.renderUserTable();
                },
                init() {
                    this.fetchUserData();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
