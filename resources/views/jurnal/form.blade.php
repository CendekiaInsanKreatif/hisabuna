<x-app-layout>
    @section('content')
    @php
        $field = ['no_transaksi', 'jenis', 'keterangan'];
        $fieldSelect = [
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
            ];

            $currentRoute = request()->route()->getName();
    @endphp
    <x-modal :field="$fieldSelect" :data="$coa" maxWidth="lg" focusable />
    <div x-ref="alertError" class="alert-error hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Error!</strong>
        <span x-ref="error_message"></span>
    </div>
    @if ($currentRoute == 'jurnal.edit')
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <form action="{{ route('jurnal.update', $jurnal->id) }}" @submit.prevent="submitForm" method="post" enctype="multipart/form-data" id="jurnalForm">
                @csrf
                @method('PUT')
            <div class="mb-6 flex justify-between items-center">
                <p class="text-2xl font-semibold text-emerald-500">Edit Jurnal</p>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Simpan Jurnal
                    </button>

            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    @foreach ($field as $item)
                    <div class="col-span-1">
                        <label for="{{ $item }}" class="block text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                        @if ($item == 'keterangan')                        
                        <textarea name="{{ $item }}_header" id="{{ $item }}" x-ref="{{ $item }}"
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" 
                                  value="{{ $jurnal->$item }}">{{ $jurnal->$item }}</textarea>
                        @elseif ($item == 'jenis')
                            <select name="{{ $item }}" id="{{ $item }}" x-ref="{{ $item }}"
                                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <option value="">Pilih Jenis</option>
                                <option value="rv" {{ $jurnal->$item == 'RV' ? 'selected' : '' }}>Voucher Penerimaan | RV</option>
                                <option value="pv" {{ $jurnal->$item == 'PV' ? 'selected' : '' }}>Voucher Pembayaran | PV</option>
                                <option value="jv" {{ $jurnal->$item == 'JV' ? 'selected' : '' }}>Voucher Jurnal | JV</option>
                            </select>
                        @elseif ($item == 'no_transaksi')
                            <input type="text" name="{{ $item }}" id="{{ $item }}" readonly value="{{ $jurnal->$item }}" placeholder="Generate By System" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                        @endif
                    </div>
                    @endforeach
                    <div class="col-span-1">
                        <div class="flex flex-col space-y-2 w-full">
                            <label for="lampiran" class="block text-sm font-medium text-gray-700 mt-5">Import Transaksi (File: .xlsx)</label>
                            <div class="flex justify-between items-center space-x-2">
                                <input type="file" id="importFile" name="file" accept=".xlsx"
                                    class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                                <button type="button" x-on:click="importJurnal"
                                        class="inline-flex items-center bg-emerald-500 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                        Import
                                </button>
                                <button type="button" x-on:click="downloadSample" class="inline-flex items-center bg-emerald-300 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
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
                                    <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="rows.push({ no_akun: '', nama_akun: '', debit: '', kredit: '', keterangan: '' })">
                                        Tambah
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th class="py-2 px-4 text-right">
                                    <h4>Total Debit: <span class="text-green-500" x-text="totalDebit"></span></h4>
                                </th>
                                <th class="py-2 px-4">
                                    <h4>Total Kredit: <span class="text-gray-500" x-text="totalCredit"></span></h4>
                                </th>
                                <th class="py-2 px-4 text-left" colspan="2">
                                    <h4>Selisih: <span class="text-red-500" x-text="selisih" id="selisih"></span></h4>
                                </th>
                            </tr>
                        </thead>
                        <template x-for="(row, index) in rows" :key="index">
                        <tbody class="bg-gray-100 text-center" id="tBody">
                                <tr class="border-b">
                                    <td class="py-2 px-4 flex items-center">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                            Pilih
                                        </button>
                                        <input type="text" :name="'no_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" x-model="row.coa_akun">
                                        <input type="text" :name="'nama_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.coa.nama_akun">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit" x-on:input="formatCurrency($event, 'debit', index)">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit" x-on:input="formatCurrency($event, 'kredit', index)">
                                    </td>
                                    <td class="py-2 px-4">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(index, 1)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 px-2" colspan="2">
                                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                        <textarea :name="'keterangan[' + index + ']'" 
                                                    class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" 
                                                    x-model="row.keterangan" 
                                                    x-on:dblclick="setKeteranganToRow(index)"></textarea>
                                    </td>
                                    <td class="py-1 px-2">
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Bukti</label>
                                        <input type="text" :name="'tanggal_bukti[' + index + ']'" 
                                                        class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker" 
                                                        x-model="row.tanggal_bukti" 
                                                        x-datepicker required>
                                    </td>
                                    <td class="py-1 px-1">
                                        <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                                        <input style="width: 85px;" type="file" id="lampiran" :name="'lampiran[' + index + ']'" accept=".pdf,.jpg,.png,.jpeg" class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700" multiple>
                                        <input type="hidden" :name="'lampiran_path[' + index + ']'" x-model="row.lampiran">
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </table>
                </div>
            </div>
            </form>
        </div>
    </div>
    @else
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <form action="{{ route('jurnal.store') }}" @submit.prevent="submitForm" method="post" enctype="multipart/form-data" id="jurnalForm">
                @csrf
                @method('POST')
            <div class="mb-6 flex justify-between items-center">
                <p class="text-2xl font-semibold text-emerald-500">Buat Jurnal</p>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Simpan Jurnal
                    </button>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    @foreach ($field as $item)
                    <div class="col-span-1">
                        <label for="{{$item}}" class="block text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                        @if ($item == 'keterangan')
                            <textarea name="{{$item}}_header" id="{{$item}}" value="{{old($item)}}" x-ref="{{$item}}"
                                      class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                      x-model="keteranganHeader"></textarea>
                        @elseif ($item == 'jenis')
                            <select name="{{$item}}" id="{{$item}}" value="{{old($item)}}" x-ref="{{$item}}"
                                      class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <option value="">Pilih Jenis</option>
                                <option value="rv">Voucher Penerimaan | RV</option>
                                <option value="pv">Voucher Pembayaran | PV</option>
                                <option value="jv">Voucher Jurnal | JV</option>
                            </select>
                        @elseif ($item == 'no_transaksi')
                            <input type="text" name="{{$item}}" id="{{$item}}" value="{{old($item)}}" readonly placeholder="Generate By System" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                        @endif
                    </div>
                    @endforeach
                    <div class="col-span-1">
                        <div class="flex flex-col space-y-2 w-full">
                            <label class="block text-sm font-medium text-gray-700 mt-5">Import Transaksi (File: .xlsx)</label>
                            <div class="flex justify-between items-center space-x-2">
                                <input type="file" id="importFile" name="file" accept=".xlsx" value="{{old('file')}}"
                                    class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                                <button type="button" x-on:click="importJurnal"
                                        class="inline-flex items-center bg-emerald-500 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                                        Import
                                </button>
                                <button type="button" x-on:click="downloadSample" class="inline-flex items-center bg-emerald-300 px-2 py-1 justify-center border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
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
                                            class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" 
                                            x-on:click="addRow">
                                        Tambah
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th class="py-2 px-4 text-right">
                                    <h4>Total Debit: <span class="text-green-500" x-text="totalDebit"></span></h4>
                                </th>
                                <th class="py-2 px-4">
                                    <h4>Total Kredit: <span class="text-gray-500" x-text="totalCredit"></span></h4>
                                </th>
                                <th class="py-2 px-4 text-left" colspan="2">
                                    <h4>Selisih: <span class="text-red-500" x-text="selisih" id="selisih"></span></h4>
                                </th>
                            </tr>
                        </thead>
                        <template x-for="(row, index) in rows" :key="index">
                        <tbody class="bg-gray-100 text-center" id="tBody">
                                    <tr class="border-b">
                                        <td class="py-2 px-4 flex items-center space-x-2">
                                            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                                Pilih
                                            </button>
                                            <input type="text" :name="'no_akun[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.no_akun" readonly required>
                                            <input type="text" :name="'nama_akun[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.nama_akun" readonly required>
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit" x-on:input="formatCurrency($event, 'debit', index), updateTotals()" required>
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit" x-on:input="formatCurrency($event, 'kredit', index), updateTotals()" required>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="removeRow(index)">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 px-2" colspan="2">
                                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                            <textarea :name="'keterangan[' + index + ']'" 
                                                    class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" 
                                                    x-model="row.keterangan" 
                                                    x-on:dblclick="setKeteranganToRow(index)"></textarea>
                                        </td>
                                        <td class="py-1 px-2">
                                            <label class="block text-sm font-medium text-gray-700">Tanggal Bukti</label>
                                            <input type="text" :name="'tanggal_bukti[' + index + ']'" 
                                                    class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker" 
                                                    x-model="row.tanggal_bukti" x-datepicker readonly required>                                            
                                        </td>
                                        <td class="py-1 px-1">
                                            <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                                            <input style="width: 85px;" type="file" id="lampiran" :name="'lampiran[' + index + ']'" accept=".pdf,.jpg,.png,.jpeg" class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700" x-model="row.lampiran" multiple>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
@push('script')
<script>
    function jurnalApp() {
        let jurnal = @json($jurnal->details ?? []);

        jurnal.forEach(row => {
            if(row.credit !== undefined){
                row.kredit = row.credit;
                delete row.credit;
            }
        });

        console.log(jurnal)

        return {
            keteranganHeader: '',
            rows: Array.isArray(jurnal) ? jurnal : [],
            totalDebit: 0,
            totalCredit: 0,
            selisih: 0,
            errorMessage: '',
            isValid: true,

            init() {
                this.$errorElement = document.querySelector('[x-ref="alertError"]');
                this.$errorMessageElement = document.querySelector('[x-ref="error_message"]');

                Alpine.directive('datepicker', (el, { expression }, { effect }) => {
                    $(el).datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        showButtonPanel: true,
                        onClose: function (dateText, inst) {
                            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                            el.dispatchEvent(new Event('input'));
                        }
                    });

                    effect(() => {
                        $(el).datepicker('destroy').datepicker({
                            dateFormat: 'dd/mm/yy',
                            changeMonth: true,
                            changeYear: true,
                            showButtonPanel: true,
                            onClose: function (dateText, inst) {
                                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                                el.dispatchEvent(new Event('input'));
                            }
                        });
                    });
                });
            },

            addRow() {
                this.rows.push({
                    keterangan: this.keteranganHeader,
                    tanggal_bukti: '',
                    lampiran: '',
                    no_akun: '', 
                    nama_akun: '', 
                    debit: '', 
                    kredit: ''
                });

                this.updateTotals();
            },

            removeRow(index) {
                this.rows.splice(index, 1);
                this.updateTotals();
            },

            setKeteranganToRow(index) {
                this.rows[index].keterangan = this.keteranganHeader;;
            },

            
            updateTotals() {
                this.totalDebit = this.rows.reduce((sum, row) => {
                    const debit = parseFloat(row.debit.replace(/\./g, '').replace(',', '.')) || 0;
                    return sum + debit;
                }, 0);
                this.totalCredit = this.rows.reduce((sum, row) => {
                    const kredit = parseFloat(row.kredit.replace(/\./g, '').replace(',', '.')) || 0;
                    return sum + kredit;
                }, 0);

                this.selisih = this.totalDebit - this.totalCredit;
                this.totalDebit = this.totalDebit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                this.totalCredit = this.totalCredit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                this.selisih = this.selisih.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
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
                let getFile = document.getElementById('importFile').files;
                console.log(getFile);
                let formData = new FormData();
                formData.append('file', getFile[0]);
                fetch('{{ route('jurnal.import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.rows.forEach(row => {
                            this.rows.push(row);
                        });
                    } else {
                        console.error('Failed to import:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
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

                if (this.$refs.jenis.value.trim() === '') {
                    this.isValid = false;
                    this.errorMessage += 'Jenis harus diisi.<br>';
                }

                if (this.$refs.keterangan.value.trim() === '') {
                    this.isValid = false;
                    this.errorMessage += 'Keterangan Header harus diisi.<br>';
                }

                if (this.rows.length === 0) {
                    this.isValid = false;
                    this.errorMessage += 'Detail jurnal tidak boleh kosong.<br>';
                }

                let totalDebit = 0;
                let totalCredit = 0;

                this.rows.forEach((row, index) => {
                    if (row.tanggal_bukti.trim() === '' || (parseFloat(row.debit) === 0 && parseFloat(row.kredit) === 0)) {
                        this.isValid = false;
                        if (row.tanggal_bukti.trim() === '') this.errorMessage += `Tanggal bukti pada baris ${index + 1} harus diisi.<br>`;
                        if (parseFloat(row.debit) === 0 && parseFloat(row.kredit) === 0) this.errorMessage += `Debit atau kredit pada baris ${index + 1} harus diisi.<br>`;
                    }

                    totalDebit += parseFloat(row.debit) || 0;
                    totalCredit += parseFloat(row.kredit) || 0;
                });

                console.log(totalDebit)
                console.log(totalCredit)
                console.log(this.rows)

                if(this.rows.length === 1){
                    this.isValid = false;
                    this.errorMessage += 'Masukan Detail Pembanding.<br>';
                }

                if (totalDebit !== totalCredit) {
                    this.isValid = false;
                    this.errorMessage += 'Total debit dan kredit harus seimbang.<br>';
                }

                if (!this.isValid) {
                    this.$errorMessageElement.innerHTML = this.errorMessage;
                    this.$errorElement.classList.remove('hidden');
                    setTimeout(() => {
                        this.$errorElement.classList.add('hidden');
                    }, 3000);
                }

                return this.isValid;
            },

            submitForm(event) {
                if (!this.validateForm()) {
                    event.preventDefault();
                }else{
                    event.target.submit();
                }
            }
        };
    }
</script>
@endpush
</x-app-layout>
