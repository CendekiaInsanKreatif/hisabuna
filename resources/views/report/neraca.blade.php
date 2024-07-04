<x-app-layout>
    @section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Neraca</h1>
        <ul class="list-disc pl-5">
            @php $totalSaldo = 0; @endphp
            @foreach ($result as $item)
                <li>
                    <h3 class="text-xl font-semibold text-gray-700">{{ $item['parent'] }}</h3>
                    <ul class="list-disc pl-5">
                        <li>
                            <h4 class="text-md text-gray-600">{{ $item['subparent'] }}</h4>
                            <ul class="list-disc pl-5">
                                @php $subtotalSaldo = 0; @endphp
                                @foreach ($item['coa'] as $coa)
                                    @php $subtotalSaldo += $coa['saldo']; @endphp
                                    <li class="flex justify-between">
                                        <span class="font-semibold">{{ $coa['nama_akun'] }}</span>
                                        <span>{{ number_format($coa['saldo']) }}</span>
                                    </li>
                                @endforeach
                                <hr class="my-2">
                                <li class="flex justify-between">
                                    <span class="font-semibold">Subtotal {{ $item['subparent'] }}:</span>
                                    <span>{{ number_format($subtotalSaldo) }}</span>
                                </li>
                                @php $totalSaldo += $subtotalSaldo; @endphp
                            </ul>
                        </li>
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
    @endsection
</x-app-layout>
