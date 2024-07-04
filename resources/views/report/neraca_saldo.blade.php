<x-app-layout>
    @section('content')
    <h1>Neraca Saldo</h1>
    <h2>Periode: {{ $tanggalMulai }} - {{ $tanggalSelesai }}</h2>
    <table border="1">
        <tr>
            <th>Akun</th>
            <th>Saldo</th>
        </tr>
        @foreach ($neracaSaldo as $saldo)
        <tr>
            <td>{{ $saldo['coa_akun'] }}</td>
            <td>{{ number_format($saldo['saldo'], 2) }}</td>
        </tr>
        @endforeach
    </table>
    @endsection
</x-app-layout>
