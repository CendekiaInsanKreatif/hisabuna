<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h3 {
            margin: 0;
            padding: 0;
        }
        h2 {
            margin: 0;
            padding: 0;
        }
        p {
            margin: 0;
            padding: 0;
        }
        .amount {
            float: right;
            text-align: right;
            margin-left: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <table>
        <caption><h1 style="font-size: 24px;">{{ auth()->user()->company_name }}</h1></caption>
        <br>
        <caption><h1>Laporan Posisi Keuangan</h1></caption>
        <caption><h3>Periode : {{ $periode }}</h3></caption>
        <thead>
            <tr>
                <th>&nbsp;</th>
                @foreach(array_keys($data) as $year)
                    <th style="text-align: right; font-size: 18px;">{{ $year }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data[array_key_first($data)] as $level1 => $level1Data)
                @if(is_array($level1Data))
                    <tr>
                        <td colspan="{{ count($data) + 1 }}"><h2>{{ $level1 == '1' ? 'Aset' : $level1 }}</h2></td>
                    </tr>
                    @php
                        $totalLevel1 = array_fill_keys(array_keys($data), 0);
                    @endphp
                    @foreach($level1Data as $level2 => $level2Data)
                        @if(is_array($level2Data))
                            <tr>
                                <td colspan="{{ count($data) + 1 }}"><h3>&nbsp;&nbsp;&nbsp;&nbsp; {{ $level2 }}</h3></td>
                            </tr>
                            @php
                                $totalLevel2 = array_fill_keys(array_keys($data), 0);
                            @endphp
                            @foreach($level2Data as $level3 => $amount)
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}</td>
                                    @foreach($data as $year => $yearData)
                                        @php
                                            $amount = $yearData[$level1][$level2][$level3] ?? 0;
                                            $totalLevel2[$year] += $amount;
                                            $totalLevel1[$year] += $amount;
                                        @endphp
                                        <td style="text-align: right;">{{ number_format($amount, 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h3>&nbsp;&nbsp;&nbsp;&nbsp; Total {{ $level2 }}</h3></td>
                                @foreach($totalLevel2 as $year => $total)
                                    <td style="text-align: right;"><h3>{{ number_format($total, 0, ',', '.') }}</h3></td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td><h2>Total {{ $level1 == '1' ? 'Aset' : $level1 }}</h2></td>
                        @foreach($totalLevel1 as $year => $total)
                            <td style="text-align: right;"><h2>{{ number_format($total, 0, ',', '.') }}</h2></td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
