<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <style>
        @import url('{{ asset('css/rpt.css') }}');
    </style>
</head>
<body>
    @include('report.partials.header', [
        'reportTitle' => 'Laporan Perubahan Ekuitas',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY'),
    ])
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 40%;">Keterangan</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;">{{ $tahun }}</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;">Penambahan / <br> (Pengurangan)</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;">{{ $tahun - 1 }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalEkuitasTahunIni = 0;
                $totalEkuitasTahunLalu = 0;
            @endphp
            @foreach ($data as $year => $values)
                @if ($year == $tahun)
                    @foreach ($values as $key => $value)
                        @php
                            $totalEkuitasTahunIni += $value;
                            $totalEkuitasTahunLalu += $data[date('Y') - 1][$key] ?? 0;
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{ $key }}</td>
                            <td style="text-align: right;">{{ number_format($value) }}</td>
                            <td style="text-align: right;">{{ number_format($value - ($data[$tahun - 1][$key] ?? 0)) }}</td>
                            <td style="text-align: right;">{{ number_format($data[$tahun - 1][$key] ?? 0) }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
            <tr class="total">
                <td style="text-align: left; font-weight: bold;">Total Ekuitas</td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;">{{ number_format($totalEkuitasTahunIni) }}</td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;">{{ number_format($totalEkuitasTahunIni - $totalEkuitasTahunLalu) }}</td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;">{{ number_format($totalEkuitasTahunLalu) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
