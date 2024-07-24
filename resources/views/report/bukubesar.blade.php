<x-app-layout>
    @section('content')
    <form method="GET" action="{{ route('report.bukubesar') }}" class="mb-6">
        <div class="flex space-x-2 items-end mb-4">
            <div class="flex flex-col">
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai:</label>
                <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai:</label>
                <input type="text" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="akun" class="block text-sm font-medium text-gray-700">Akun:</label>
                <input type="text" id="akun" name="akun" value="{{ $akun }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex items-end">
                <x-primary-button class="h-10">Tampilkan</x-primary-button>
                <a href="#" id="download" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Download</a>
            </div>
        </div>
    </form>

    <div id="ledgerContent" class="w-full">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">BUKU BESAR (LEDGER)</h1>
            <h3 class="text-gray-700 mb-4">Periode {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</h3>
        </div>
        @foreach ($ledgers as $coaAkun => $transactions)
            <h2 class="text-xl font-semibold text-gray-700">{{ $coaAkun }}</h2>
            <table class="w-full divide-y divide-gray-200 mt-4 mb-6 table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-1/5 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="w-2/5 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Debit<div class="text-red-500">Bertambah</div></th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Kredit<div class="text-red-500">Berkurang</div></th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->jurnal_tgl)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $keterangan = $transaction->keterangan;
                                    $max_length = 50;
                                    $output = '';
                                    while (strlen($keterangan) > $max_length) {
                                        $output .= substr($keterangan, 0, $max_length) . '<br>';
                                        $keterangan = substr($keterangan, $max_length);
                                    }
                                    $output .= $keterangan;
                                    echo $output;
                                @endphp
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($transaction->debit, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($transaction->credit, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($transaction->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
    @endsection

    @push('script')
    <script type="module">
        $(document).ready(function() {
            $('#tanggal_mulai').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) { 
                    var tanggalMulai = new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
                    $(this).datepicker('setDate', tanggalMulai);
                    $('#tanggal_selesai').datepicker('option', 'minDate', tanggalMulai);
                    $('#tanggal_selesai').datepicker('setDate', tanggalMulai);
                }
            });

            $('#tanggal_selesai').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                beforeShow: function(input, inst) {
                    var tanggalMulai = $('#tanggal_mulai').datepicker('getDate');
                    if (tanggalMulai) {
                        $(this).datepicker('option', 'minDate', tanggalMulai);
                    }
                }
            });

            document.getElementById('download').addEventListener('click', function(event) {
                event.preventDefault();
                var tanggalMulai = document.getElementById('tanggal_mulai').value;
                var tanggalSelesai = document.getElementById('tanggal_selesai').value;
                if (tanggalMulai) {
                    var url = "{{ route('report.bukubesar.download') }}" + "?tanggal_mulai=" + tanggalMulai + "&tanggal_selesai=" + tanggalSelesai;
                    window.location.href = url;
                } else {
                    alert("Harap pilih tanggal mulai.");
                }
            });
        });
        
    </script>
    @endpush
</x-app-layout>

