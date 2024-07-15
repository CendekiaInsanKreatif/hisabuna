<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neraca Perbandingan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e8f4ff;
        }
        .indent-1 {
            padding-left: 20px;
        }
        .indent-2 {
            padding-left: 40px;
        }
        .total {
            font-weight: bold;
            background-color: #e8e8e8;
        }
    </style>
</head>
<body>
    <table>
        <caption><strong><h1>Neraca Perbandingan</h1></strong></caption>
        <thead>
            <tr>
                <th style="text-align: center">Keterangan</th>
                <th style="text-align: center">{{ $tahunSekarang }}</th>
                <th style="text-align: center">{{ $tahunSebelumnya }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data[$tahunSekarang] as $keterangan => $subData)
                <tr class="total" style="margin-top: 10px;">
                    <td><strong>{{ $keterangan }}</strong></td>
                    <td colspan="2"></td>
                </tr>
                @foreach ($subData as $kategori => $nilai)
                    @if (is_array($nilai))
                        <tr class="indent-1">
                            <td>&nbsp;&nbsp;&nbsp;<strong>{{ $kategori }}</strong></td>
                            <td colspan="2"></td>
                        </tr>
                        @foreach ($nilai as $subkategori => $jumlah)
                            @if ($subkategori != 'Total')
                                <tr class="indent-2">
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $subkategori }}</td>
                                    <td style="text-align: right;">{{ number_format($jumlah, 0, ',', '.') }}</td>
                                    <td style="text-align: right;">{{ number_format($data[$tahunSebelumnya][$keterangan][$kategori][$subkategori], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="total indent-1">
                            <td>Jumlah {{ $kategori }}</td>
                            <td style="text-align: right;">{{ number_format($nilai['Total'], 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($data[$tahunSebelumnya][$keterangan][$kategori]['Total'], 0, ',', '.') }}</td>
                        </tr>
                    @else
                        {{-- <tr class="indent-1">
                            <td><strong>{{ $kategori }}</strong></td>
                            <td style="text-align: right;">{{ number_format($nilai, 0, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($data[$tahunSebelumnya][$keterangan][$kategori], 0, ',', '.') }}</td>
                        </tr> --}}
                    @endif
                @endforeach
                <tr class="total">
                    <td>Jumlah {{ $keterangan }}</td>
                    <td style="text-align: right;">{{ number_format($subData['Total'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($data[$tahunSebelumnya][$keterangan]['Total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
