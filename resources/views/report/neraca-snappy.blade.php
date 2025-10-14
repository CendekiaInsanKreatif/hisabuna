<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan {{ $label }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 6px 2px;
        }

        th {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            padding: 8px;
        }

        th:first-child {
            text-align: left;
        }

        td {
            padding: 5px 8px;
        }

        h3 {
            margin: 8px 0;
            font-size: 13px;
        }

        .total-row td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .grand-total-row td {
            border-top: 2px double #000;
            border-bottom: 2px double #000;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>&nbsp;</th>
                @foreach (array_keys($data) as $year)
                    <th style="text-align: right; width: 115px;">{{ $year }}</th>
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
                    @php $totalLevel1 = array_fill_keys(array_keys($data), 0); @endphp
                    @foreach ($level1Data as $level2 => $level2Data)
                        @if (is_array($level2Data))
                            <tr>
                                <td colspan="{{ count($data) + 1 }}">
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; {{ $level2 }}</h3>
                                </td>
                            </tr>
                            @php $totalLevel2 = array_fill_keys(array_keys($data), 0); @endphp
                            @foreach ($level2Data as $level3 => $amount)
                                <tr>
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
                            <tr class="total-row">
                                <td>
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah {{ ucwords(strtolower($level2)) }}</h3>
                                </td>
                                @foreach ($totalLevel2 as $year => $total)
                                    <td style="text-align: right;">
                                        <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                    <tr class="grand-total-row">
                        <td>
                            <h3>JUMLAH {{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
                        </td>
                        @foreach ($totalLevel1 as $year => $total)
                            <td style="text-align: right;">
                                <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td colspan="{{ count($data) + 1 }}">&nbsp;</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>
