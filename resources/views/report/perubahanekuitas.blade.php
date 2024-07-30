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
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            background-color: #e8e8e8;
        }
        h2 {
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
    {{-- <h2><u>LAPORAN PERUBAHAN EKUITAS</u></h2>
    <h4>{{ auth()->user()->company_name }}</h4> --}}
    <div class="footer content">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo" class="company-logo">
        <span class="company-name">{{ auth()->user()->company_name }}</span>
    </div>
    <h2><u>LAPORAN PERUBAHAN EKUITAS</u></h2>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>{{ $tahunSekarang }}</th>
                <th>Penambahan / (Pengurangan)</th>
                <th>{{ $tahunSebelumnya }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $modalSekarang = $data[$tahunSekarang][$totalsSekarang['namaAkun']];
                $saldoSekarang = $data[$tahunSekarang]['Saldo Tahun Berjalan'];
                $modalDulu = $data[$tahunSebelumnya][$totalsDulu['namaAkun']];
                $saldoDulu = $data[$tahunSebelumnya]['Saldo Tahun Berjalan'];
                $modalDiff = $data[0][$totalsDulu['namaAkun']];
                $saldoDiff = $data[0]['Saldo Tahun Berjalan'];
            @endphp
            <tr>
                <td>Modal Disetor</td>
                <td style="text-align: right;">{{ number_format($modalSekarang, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDulu, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Saldo Tahun Berjalan</td>
                <td style="text-align: right;">{{ number_format($saldoSekarang, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($saldoDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($saldoDulu, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Jumlah Ekuitas</td>
                <td style="text-align: right;">{{ number_format($modalSekarang + $saldoSekarang, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDiff + $saldoDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDulu + $saldoDulu, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
