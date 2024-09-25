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
        <x-modal :field="$fields" focusable />
        <div class="container mx-auto px-4" x-data="jurnalTable">
            <div class="mb-4 mt-2">
                <p class="text-2xl text-emerald-500">Jurnal</p>
            </div>
            <div class="card bg-white rounded-xl border border-gray-200">
                <div class="flex flex-wrap items-center justify-between">
                    <div id="filterAkun" class="flex-grow rounded p-3 flex flex-wrap gap-2 w-full md:w-auto justify-between">
                        <div class="flex flex-wrap gap-2 h-fit">
                            <button @click="filterCategory('all')" class="btn-akun btn-filter bg-gray-200">Semua</button>
                            <button @click="filterCategory('rv')" class="btn-akun btn-filter">RV</button>
                            <button @click="filterCategory('pv')" class="btn-akun btn-filter">PV</button>
                            <button @click="filterCategory('jv')" class="btn-akun btn-filter">JV</button>
                            <div class="relative w-full md:w-auto flex-grow md:flex-grow-0">
                                <input type="text" id="cari" x-model="searchInput"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 w-full px-3 h-full"
                                    style="width: 600px" placeholder="Cari Jurnal . . ." @keydown.enter="searchJurnalTable">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- <a href="{{ route('report.daftarjurnal') }}" class="btn btn-filter">
                                <p style="line-height: 1.5;">Daftar Jurnal</p>
                            </a> -->
                            <!-- Button -->
                            <button id="detail" type="button" class="bg-emerald-500 hover:bg-emerald-700 text-base text-white py-2 px-4 rounded" onclick="toggleModal('exampleModal')">
                                Daftar Jurnal
                            </button>

                            <!-- Modal -->
                           
                        <div class="fixed z-10 inset-0 hidden overflow-y-auto bg-gray-900 bg-opacity-50" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="bg-white rounded-lg shadow-lg w-full max-w-md relative">
                                    <div class="px-4 py-2 border-b border-gray-300 flex justify-between items-center">
                                        <h5 class="text-lg font-medium" id="exampleModalLabel">Jurnal Detail</h5>
                                        <button type="button" class="text-gray-500 hover:text-gray-700" onclick="toggleModal('exampleModal')" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex flex-col">
                                            <label for="" class="mb-1">Total Jurnal</label>
                                            <input type="text" id="total-jurnal" class="border border-gray-300 rounded p-2" readonly>
                                        </div>
                                        <div class="flex space-x-4">
                                            <div class="flex flex-col">
                                                <label for="" class="mb-1">Dari Jurnal Ke</label>
                                                <input id="dari" type="number" class="border border-gray-300 rounded p-2">
                                            </div>
                                            <div class="flex flex-col">
                                                <label for="" class="mb-1">Sampai Jurnal Ke</label>
                                                <input id="sampai" type="number" class="border border-gray-300 rounded p-2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-300 flex justify-end">
                                        <button id="print" type="button" class="ml-2 bg-emerald-500 text-white py-2 px-4 rounded hover:bg-emerald-700">Print</button>
                                    </div>
                                    <a id="downloadLink" href="#" style="display: none;">Download PDF</a>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->profile == 'trial' && auth()->user()->is_active == 1)
                            <a role="button"
                                class="text-base py-2 px-4 inline-flex items-center justify-center bg-emerald-500 border border-transparent rounded-md text-white hover:bg-emerald-700"
                                href="{{ route('jurnal.create') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 28 28" class="mr-2">
                                    <path fill="white"
                                        d="M14 5a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H6a1 1 0 1 1 0-2h7V6a1 1 0 0 1 1-1z" />
                            </svg>Tambah Jurnal</a>
                        @endif
                    </div>
                </div>
                <div class="card-body overflow-x-auto">
                    <table class="w-full min-w-full" id="jurnalTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer"
                                    style="font-weight: 400; line-height: 1; width: 132px;">
                                    <div class="flex items-center text-left">
                                        No. Transaksi
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon"
                                                data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer"
                                    style="font-weight: 400; line-height: 1; width: 132px;">
                                    <div class="flex items-center">
                                        Jenis Jurnal
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon"
                                                data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer"
                                    style="font-weight: 400; line-height: 1;">
                                    <div class="flex items-center">
                                        Keterangan
                                        <span class="ml-2">
                                            <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon"
                                                data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm  text-gray-500  cursor-pointer text-center"
                                    style="font-weight: 400; width: 50px;">
                                    <div class="flex items-center justify-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="jurnalTableBody">
                            <template x-for="(jurnal, index) in paginatedData" :key="jurnal.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false" class="hover:bg-gray-100">
                                    <td class="text-left  px-3 py-2" x-text="jurnal.no_urut_transaksi"></td>
                                    <td class="text-center  " x-text="jurnal.jenis"></td>
                                    <td class="text-left px-3  break-words" x-text="jurnal.keterangan"></td>
                                    <td class="text-left  px-3 py-2">
                                        <div class="flex items-center flex-col md:flex-row gap-2">
                                            <a class="btn btn-action-primary"
                                                :href="`{{ url('jurnal') }}/${jurnal.id}/edit`">
                                                Edit
                                            </a>

                                            <a class="btn cursor-pointer btn-action-secondary" x-data="{ data: jurnal }"
                                                x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('jurnal.show', '') }}/${jurnal.id}`, name: 'jurnal.show', title: 'Lihat Jurnal', data: jurnal, type: 'form' })">{{ __('View') }}</a>
                                            <a :href="`{{ route('report.transaksi', '') }}/${jurnal.id}`" target="_blank"
                                                class="btn btn-action-secondary">Print</a>

                                            {{-- <x-primary-button class="w-full md:w-auto"
                                                x-data="{data: jurnal}"
                                                x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('jurnal.show', '') }}/${jurnal.id}`, name: 'jurnal.show', title: 'Lihat Jurnal', data: jurnal, type: 'form' })"
                                            >{{ __('View') }}</x-primary-button> --}}

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
                                <button @click="changePage(page)"
                                    :class="{
                                        'bg-emerald-600 text-white': page === currentPage,
                                        'bg-gray-200': page !==
                                            currentPage
                                    }"
                                    class="page-number py-1 px-3 rounded" x-text="page"></button>
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
            $(document).ready(function() {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log('CSRF Token:', csrfToken);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                $('#print').click(function(e) {
                    e.preventDefault();

                    var a = $('#dari').val();
                    var b = $('#sampai').val();

                    if (a && b) {
                        $.ajax({
                            url: '{{ route("printReport") }}', // Sesuaikan dengan route yang benar
                            type: 'POST',
                            data: {
                                a: a,
                                b: b,
                                _token: '{{ csrf_token() }}' // Token CSRF
                            },
                            xhrFields: {
                                responseType: 'blob' // Menangani respons sebagai file PDF (blob)
                            },
                            success: function(response) {
                                var blob = new Blob([response], { type: 'application/pdf' });
                                var url = window.URL.createObjectURL(blob);
                                var a = document.createElement('a');
                                a.href = url;
                                a.download = 'daftar_jurnal.pdf'; // Nama file PDF
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    } else {
                        alert('Isi semua data!');
                    }
                });
            });
        </script>
        <script>
            function toggleModal(modalID) {
                const modal = document.getElementById(modalID);
                modal.classList.toggle('hidden');
            }

            // const csrfToken = $('meta[name="csrf-token"]').attr('content');
            // console.log('CSRF Token:', csrfToken);

            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': csrfToken
            //     }
            // });

            // $('#print').click(function(e) {
            //     e.preventDefault();
            //     var a = $('#dari').val();
            //     var b = $('#sampai').val();
            //     $.post('printReport',{a:a,b:b}).done((res,status,xhr)=> {

            //     })
            // })

            $('#detail').click(function(e) {
                e.preventDefault();
                $.get('totalJurnal').done((res,status,xhr)=> {
                    $('#total-jurnal').val(res.data)
                })
            })

            async function cekTrial() {
                await $.get('cekTrial').done((res, status, xhr) => {
                    var data = res.data;
                })
            }
            cekTrial();

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
                        return Array.from({
                            length: endPage - startPage + 1
                        }, (_, i) => startPage + i);
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
                                alert(
                                    'Error: Unexpected data format. Please check the data returned from the server.'
                                );
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
                            const matchesCategory = (this.filter === 'all' || jurnal.jenis
                                .toLowerCase() === this.filter.toLowerCase());
                            const matchesSearch = jurnal.keterangan.toLowerCase().includes(this
                                .searchInput.toLowerCase());
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
