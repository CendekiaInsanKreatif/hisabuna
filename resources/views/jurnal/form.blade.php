<x-app-layout>
    @section('content')
        @php
            $field = ['no_urut_transaksi', 'jenis', 'keterangan'];
            $fieldSelect = [
                [
                    'name' => 'nomor_akun',
                    'type' => 'text',
                    'label' => 'Nomor Akun',
                    'required' => true,
                ],
                [
                    'name' => 'nama_akun',
                    'type' => 'text',
                    'label' => 'Nama Akun',
                    'required' => true,
                ],
            ];

            $currentRoute = request()->route()->getName();
        @endphp
        <x-modal :field="$fieldSelect" :data="$coa" maxWidth="lg" focusable />
        <div x-ref="alertError"
            class="alert-error hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
            style="margin-bottom: 10px;">
            <strong class="font-bold">Error!</strong>
            <span x-ref="error_message"></span>
        </div>
        @if ($currentRoute == 'jurnal.edit')
            <div x-data="jurnalApp()" x-init="init()">
                <div class="container mx-auto px-4">
                    <form action="{{ route('jurnal.update', $jurnal->id) }}" @submit.prevent="submitForm" method="post"
                        enctype="multipart/form-data" id="jurnalForm">
                        @csrf
                        @method('PUT')

                        <!-- Header Section -->
                        <div class="mb-6 flex justify-between items-center">
                            <h1 class="text-2xl font-semibold text-emerald-500">Edit Jurnal</h1>
                            <div class="flex items-center space-x-2">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                    Simpan Jurnal
                                </button>
                                <a href="{{ route('jurnal.index') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                    Batal
                                </a>
                            </div>
                        </div>

                        <!-- Form Card -->
                        <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-6">
                            <!-- Form Card -->
                            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-6">
                                <!-- Form Fields Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    @foreach ($field as $item)
                                        <div class="col-span-1">
                                            <label for="{{ $item }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ ucwords(str_replace('_', ' ', $item)) }}
                                                @if ($item != 'no_transaksi')
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>

                                            @if ($item == 'keterangan')
                                                <textarea name="{{ $item }}_header" id="{{ $item }}" x-ref="{{ $item }}"
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                    rows="3">{{ $jurnal->$item }}</textarea>
                                            @elseif ($item == 'jenis')
                                                <select name="{{ $item }}" id="{{ $item }}"
                                                    x-ref="{{ $item }}"
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                                    <option value="">Pilih Jenis</option>
                                                    <option value="rv" {{ $jurnal->$item == 'RV' ? 'selected' : '' }}>
                                                        Voucher Penerimaan | RV</option>
                                                    <option value="pv" {{ $jurnal->$item == 'PV' ? 'selected' : '' }}>
                                                        Voucher Pembayaran | PV</option>
                                                    <option value="jv" {{ $jurnal->$item == 'JV' ? 'selected' : '' }}>
                                                        Voucher Jurnal | JV</option>
                                                </select>
                                            @elseif ($item == 'no_urut_transaksi')
                                                <input type="text" name="{{ $item }}" id="{{ $item }}"
                                                    readonly value="{{ $jurnal->$item }}" placeholder="Generate By System"
                                                    class="mt-1 block w-full shadow-sm sm:text-sm bg-gray-50 border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                            @endif
                                        </div>
                                    @endforeach

                                    <!-- Tanggal Transaksi -->
                                    <div class="col-span-1">
                                        <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tanggal Transaksi<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="tanggal_transaksi" id="tanggal_transaksi"
                                            x-model="tanggal_transaksi"
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker"
                                            x-on:dblclick="setToday()">
                                    </div> <!-- Import Section -->
                                    <div class="col-span-1">
                                        <label for="lampiran" class="block text-sm font-medium text-gray-700 mb-1">
                                            Import Transaksi (File: .xlsx)
                                        </label>
                                        <div class="flex items-center gap-2 mt-1">
                                            <input type="file" id="importFile" name="file" accept=".xlsx"
                                                class="flex-1 file:bg-emerald-500 file:border-none file:rounded-md file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700 file:transition"
                                                @if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) disabled @endif>
                                            <button type="button" x-on:click="importJurnal"
                                                class="inline-flex items-center px-3 py-1.5 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-custom-strong whitespace-nowrap"
                                                @if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) disabled @endif>
                                                Import
                                            </button>
                                            <button type="button" x-on:click="downloadSample"
                                                class="inline-flex items-center px-3 py-1.5 bg-emerald-300 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-custom-strong whitespace-nowrap">
                                                Sample
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Table -->
                                <div class="mt-6 overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
                                    <!-- Detail Table -->
                                    <div class="mt-6 overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
                                        <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                                                <tr>
                                                    <th class="py-3 px-4 text-center">Akun</th>
                                                    <th class="py-3 px-4 text-center">Debit</th>
                                                    <th class="py-3 px-4 text-center">Kredit</th>
                                                    <th class="py-3 px-4 text-right">
                                                        <button type="button"
                                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-custom-strong"
                                                            x-on:click="rows.push({ no_akun: '', coa_akun: '', coa: {nama_akun: ''}, debit: '', kredit: '', keterangan: '' })">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            Tambah
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr class="bg-gray-100 border-t border-gray-300">
                                                    <th class="py-2 px-4 text-right font-semibold">
                                                        Total Debit: <span class="text-emerald-600"
                                                            x-text="totalDebit"></span>
                                                    </th>
                                                    <th class="py-2 px-4 text-center font-semibold">
                                                        Total Kredit: <span class="text-gray-600"
                                                            x-text="totalCredit"></span>
                                                    </th>
                                                    <th class="py-2 px-4 text-left font-semibold" colspan="2">
                                                        Selisih: <span class="text-red-600 font-bold" x-text="selisih"
                                                            id="selisih"></span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <template x-for="(row, index) in paginatedRows"
                                                :key="((currentPage - 1) * itemsPerPage) + index">
                                                <tbody class="bg-white border-b border-gray-200 hover:bg-gray-50"
                                                    id="tBody">
                                                    <tr>
                                                        <td class="py-3 px-4">
                                                            <div class="flex items-center gap-2">
                                                                <button type="button"
                                                                    class="inline-flex items-center justify-center px-2 py-1.5 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-custom-strong flex-shrink-0"
                                                                    x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: ((currentPage - 1) * itemsPerPage) + index })">
                                                                    Pilih
                                                                </button>
                                                                <input type="text"
                                                                    :name="'no_akun[' + (((currentPage - 1) * itemsPerPage) +
                                                                        index) + ']'"
                                                                    readonly required
                                                                    class="w-24 px-2 py-1.5 rounded-md shadow-sm bg-gray-100 border-gray-300 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                                    x-model="row.coa_akun" x-init="$watch('row.coa_akun', value => row.coa_akun = formatNomorAkun(value))">
                                                                <input type="text"
                                                                    :name="'nama_akun[' + (((currentPage - 1) * itemsPerPage) +
                                                                        index) + ']'"
                                                                    readonly required
                                                                    class="flex-1 px-2 py-1.5 rounded-md shadow-sm bg-gray-100 border-gray-300 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                                    x-model="row.nama_akun">
                                                            </div>
                                                        </td>
                                                        <td class="py-3 px-4">
                                                            <input type="text"
                                                                :name="'debit[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                class="w-full px-3 py-1.5 rounded-md shadow-sm border-gray-300 text-sm text-right focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                                x-model="row.debit"
                                                                x-on:input="formatCurrency($event, 'debit', ((currentPage - 1) * itemsPerPage) + index), updateTotals()">
                                                        </td>
                                                        <td class="py-3 px-4">
                                                            <input type="text"
                                                                :name="'kredit[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                class="w-full px-3 py-1.5 rounded-md shadow-sm border-gray-300 text-sm text-right focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                                x-model="row.kredit"
                                                                x-on:input="formatCurrency($event, 'kredit', ((currentPage - 1) * itemsPerPage) + index), updateTotals()">
                                                        </td>
                                                        <td class="py-3 px-4 text-right">
                                                            <button type="button"
                                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition shadow-custom-strong"
                                                                x-on:click="removeRow(index)">
                                                                <svg class="w-4 h-4 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-2 px-4" colspan="2">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                                Keterangan <span class="text-red-500">*</span>
                                                            </label>
                                                            <textarea
                                                                :name="'keterangan[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                class="w-full px-3 py-2 rounded-md shadow-sm border-gray-300 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                                rows="2" x-model="row.keterangan"
                                                                x-on:dblclick="setKeteranganToRow(((currentPage - 1) * itemsPerPage) + index)"
                                                                placeholder="Double-click untuk gunakan keterangan header"></textarea>
                                                        </td>
                                                        <td class="py-2 px-4">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                                Tanggal Bukti <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                :name="'tanggal_bukti[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                class="w-full px-3 py-1.5 rounded-md shadow-sm border-gray-300 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker"
                                                                @if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) disabled @endif
                                                                x-model="row.tanggal_bukti"
                                                                :x-ref="'tanggal_bukti_' + (((currentPage - 1) * itemsPerPage) +
                                                                    index)"
                                                                x-datepicker required>
                                                        </td>
                                                        <td class="py-2 px-4">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                                Lampiran
                                                            </label>
                                                            <input type="file"
                                                                :name="'lampiran[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                accept=".pdf,.jpg,.png,.jpeg"
                                                                class="w-full text-xs file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-xs file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700 file:transition"
                                                                multiple @if (auth()->user()->profile == 'trial' && auth()->user()->is_active == 0) disabled @endif>
                                                            <input type="hidden"
                                                                :name="'lampiran_path[' + (((currentPage - 1) * itemsPerPage) +
                                                                    index) + ']'"
                                                                x-model="row.lampiran">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </template>
                                        </table>

                                        <!-- Pagination Controls for Edit Page -->
                                        <div x-show="rows.length > itemsPerPage"
                                            class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 flex justify-between sm:hidden">
                                                    <button @click="previousPage()" :disabled="currentPage === 1"
                                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Previous
                                                    </button>
                                                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Next
                                                    </button>
                                                </div>
                                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                    <div>
                                                        <p class="text-sm text-gray-700">
                                                            Menampilkan
                                                            <span class="font-medium" x-text="showingFrom"></span>
                                                            sampai
                                                            <span class="font-medium" x-text="showingTo"></span>
                                                            dari
                                                            <span class="font-medium" x-text="rows.length"></span>
                                                            baris
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                                            aria-label="Pagination">
                                                            <button @click="previousPage()" :disabled="currentPage === 1"
                                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>

                                                            <template x-for="page in visiblePages" :key="page">
                                                                <button @click="goToPage(page)"
                                                                    :class="page === currentPage ?
                                                                        'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' :
                                                                        'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                                    x-text="page">
                                                                </button>
                                                            </template>

                                                            <button @click="nextPage()"
                                                                :disabled="currentPage === totalPages"
                                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                        </nav>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </form>
                </div>
            </div>
        @else
            <div x-data="jurnalApp()" x-init="init()">
                <div class="container mx-auto px-4">
                    <form action="{{ route('jurnal.store') }}" @submit.prevent="submitForm" method="post"
                        enctype="multipart/form-data" id="jurnalForm">
                        @csrf
                        @method('POST')
                        <div class="mb-6 flex justify-between items-center">
                            <p class="text-2xl font-semibold text-emerald-500">Buat Jurnal</p>
                            <div class="flex items-center space-x-2">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                    Simpan Jurnal
                                </button>
                                <a href="{{ route('jurnal.index') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong ml-2">
                                    Batal
                                </a>
                            </div>
                        </div>
                        <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                                @foreach ($field as $item)
                                    <div class="col-span-1">
                                        <label for="{{ $item }}"
                                            class="block text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $item)) }}
                                            @if ($item != 'no_transaksi')
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        @if ($item == 'keterangan')
                                            <textarea name="{{ $item }}_header" id="{{ $item }}" value="{{ old($item) }}"
                                                x-ref="{{ $item }}"
                                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                x-model="keteranganHeader"></textarea>
                                        @elseif ($item == 'jenis')
                                            <select name="{{ $item }}" id="{{ $item }}"
                                                value="{{ old($item) }}" x-ref="{{ $item }}"
                                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                                <option value="">Pilih Jenis</option>
                                                <option value="rv">Voucher Penerimaan | RV</option>
                                                <option value="pv">Voucher Pembayaran | PV</option>
                                                <option value="jv">Voucher Jurnal | JV</option>
                                            </select>
                                        @elseif ($item == 'no_urut_transaksi')
                                            <input type="text" name="{{ $item }}" id="{{ $item }}"
                                                value="{{ old($item) }}" readonly placeholder="Generate By System"
                                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                        @endif
                                    </div>
                                @endforeach
                                <div class="col-span-1">
                                    <label for="tanggal_transaksi"
                                        class="block text-sm font-medium text-gray-700 mt-5">Tanggal Transaksi<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="tanggal_transaksi" name="tanggal_transaksi" x-model="tanggal_transaksi"
                                        x-ref="tanggal_transaksi"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker"
                                        x-on:dblclick="setToday()">
                                </div>
                                <div class="col-span-1">
                                    <div class="flex flex-col space-y-2 w-full">
                                        <label class="block text-sm font-medium text-gray-700 mt-5">Import Transaksi (File:
                                            .xlsx)</label>
                                        <div class="flex justify-between items-center space-x-2">
                                            <input type="file" id="importFile" name="file" accept=".xlsx"
                                                value="{{ old('file') }}"
                                                class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                                            <button type="button" x-on:click="importJurnal"
                                                class="inline-flex items-center bg-emerald-500 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                                Import
                                            </button>
                                            <button type="button" x-on:click="downloadSample"
                                                class="inline-flex items-center bg-emerald-300 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                                Sample
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body overflow-x-auto mt-1 bg-white shadow-md rounded-lg p-1">
                                <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                                    <thead class="text-xs text-gray-700 uppercase text-center bg-gray-200">
                                        <tr>
                                            <th class="py-2 px-4">Akun</th>
                                            <th class="py-2 px-4">Debit</th>
                                            <th class="py-2 px-4">Kredit</th>
                                            <th class="py-2 px-4 text-right">
                                                <button type="button"
                                                    class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong"
                                                    x-on:click="addRow">
                                                    Tambah
                                                </button>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="py-2 px-4 text-right">
                                                <h4>Total Debit: <span class="text-green-500" x-text="totalDebit"></span>
                                                </h4>
                                            </th>
                                            <th class="py-2 px-4">
                                                <h4>Total Kredit: <span class="text-gray-500" x-text="totalCredit"></span>
                                                </h4>
                                            </th>
                                            <th class="py-2 px-4 text-left" colspan="2">
                                                <h4>Selisih: <span class="text-red-500" x-text="selisih"
                                                        id="selisih"></span></h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <template x-for="(row, index) in paginatedRows"
                                        :key="((currentPage - 1) * itemsPerPage) + index" id="myTemplate">
                                        <tbody class="bg-gray-100 text-center" id="tBody">
                                            <tr class="border-b">
                                                <td class="py-2 px-4 flex items-center space-x-2">
                                                    <button type="button"
                                                        class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong"
                                                        x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: ((currentPage - 1) * itemsPerPage) + index })">
                                                        Pilih
                                                    </button>
                                                    <input type="text"
                                                        :name="'no_akun[' + (((currentPage - 1) * itemsPerPage) + index) + ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                        x-model="row.no_akun" readonly required>
                                                    <input type="text"
                                                        :name="'nama_akun[' + (((currentPage - 1) * itemsPerPage) + index) +
                                                        ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                        x-model="row.nama_akun" readonly required>
                                                </td>
                                                <td class="py-2 px-4">
                                                    <input type="text"
                                                        :name="'debit[' + (((currentPage - 1) * itemsPerPage) + index) + ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 text-right"
                                                        x-model="row.debit"
                                                        x-on:input="formatCurrency($event, 'debit', ((currentPage - 1) * itemsPerPage) + index), updateTotals()"
                                                        required>
                                                </td>
                                                <td class="py-2 px-4">
                                                    <input type="text"
                                                        :name="'kredit[' + (((currentPage - 1) * itemsPerPage) + index) + ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 text-right"
                                                        x-model="row.kredit"
                                                        x-on:input="formatCurrency($event, 'kredit', ((currentPage - 1) * itemsPerPage) + index), updateTotals()"
                                                        required>
                                                </td>
                                                <td class="py-2 px-4">
                                                    <button type="button"
                                                        class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong"
                                                        x-on:click="removeRow(index)">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 px-2" colspan="2">
                                                    <label for="keterangan"
                                                        class="block text-sm font-medium text-gray-700">Keterangan&nbsp;<span
                                                            class="text-red-500">*</span></label>
                                                    <textarea :name="'keterangan[' + (((currentPage - 1) * itemsPerPage) + index) + ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                                        x-model="row.keterangan" x-on:dblclick="setKeteranganToRow(((currentPage - 1) * itemsPerPage) + index)"></textarea>
                                                </td>
                                                <td class="py-1 px-2">
                                                    <label class="block text-sm font-medium text-gray-700">Tanggal
                                                        Bukti&nbsp;<span class="text-red-500">*</span></label>
                                                    {{-- <input type="date" :name="'tanggal_bukti[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker" x-on:dblclick="setToday(index)" :x-ref="'tanggal_bukti_' + index"
                                            x-model="row.tanggal_bukti" required> --}}
                                                    <input type="text"
                                                        :name="'tanggal_bukti[' + (((currentPage - 1) * itemsPerPage) +
                                                            index) + ']'"
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker"
                                                        x-on:dblclick="setToday(((currentPage - 1) * itemsPerPage) + index)"
                                                        :x-ref="'tanggal_bukti_' + (((currentPage - 1) * itemsPerPage) + index)"
                                                        x-model="row.tanggal_bukti" x-datepicker readonly required>
                                                </td>
                                                <td class="py-1 px-1">
                                                    <label for="lampiran"
                                                        class="block text-sm font-medium text-gray-700">Lampiran</label>
                                                    <input style="width: 85px;" type="file" id="lampiran"
                                                        :name="'lampiran[' + index + ']'" accept=".pdf,.jpg,.png,.jpeg"
                                                        class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700"
                                                        x-model="row.lampiran" multiple>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </table>

                                <!-- Pagination Controls -->
                                <div x-show="rows.length > itemsPerPage"
                                    class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 flex justify-between sm:hidden">
                                            <button @click="previousPage()" :disabled="currentPage === 1"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Previous
                                            </button>
                                            <button @click="nextPage()" :disabled="currentPage === totalPages"
                                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Next
                                            </button>
                                        </div>
                                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-sm text-gray-700">
                                                    Menampilkan
                                                    <span class="font-medium" x-text="showingFrom"></span>
                                                    sampai
                                                    <span class="font-medium" x-text="showingTo"></span>
                                                    dari
                                                    <span class="font-medium" x-text="rows.length"></span>
                                                    baris
                                                </p>
                                            </div>
                                            <div>
                                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                                    aria-label="Pagination">
                                                    <button @click="previousPage()" :disabled="currentPage === 1"
                                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>

                                                    <template x-for="page in visiblePages" :key="page">
                                                        <button @click="goToPage(page)"
                                                            :class="page === currentPage ?
                                                                'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' :
                                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                            x-text="page">
                                                        </button>
                                                    </template>

                                                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endsection
    @push('script')
        <script type="text/javascript">
            function jurnalApp() {
                let jurnal = @json($jurnal->details ?? []);
                let coaList = @json($coa ?? []);
                console.log('Jurnal details:', jurnal);
                console.log('COA list:', coaList);
                let periode = @js(auth()->user()->periode);
                let jurnalTgl = @json($jurnal->jurnal_tgl ?? '');

                // Helper function to normalize account number (remove dashes)
                function normalizeAkun(akun) {
                    if (!akun) return '';
                    return akun.toString().replace(/-/g, '');
                }
                
                // Create a lookup map for COA by nomor_akun for quick access
                // Store both original and normalized versions for flexible lookup
                let coaMap = {};
                if (Array.isArray(coaList)) {
                    coaList.forEach(c => {
                        if (c.nomor_akun) {
                            // Store with original key
                            coaMap[c.nomor_akun] = c;
                            // Also store with normalized key (no dashes)
                            let normalized = normalizeAkun(c.nomor_akun);
                            coaMap[normalized] = c;
                        }
                    });
                }
                console.log('COA Map keys:', Object.keys(coaMap));

                // Extract date part if datetime format (remove time portion)
                if (jurnalTgl && jurnalTgl.includes(' ')) {
                    jurnalTgl = jurnalTgl.split(' ')[0];
                }

                // Ensure jurnal is an array
                if (!Array.isArray(jurnal)) {
                    jurnal = [];
                }

                jurnal.forEach(row => {
                    if (row.credit !== undefined) {
                        row.kredit = row.credit;
                        delete row.credit;
                    }

                    // Transform data structure for frontend compatibility
                    // Ensure coa_akun is set (this is the primary account number field)
                    if (!row.coa_akun && row.no_akun) {
                        row.coa_akun = row.no_akun;
                    }
                    
                    // Map coa_akun to no_akun for validation compatibility
                    if (row.coa_akun && !row.no_akun) {
                        row.no_akun = row.coa_akun;
                    }
                    
                    // Try to get nama_akun from multiple sources
                    if (!row.nama_akun) {
                        // First, try from coa relationship
                        if (row.coa && row.coa.nama_akun) {
                            row.nama_akun = row.coa.nama_akun;
                        }
                        // If coa relationship is null, try to get from coaMap
                        else if (row.coa_akun) {
                            // Try direct lookup first, then normalized lookup
                            let foundCoa = coaMap[row.coa_akun] || coaMap[normalizeAkun(row.coa_akun)];
                            if (foundCoa) {
                                row.nama_akun = foundCoa.nama_akun;
                                // Also populate the coa object for consistency
                                row.coa = foundCoa;
                                console.log('Found COA for', row.coa_akun, ':', foundCoa.nama_akun);
                            } else {
                                console.log('COA not found for:', row.coa_akun, 'normalized:', normalizeAkun(row.coa_akun));
                            }
                        }
                    }
                    
                    // Ensure coa object exists if coa_akun exists
                    if (row.coa_akun && !row.coa) {
                        row.coa = { nama_akun: row.nama_akun || '', nomor_akun: row.coa_akun };
                    }

                    console.log('Row after transform:', row.coa_akun, row.nama_akun);

                    // Ensure debit and kredit are numbers before formatting
                    if (typeof row.debit === 'number') {
                        row.debit = row.debit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    } else if (typeof row.debit === 'string' && row.debit !== '') {
                        // If already formatted, keep it
                        if (!row.debit.includes(',')) {
                            row.debit = parseFloat(row.debit.replace(/\./g, '')).toLocaleString('id-ID', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    }
                    
                    if (typeof row.kredit === 'number') {
                        row.kredit = row.kredit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    } else if (typeof row.kredit === 'string' && row.kredit !== '') {
                        // If already formatted, keep it
                        if (!row.kredit.includes(',')) {
                            row.kredit = parseFloat(row.kredit.replace(/\./g, '')).toLocaleString('id-ID', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    }
                });

                return {
                    keteranganHeader: '',
                    rows: Array.isArray(jurnal) ? jurnal : [],
                    totalDebit: 0,
                    totalCredit: 0,
                    selisih: 0,
                    errorMessage: '',
                    isValid: true,
                    isImport: false,
                    tanggal_transaksi: jurnalTgl,

                    // Pagination properties
                    currentPage: 1,
                    itemsPerPage: 50,
                    totalPages: 1,

                    get paginatedRows() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;
                        return this.rows.slice(start, end);
                    },

                    get showingFrom() {
                        if (this.rows.length === 0) return 0;
                        return ((this.currentPage - 1) * this.itemsPerPage) + 1;
                    },

                    get showingTo() {
                        const to = this.currentPage * this.itemsPerPage;
                        return to > this.rows.length ? this.rows.length : to;
                    },

                    updatePagination() {
                        this.totalPages = Math.ceil(this.rows.length / this.itemsPerPage);
                        if (this.currentPage > this.totalPages) {
                            this.currentPage = this.totalPages || 1;
                        }
                    },

                    goToPage(page) {
                        if (page >= 1 && page <= this.totalPages) {
                            this.currentPage = page;
                            // Scroll to top of table
                            document.getElementById('jurnalDetail')?.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    },

                    previousPage() {
                        if (this.currentPage > 1) {
                            this.goToPage(this.currentPage - 1);
                        }
                    },

                    nextPage() {
                        if (this.currentPage < this.totalPages) {
                            this.goToPage(this.currentPage + 1);
                        }
                    },

                    get visiblePages() {
                        const pages = [];
                        const maxVisible = 5;

                        let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
                        let end = Math.min(this.totalPages, start + maxVisible - 1);

                        if (end - start + 1 < maxVisible) {
                            start = Math.max(1, end - maxVisible + 1);
                        }

                        for (let i = start; i <= end; i++) {
                            pages.push(i);
                        }

                        return pages;
                    },

                    init() {
                        this.$errorElement = document.querySelector('[x-ref="alertError"]');
                        this.$errorMessageElement = document.querySelector('[x-ref="error_message"]');

                        if (jurnal.length > 0) {
                            this.keteranganHeader = jurnal[0].keterangan;
                        }

                        // Convert tanggal_transaksi from Y-m-d to d-m-Y format
                        if (this.tanggal_transaksi && this.tanggal_transaksi.includes('-')) {
                            const parts = this.tanggal_transaksi.split('-');
                            if (parts.length === 3 && parts[0].length === 4) {
                                // Format is Y-m-d, convert to d-m-Y
                                this.tanggal_transaksi = `${parts[2]}-${parts[1]}-${parts[0]}`;
                            }
                        }

                        Alpine.directive('datepicker', (el, {
                            expression
                        }, {
                            effect
                        }) => {
                            flatpickr(el, {
                                dateFormat: 'd-m-Y',
                                allowInput: true,
                                minDate: '01-01-' + periode,
                                maxDate: '31-12-' + periode,
                                onClose: function(selectedDates, dateStr, instance) {
                                    instance.setDate(dateStr, true);
                                    el.dispatchEvent(new Event('input'));
                                }.bind(this)
                            });

                            effect(() => {
                                flatpickr(el, {
                                    dateFormat: 'd-m-Y',
                                    allowInput: true,
                                    minDate: '01-01-' + periode,
                                    maxDate: '31-12-' + periode,
                                    onClose: function(selectedDates, dateStr, instance) {
                                        instance.setDate(dateStr, true);
                                        el.dispatchEvent(new Event('input'));
                                    }
                                });
                            });
                        });

                        // Initialize datepickers after a short delay to ensure DOM is ready
                        setTimeout(() => {
                            this.initializeDatePickers();
                        }, 100);
                        this.updateTotals();
                        
                        // Listen for coa-selected event from modal to update Alpine.js data
                        window.addEventListener('coa-selected', (event) => {
                            const { index, coa_akun, nama_akun, coa } = event.detail;
                            console.log('COA selected event received:', index, coa_akun, nama_akun);
                            
                            if (index !== undefined && index !== false && this.rows[index]) {
                                // Update the row data directly (this is what validation reads)
                                this.rows[index].coa_akun = coa_akun;
                                this.rows[index].no_akun = coa_akun;
                                this.rows[index].nama_akun = nama_akun;
                                this.rows[index].coa = coa;
                                
                                console.log('Updated row', index, ':', this.rows[index]);
                            }
                        });
                    },

                    initializeDatePickers() {
                        // Inisialisasi datepicker untuk input tanggal transaksi
                        // Use $nextTick to ensure DOM is ready
                        this.$nextTick(() => {
                            // Try using $refs first, fallback to getElementById
                            const datepickerTransaksi = this.$refs.tanggal_transaksi || document.getElementById('tanggal_transaksi');
                            if (datepickerTransaksi) {
                                // Check if flatpickr is already initialized
                                if (datepickerTransaksi._flatpickr) {
                                    datepickerTransaksi._flatpickr.destroy();
                                }
                                
                                const flatpickrOptions = {
                                    dateFormat: 'd-m-Y',
                                    allowInput: true,
                                    minDate: '01-01-' + periode,
                                    maxDate: '31-12-' + periode,
                                    onClose: function(selectedDates, dateStr, instance) {
                                        instance.setDate(dateStr, true);
                                        datepickerTransaksi.dispatchEvent(new Event('input'));
                                    }
                                };
                                
                                // Set default date only if tanggal_transaksi has value
                                if (this.tanggal_transaksi) {
                                    flatpickrOptions.defaultDate = this.tanggal_transaksi;
                                }
                                
                                flatpickr(datepickerTransaksi, flatpickrOptions);
                            }
                        });
                        this.rows.forEach((row, index) => {
                            if (row.tanggal_bukti) {
                                this.$nextTick(() => {
                                    const datepicker = document.querySelector(
                                        `[x-ref="tanggal_bukti_${index}"]`);
                                    if (datepicker) {
                                        flatpickr(datepicker, {
                                            dateFormat: 'd-m-Y',
                                            defaultDate: this.convertDateFormat(row.tanggal_bukti,
                                                'Y-m-d', 'd-m-Y'),
                                            allowInput: true,
                                            minDate: '01-01-' + periode,
                                            maxDate: '31-12-' + periode,
                                            onClose: function(selectedDates, dateStr, instance) {
                                                instance.setDate(dateStr, true);
                                                datepicker.dispatchEvent(new Event('input'));
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    },

                    formatNumber(number) {
                        return number.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    },

                    get formattedAkun() {
                        return this.formatNomorAkun(this.row.coa_akun);
                    },

                    // Fungsi untuk memformat nomor akun
                    formatNomorAkun(nomor_akun) {
                        let formatted = nomor_akun.replace(/\D/g, ''); // Hapus semua karakter non-digit
                        if (formatted.length > 6) {
                            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' + formatted.slice(5);
                        } else if (formatted.length > 4) {
                            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
                        } else {
                            formatted = formatted.slice(0, 3);
                        }
                        return formatted;
                    },

                    convertDateFormat(dateStr, fromFormat, toFormat) {
                        if (!dateStr) return '';

                        const fromParts = dateStr.split(fromFormat.includes('-') ? '-' : '/');
                        let day, month, year;

                        if (fromFormat === 'Y-m-d') {
                            year = fromParts[0];
                            month = fromParts[1];
                            day = fromParts[2];
                        } else if (fromFormat === 'd-m-Y') {
                            day = fromParts[0];
                            month = fromParts[1];
                            year = fromParts[2];
                        }

                        if (toFormat === 'd-m-Y') {
                            return `${String(day).padStart(2, '0')}-${String(month).padStart(2, '0')}-${year}`;
                        } else if (toFormat === 'Y-m-d') {
                            return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        }
                    },

                    setToday(index) {
                        const today = new Date();
                        const day = String(today.getDate()).padStart(2, '0');
                        const month = String(today.getMonth() + 1).padStart(2, '0');
                        const year = today.getFullYear();
                        const todayFormatted = `${day}-${month}-${year}`;

                        //this.rows[index].tanggal_bukti = todayFormatted;

                        this.$nextTick(() => {
                            const datepicker = this.$refs[`tanggal_bukti_${index}`];
                            if (datepicker) {
                                datepicker._flatpickr.setDate(todayFormatted, true);
                            }
                        });
                    },

                    formatDate(event, field, index) {
                        let value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
                        this.rows[index][field] = value;
                    },

                    // Tambahkan fungsi untuk mengupdate tanggal transaksi
                    updateTanggalBukti(index) {
                        if (this.tanggalTransaksi) {
                            this.rows[index].tanggal_bukti = this.tanggalTransaksi;
                            this.$nextTick(() => {
                                const datepicker = this.$refs[`tanggal_bukti_${index}`];
                                if (datepicker) {
                                    datepicker._flatpickr.setDate(this.tanggalTransaksi, true);
                                }
                            });
                        }
                    },

                    addRow() {
                        this.rows.push({
                            keterangan: this.keteranganHeader,
                            tanggal_bukti: this.tanggal_transaksi,
                            lampiran: '',
                            no_akun: '',
                            nama_akun: '',
                            debit: '',
                            kredit: ''
                        });

                        this.updatePagination();
                        this.updateTotals();
                        this.updateTanggalBukti(this.rows.length - 1);
                    },


                    removeRow(index) {
                        // Calculate actual index considering pagination
                        const actualIndex = ((this.currentPage - 1) * this.itemsPerPage) + index;
                        this.rows.splice(actualIndex, 1);
                        this.updatePagination();
                        this.updateTotals();
                    },

                    setKeteranganToRow(index) {
                        console.log(this.keteranganHeader)
                        this.rows[index].keterangan = this.keteranganHeader;;
                    },


                    updateTotals() {
                        this.totalDebit = this.rows.reduce((sum, row) => {
                            let debit = parseFloat((row.debit || '0').toString().replace(/\./g, '').replace(',',
                                '.')) || 0;
                            return sum + debit;
                        }, 0);
                        this.totalCredit = this.rows.reduce((sum, row) => {
                            let kredit = parseFloat((row.kredit || '0').toString().replace(/\./g, '').replace(',',
                                '.')) || 0;
                            return sum + kredit;
                        }, 0);
                        this.selisih = this.totalDebit - this.totalCredit;
                        this.totalDebit = this.totalDebit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                        this.totalCredit = this.totalCredit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                        this.selisih = this.selisih.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    },


                    formatCurrency(event, field, index) {
                        let value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
                        if (value === '') {
                            value = '0';
                        }
                        let parsedValue = parseFloat(value);
                        if (isNaN(parsedValue)) {
                            parsedValue = 0;
                        }
                        const formattedValue = parsedValue.toLocaleString('id-ID');
                        this.rows[index][field] = formattedValue;
                    },

                    importJurnal() {
                        const overlay = document.getElementById('overlay');
                        if (!overlay) {
                            alert('Loading overlay tidak ditemukan');
                            return;
                        }

                        // Get the loading text elements
                        const overlayTitle = overlay.querySelector('h3');
                        const overlaySubtext = overlay.querySelector('p');

                        overlay.style.display = 'flex';

                        // Update loading text
                        if (overlayTitle) overlayTitle.textContent = 'Memproses Import Jurnal';
                        if (overlaySubtext) overlaySubtext.textContent = 'Mohon tunggu, sedang membaca file...';

                        let getFile = document.getElementById('importFile').files;
                        if (getFile.length === 0) {
                            document.getElementById('importFile').focus();
                            alert('Silakan pilih file untuk diimport.');
                            overlay.style.display = 'none';
                            return;
                        }

                        let formData = new FormData();
                        formData.append('file', getFile[0]);

                        // Add timeout for large files
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 120000); // 2 minutes timeout

                        fetch('{{ route('jurnal.import.html') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                signal: controller.signal
                            })
                            .then(response => {
                                clearTimeout(timeoutId);
                                return response.json();
                            })
                            .then(data => {
                                if (data.html === 0) {
                                    alert(data.message);
                                    overlay.style.display = 'none';
                                    return;
                                } else {
                                    if (data.html && data.html.length > 0) {
                                        if (overlayTitle) overlayTitle.textContent = 'Memproses Data';
                                        if (overlaySubtext) overlaySubtext.textContent = 'Memproses ' + data.html.length +
                                            ' baris data...';

                                        // Process in batches to prevent UI freeze
                                        const batchSize = 50;
                                        let currentBatch = 0;

                                        const processBatch = () => {
                                            const start = currentBatch * batchSize;
                                            const end = Math.min(start + batchSize, data.html.length);

                                            // Add batch to rows
                                            for (let i = start; i < end; i++) {
                                                this.rows.push(data.html[i]);
                                            }

                                            currentBatch++;

                                            // Update progress
                                            const progress = Math.round((end / data.html.length) * 100);
                                            if (overlayTitle) overlayTitle.textContent = 'Memproses Data (' + progress +
                                                '%)';
                                            if (overlaySubtext) overlaySubtext.textContent = end + ' dari ' + data.html
                                                .length + ' baris telah diproses';

                                            if (end < data.html.length) {
                                                // Process next batch asynchronously
                                                setTimeout(processBatch, 10);
                                            } else {
                                                // All batches processed
                                                if (overlayTitle) overlayTitle.textContent = 'Menyelesaikan Import';
                                                if (overlaySubtext) overlaySubtext.textContent =
                                                    'Sedang menghitung total...';

                                                // Small delay to show completion message
                                                setTimeout(() => {
                                                    this.isImport = true;
                                                    this.updatePagination();
                                                    this.updateTotals();
                                                    this.importUpdate();
                                                    alert(data.message);
                                                    overlay.style.display = 'none';

                                                    // Reset text for next use
                                                    if (overlayTitle) overlayTitle.textContent = 'Memuat Data';
                                                    if (overlaySubtext) overlaySubtext.textContent =
                                                        'Harap tunggu sebentar...';
                                                }, 300);
                                            }
                                        };

                                        // Start batch processing
                                        processBatch();

                                    } else {
                                        alert('Terjadi kesalahan saat mengimpor jurnal.');
                                        overlay.style.display = 'none';
                                    }
                                }
                            })
                            .catch(error => {
                                clearTimeout(timeoutId);
                                this.isValid = false;

                                if (error.name === 'AbortError') {
                                    this.errorMessage = 'Import timeout. File terlalu besar atau koneksi lambat.';
                                    alert(
                                        'Import timeout. File terlalu besar atau koneksi lambat. Silakan coba dengan file yang lebih kecil.'
                                    );
                                } else {
                                    this.errorMessage = 'Terjadi kesalahan saat mengimpor jurnal: ' + error.message;
                                    alert('Terjadi kesalahan saat mengimpor jurnal.');
                                }

                                if (this.$refs.errorElement) {
                                    this.$refs.errorElement.removeAttribute('hidden');
                                    setTimeout(() => {
                                        this.$refs.errorElement.setAttribute('hidden', true);
                                    }, 3000);
                                }

                                overlay.style.display = 'none';

                                // Reset text for next use
                                if (overlayTitle) overlayTitle.textContent = 'Memuat Data';
                                if (overlaySubtext) overlaySubtext.textContent = 'Harap tunggu sebentar...';
                            });
                    },

                    importUpdate() {
                        this.totalDebit = 0;
                        this.totalCredit = 0;
                        for (let i = 0; i < this.rows.length; i++) {
                            this.totalDebit += parseFloat(this.rows[i].debit) || 0;
                            this.totalCredit += parseFloat(this.rows[i].kredit) || 0;

                            this.rows[i].debit = this.formatNumber(this.rows[i].debit);
                            this.rows[i].kredit = this.formatNumber(this.rows[i].kredit);
                        }
                        this.initializeDatePickers();
                        this.selisih = this.totalDebit - this.totalCredit;

                        this.totalDebit = this.totalDebit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                        this.totalCredit = this.totalCredit.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                        this.selisih = this.selisih.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    },

                    downloadSample() {
                        fetch('{{ route('jurnal.sample.export') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                                }
                            })
                            .then(response => response.blob())
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download = 'jurnal_sample.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                            })
                            .catch(error => console.error('Error:', error));
                    },

                    validateForm() {
                        this.isValid = true;
                        this.errorMessage = '';

                        if (this.$refs.jenis && this.$refs.jenis.value.trim() === '') {
                            this.isValid = false;
                            this.errorMessage += 'Jenis harus diisi.<br>';
                        }

                        if (this.$refs.keterangan && this.$refs.keterangan.value.trim() === '') {
                            this.isValid = false;
                            this.errorMessage += 'Keterangan Header harus diisi.<br>';
                        }

                        if (this.rows.length === 0) {
                            this.isValid = false;
                            this.errorMessage += 'Detail jurnal tidak boleh kosong.<br>';
                        }

                        if (this.rows.length === 1) {
                            this.isValid = false;
                            this.errorMessage += 'Masukan Detail Pembanding.<br>';
                        }

                        let totalDebit = 0;
                        let totalCredit = 0;

                        // console.log(this.rows);
                        // Validate ALL rows (not just paginated ones)
                        this.rows.forEach((row, actualIndex) => {
                            // Use row data directly instead of DOM elements
                            // Support both formats: no_akun/nama_akun and coa_akun/coa.nama_akun
                            let no_akun = row.no_akun || row.coa_akun || '';
                            let nama_akun = row.nama_akun || (row.coa && row.coa.nama_akun) || '';

                            // Convert formatted numbers to float
                            let debitValue = typeof row.debit === 'string' ?
                                parseFloat(row.debit.replace(/\./g, '').replace(',', '.')) :
                                parseFloat(row.debit);
                            let kreditValue = typeof row.kredit === 'string' ?
                                parseFloat(row.kredit.replace(/\./g, '').replace(',', '.')) :
                                parseFloat(row.kredit);

                            if (isNaN(debitValue)) debitValue = 0;
                            if (isNaN(kreditValue)) kreditValue = 0;

                            // Check if at least one of debit or kredit has a value (not both zero)
                            // In double-entry bookkeeping, each row should have either debit OR kredit, not necessarily both
                            if (debitValue === 0 && kreditValue === 0) {
                                this.isValid = false;
                                this.errorMessage +=
                                    `Debit atau kredit pada baris ${actualIndex + 1} harus diisi.<br>`;
                            }

                            if (no_akun === '' || nama_akun === '') {
                                this.isValid = false;
                                this.errorMessage +=
                                    `No Akun atau Nama Akun pada baris ${actualIndex + 1} harus diisi.<br>`;
                            }

                            totalDebit += debitValue;
                            totalCredit += kreditValue;
                        });

                        // Check balance
                        const selisih = Math.abs(totalDebit - totalCredit);
                        if (selisih > 0.01) { // Allow small floating point differences
                            this.isValid = false;
                            this.errorMessage += 'Total debit dan kredit harus seimbang.<br>';
                        }

                        if (!this.isValid) {
                            this.$errorMessageElement.innerHTML = this.errorMessage;
                            this.$errorElement.classList.remove('hidden');
                            setTimeout(() => {
                                this.$errorElement.classList.add('hidden');
                            }, 5000);
                        }

                        return this.isValid;
                    },

                    submitForm(event) {
                        const overlay = document.getElementById('overlay');
                        overlay.style.display = 'flex';

                        if (!this.validateForm()) {
                            event.preventDefault();
                            overlay.style.display = 'none';
                        } else {
                            event.target.submit();
                        }
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
