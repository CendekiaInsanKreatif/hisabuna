<x-app-layout>
    @section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-800">Neraca Perbandingan</h1>
        <h2 class="text-lg text-gray-600 mt-2">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}</h2>

        <div class="flex flex-wrap mt-4">
            <div class="w-full lg:w-1/2 px-2">
                <h3 class="text-xl font-semibold text-gray-700">ASET</h3>
                @if(isset($neracaPerbandingan['aset']))
                    @foreach($neracaPerbandingan['aset'] as $group => $items)
                        <div class="mt-4">
                            <h4 class="text-md font-semibold text-gray-600">{{ $group }}</h4>
                            <table class="min-w-full mt-2 bg-white shadow overflow-hidden rounded-lg">
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item['nama_akun'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($item['saldo_tahun_ini'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="w-full lg:w-1/2 px-2">
                <h3 class="text-xl font-semibold text-gray-700">LIABILITAS DAN EKUITAS</h3>
                @if(isset($neracaPerbandingan['liabilitas']))
                    @foreach($neracaPerbandingan['liabilitas'] as $group => $items)
                        <div class="mt-4">
                            <h4 class="text-md font-semibold text-gray-600">{{ $group }}</h4>
                            <table class="min-w-full mt-2 bg-white shadow overflow-hidden rounded-lg">
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item['nama_akun'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($item['saldo_tahun_ini'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                @if(isset($neracaPerbandingan['ekuitas']))
                    @foreach($neracaPerbandingan['ekuitas'] as $group => $items)
                        <div class="mt-4">
                            <h4 class="text-md font-semibold text-gray-600">{{ $group }}</h4>
                            <table class="min-w-full mt-2 bg-white shadow overflow-hidden rounded-lg">
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item['nama_akun'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($item['saldo_tahun_ini'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
