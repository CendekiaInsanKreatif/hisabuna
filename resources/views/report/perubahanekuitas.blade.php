<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            padding: 2px;
        }
        h2, h3 {
            margin: 0;
            padding: 0;
        }

        .footer.content {
            display: flex;
            align-items: center;
        }

        .new-header {
            position: relative;
        }

        .company-logo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100px;

        }
    </style>
</head>

<body onload="window.print()">
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Laporan Perubahan Ekuitas</h2>
            <h3>Periode: {{ $tanggal_mulai }} s/d {{ $tanggal_selesai }}</h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right; border-bottom: 1px solid #000;">{{ date('Y') }}</th>
                <th style="text-align: right; border-bottom: 1px solid #000;">Penambahan / <br> (Pengurangan)</th>
                <th style="text-align: right; border-bottom: 1px solid #000;">{{ date('Y') - 1 }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalEkuitasTahunIni = 0;
                $totalEkuitasTahunLalu = 0;
            @endphp
            @foreach ($data as $year => $values)
                @if ($year == date('Y'))
                    @foreach ($values as $key => $value)
                        @php
                            $totalEkuitasTahunIni += $value;
                            $totalEkuitasTahunLalu += $data[date('Y') - 1][$key] ?? 0;
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{ $key }}</td>
                            <td style="text-align: right;">{{ number_format($value) }}</td>
                            <td style="text-align: right;">{{ number_format($value - ($data[date('Y') - 1][$key] ?? 0)) }}</td>
                            <td style="text-align: right;">{{ number_format($data[date('Y') - 1][$key] ?? 0) }}</td>
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
