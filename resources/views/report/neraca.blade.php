<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan {{ $label }}</title>
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url('{{ asset('css/rpt.css') }}');
    </style>
    <script>
        setTimeout(() => {
            window.print()
        }, 5000);
    </script>
</head>

<body>
    @include('report.partials.header', [
        'reportTitle' => 'Laporan Posisi Keuangan',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY'),
    ])

    <table style="border-spacing: 6px 2px;">
        <thead>
            <tr>
                <th>&nbsp;</th>
                @foreach (array_keys($data) as $year)
                    <th style="text-align: right; font-size: 18px; width: 115px;">{{ $year }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data[array_key_first($data)] as $level1 => $level1Data)
                @if (is_array($level1Data))
                    <tr>
                        <td colspan="{{ count($data) + 1 }}">
                            <h3>{{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
                        </td>
                    </tr>
                    @php $totalLevel1 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level1Data as $level2 => $level2Data)
                        @if (is_array($level2Data))
                            <tr>
                                <td colspan="{{ count($data) + 1 }}">
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; {{ $level2 }}</h3>
                                </td>
                            </tr>
                            @php $totalLevel2 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level2Data as $level3 => $amount)
                                <tr>
                                    {{-- <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}</td> --}}
                                    @if ($level3 == 'Saldo Tahun Berjalan')
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}
                                        </td>
                                    @else
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}
                                        </td>
                                    @endif
                                    @foreach ($data as $year => $yearData)
                                        @php
                                            $amount = $yearData[$level1][$level2][$level3] ?? 0;
                                            $totalLevel2[$year] += $amount;
                                            $totalLevel1[$year] += $amount;
                                        @endphp
                                        <td style="text-align: right;">
                                            {{ number_format($amount, 0, ',', '.') }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <td>
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah {{ ucwords(strtolower($level2)) }}</h3>
                                </td>
                                @foreach ($totalLevel2 as $year => $total)
                                    <td
                                        style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                                        <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td>
                            <h3>JUMLAH {{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
                        </td>
                        @foreach ($totalLevel1 as $year => $total)
                            <td
                                style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                                <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                            </td>
                        @endforeach
                    </tr>
                    <br />
                @endif
            @endforeach
        </tbody>
    </table>

    @include('report.partials.footer', [
        'location' => $paged['alamat'],
        'date' => $paged['tanggal'],
        'preparedBy' => $paged['dibuat'],
        'position' => $paged['jabatan'],
    ])
</body>

</html>




{{-- <table style="border-spacing: 6px 2px;">
    <thead>
        <tr>
            <th>&nbsp;</th>
            @foreach (array_keys($data) as $year)
                <th style="text-align: right; font-size: 18px; width: 115px;">{{ $year }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data[array_key_first($data)] as $level1 => $level1Data) @if (is_array($level1Data))
        <tr>
            <td colspan="{{ count($data) + 1 }}">
                <h3>{{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
            </td>
        </tr>
        @php $totalLevel1 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level1Data as $level2 => $level2Data) @if (is_array($level2Data))
        <tr>
            <td colspan="{{ count($data) + 1 }}">
                <h3>&nbsp;&nbsp;&nbsp;&nbsp; {{ $level2 }}</h3>
            </td>
        </tr>
        @php $totalLevel2 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level2Data as $level3 => $amount)
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}</td>
            @foreach ($data as $year => $yearData) @php $amount = $yearData[$level1][$level2][$level3] ?? 0; $totalLevel2[$year] += $amount; $totalLevel1[$year] += $amount; @endphp
            <td style="text-align: right;">
                {{ number_format($amount, 0, ',', '.') }}
            </td>
            @endforeach
        </tr>
        @endforeach
        <tr>
            <td>
                <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah {{ $level2 }}</h3>
            </td>
            @foreach ($totalLevel2 as $year => $total)
            <td style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                <h3>{{ number_format($total, 0, ',', '.') }}</h3>
            </td>
            @endforeach
        </tr>
        @endif @endforeach
        <tr>
            <td>
                <h3>JUMLAH {{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
            </td>
            @foreach ($totalLevel1 as $year => $total)
            <td style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                <h3>{{ number_format($total, 0, ',', '.') }}</h3>
            </td>
            @endforeach
        </tr>
        <br />
        @endif @endforeach
    </tbody>
</table>
<table style="width: 100%; margin-top: 70px;">
    <tr>
        <td style="text-align: center; width: 100%;">
            <div style="font-size: 12px;">{{ $paged['alamat'] }}, {{ $paged['tanggal'] }}</div>
            <div style="height: 80px;"></div>
            <div style="font-size: 12px;">{{ $paged['dibuat'] }}</div>
            <div style="border-bottom: 2px solid black; width: 100px; margin-left: auto; margin-right: auto;"></div>
            <div style="font-size: 12px;">{{ $paged['jabatan'] }}</div>
        </td>
    </tr>
</table> --}}
