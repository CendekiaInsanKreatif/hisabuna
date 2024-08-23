<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            padding: 2px;
        }
        h2, h1 {
            text-align: center;
            margin-top: 20px;
        }

        .footer.content {
            display: flex;
            align-items: center;
        }

        .company-logo {
            width: 5rem;
            height: 5rem;
            margin-right: 8rem;
        }

        .company-name {
            font-size: 1.25rem; /* Ukuran font yang sesuai */
            position: relative;
            top: -1.5rem; /* Sesuaikan nilai ini sesuai kebutuhan */
        }
    </style>
</head>

<body>
    <div class="footer content">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
    </div>
    <h1>{{ auth()->user()->company_name }}</h1>
    <h2><u>LAPORAN PERUBAHAN EKUITAS</u></h2>
    <table>
        <caption style="text-align: center; font-size: 14px;">Periode : {{ $tanggal_mulai }} s/d {{ $tanggal_selesai }}</caption>
        <br>
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
