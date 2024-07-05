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
    @if ($currentRoute == 'jurnal.edit')
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center w-full mt-6">
                <form id="importForm" class="w-full" @submit.prevent="importJurnal">
                    @csrf
                    <input type="file" id="importFile" name="file" accept=".xlsx" required
                        class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                    <button type="submit"
                        class="inline-flex items-center bg-emerald-500 justify-center px-2 py-1 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Import Jurnal
                    </button>
                </form>
                <form action="{{ route('jurnal.sample.export') }}" method="post" class="w-full text-right">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center px-2 py-1 bg-emerald-300 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Download Sample
                    </button>
                </form>
            </div>
            <form action="{{ route('jurnal.update', $jurnal->id) }}" method="post">
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
                            <textarea name="{{ $item }}_header" id="{{ $item }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">{{ $jurnal->$item }}</textarea>
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
                </div>
                <div class="card-body overflow-x-auto mt-1 bg-white shadow-md rounded-lg p-1">
                    <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                        <thead class="text-xs text-gray-700 uppercase text-center bg-gray-200">
                            <tr>
                                <th class="py-2 px-4">Akun</th>
                                <th class="py-2 px-4">Debit</th>
                                <th class="py-2 px-4">Kredit</th>
                                <th class="py-2 px-4">Keterangan</th>
                                <th class="py-2 px-4 text-right">
                                    <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="rows.push({ no_akun: '', nama_akun: '', debit: '', kredit: '', keterangan: '' })">
                                        Tambah
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100 text-center" id="tBody">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="border-b">
                                    <td class="py-2 px-4 flex items-center">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                            Pilih
                                        </button>
                                        <input type="text" :name="'no_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" x-model="row.no_akun">
                                        <input type="text" :name="'nama_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.nama_akun">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'keterangan[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.keterangan">
                                    </td>
                                    <td class="py-2 px-4">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(index, 1)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr class="border-b bg-gray-300" id="tRowX">
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </form>
        </div>
    </div>
    @else
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center w-full mt-6">
                <form id="importForm" class="w-full" @submit.prevent="importJurnal">
                    @csrf
                    <input type="file" id="importFile" name="file" accept=".xlsx" required
                        class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                    <button type="submit"
                        class="inline-flex items-center bg-emerald-500 justify-center px-2 py-1 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Import Jurnal
                    </button>
                </form>
                <form action="{{ route('jurnal.sample.export') }}" method="post" class="w-full text-right">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center px-2 py-1 bg-emerald-300 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Download Sample
                    </button>
                </form>
            </div>
            <div class="mb-6 flex justify-between items-center">
                <p class="text-2xl font-semibold text-emerald-500">Buat Jurnal</p>
                <form action="{{ route('jurnal.store') }}" method="post">
                    @csrf
                    @method('POST')
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
                                <textarea name="{{$item}}_header" id="{{$item}}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"></textarea>
                            @elseif ($item == 'jenis')
                                <select name="{{$item}}" id="{{$item}}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                    <option value="">Pilih Jenis</option>
                                    <option value="rv">Voucher Penerimaan | RV</option>
                                    <option value="pv">Voucher Pembayaran | PV</option>
                                    <option value="jv">Voucher Jurnal | JV</option>
                                </select>
                            @elseif ($item == 'no_transaksi')
                                <input type="text" name="{{$item}}" id="{{$item}}" readonly placeholder="Generate By System" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="card-body overflow-x-auto mt-1 bg-white shadow-md rounded-lg p-1">
                        <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                            <thead class="text-xs text-gray-700 uppercase text-center bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4">Akun</th>
                                    <th class="py-2 px-4">Debit</th>
                                    <th class="py-2 px-4">Kredit</th>
                                    <th class="py-2 px-4">Keterangan</th>
                                    <th class="py-2 px-4 text-right">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="rows.push({ no_akun: '', nama_akun: '', debit: '', kredit: '', keterangan: '' })">
                                            Tambah
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-100 text-center" id="tBody">
                                <template x-for="(row, index) in rows" :key="index">
                                    <tr class="border-b">
                                        <td class="py-2 px-4 flex items-center">
                                            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                                Pilih
                                            </button>
                                            <input type="text" :name="'no_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" x-model="row.no_akun">
                                            <input type="text" :name="'nama_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.nama_akun">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" :name="'keterangan[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.keterangan">
                                        </td>
                                        <td class="py-2 px-4">
                                            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(index, 1)">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr class="border-b bg-gray-300" id="tRowX">
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endif
    @endsection
    @push('script')
    <script>
    function jurnalApp() {
        let jurnal = @json($jurnal->details).map(detail => ({
            no_akun: detail.coa_akun,
            nama_akun: detail.coa.nama_akun,
            debit: detail.debit,
            kredit: detail.credit,
            keterangan: detail.keterangan
        }));
        // console.log(jurnal);

        return {
            rows: jurnal ?? [],
            importJurnal(event) {
                let formData = new FormData(document.getElementById('importForm'));
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
            }
        };
    }

    </script>
    @endpush

</x-app-layout>
