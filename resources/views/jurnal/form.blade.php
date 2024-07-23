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
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 mx-auto rounded mb-2 hidden" id="alertError" style="width: 1045px;" role="alert">
        <strong class="font-bold"><h2>Error :</h2></strong>
        <span class="block sm:inline" id="error_message"></span>
    </div>
    @if ($currentRoute == 'jurnal.edit')
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <form action="{{ route('jurnal.update', $jurnal->id) }}" method="post" enctype="multipart/form-data">
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
                        <textarea name="{{ $item }}_header" id="{{ $item }}" 
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" 
                                  x-model="keteranganHeader">{{ $jurnal->$item }}</textarea>
                        @elseif ($item == 'jenis')
                            <select name="{{ $item }}" id="{{ $item }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
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
                                        <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.credit">
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
            <form action="{{ route('jurnal.store') }}" method="post" enctype="multipart/form-data">
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
                            <textarea name="{{$item}}_header" id="{{$item}}" value="{{old($item)}}"
                                      class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"
                                      x-model="keteranganHeader"></textarea>
                        @elseif ($item == 'jenis')
                            <select name="{{$item}}" id="{{$item}}" value="{{old($item)}}"
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
                                            <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit" x-on:input="formatCurrency($event, 'debit', index)">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit" x-on:input="formatCurrency($event, 'kredit', index)">
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
    document.addEventListener('alpine:init', () => {

        $(document).on('submit', 'form', function(event) {
            var isValid = true;
            var errorMessage = '';

            $('#jenis, #keterangan_header, #no_akun, #debit, #kredit, #tanggal_bukti, #keterangan').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    errorMessage += $(this).attr('name') + ' harus diisi.<br>';
                }
            });

            if($('#jurnalDetail tbody tr').length === 0) {
                isValid = false;
                errorMessage += 'Detail jurnal tidak boleh kosong.<br>';
            }

            $('#jurnalDetail tbody').each(function() {
                $(this).find('input, textarea').each(function() {
                    if ($(this).prop('required') && $(this).val().trim() === '') {
                        isValid = false;
                        var fieldName = $(this).attr('name').replace(/\[\d+\]/, '');
                        errorMessage += fieldName.replace('_', ' ') + ' harus diisi.<br>';
                    }
                });
            });

            if (!isValid) {
                $('#error_message').html(errorMessage);
                $('#alertError').removeClass('hidden');
                setTimeout(function() {
                    $('#alertError').addClass('hidden');
                }, 3000);
                event.preventDefault();
            }
        });

        Alpine.directive('datepicker', (el, { expression }, { effect }) => {
            $(el).datepicker({
                dateFormat: 'yy-mm-dd',
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
                    dateFormat: 'yy-mm-dd',
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
    });

    function jurnalApp() {
        let jurnal = @json($jurnal->details ?? []);

        return {
            keteranganHeader: '',
            rows: Array.isArray(jurnal) ? jurnal : [],
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
            },

            removeRow(index) {
                this.rows.splice(index, 1);
                this.rowx.splice(index, 1);
            },

            setKeteranganToRow(index) {
                this.rows[index].keterangan = this.keteranganHeader;;
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
        };
    }
</script>
{{-- <script type="module">

    

    $(document).ready(function() {
        $('#otomatis').hide();

        $('#berulang').change(function() {
            if ($(this).is(':checked')) {
                $('#otomatis').show();
                $('#manual').hide();
            } else {
                $('#otomatis').hide();
                $('#manual').show();
            }
        });
    });
</script> --}}
@endpush
</x-app-layout>
