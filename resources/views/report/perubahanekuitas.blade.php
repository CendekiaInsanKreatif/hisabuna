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
    </style>
</head>
<body>
    <h2><u>LAPORAN PERUBAHAN EKUITAS</u></h2>
    <h4>{{ auth()->user()->company_name }}</h4>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>2024</th>
                <th>Penambahan / (Pengurangan)</th>
                <th>2023</th>
            </tr>
        </thead>
        <tbody>
            @php
                $modal2024 = $data['2024']['Modal Disetor'];
                $saldo2024 = $data['2024']['Saldo Tahun Berjalan'];
                $modal2023 = $data['2023']['Modal Disetor'];
                $saldo2023 = $data['2023']['Saldo Tahun Berjalan'];
                $modalDiff = $data[0]['Modal Disetor'];
                $saldoDiff = $data[0]['Saldo Tahun Berjalan'];
            @endphp
            <tr>
                <td>Modal Disetor</td>
                <td style="text-align: right;">{{ number_format($modal2024, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modal2023, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Saldo Tahun Berjalan</td>
                <td style="text-align: right;">{{ number_format($saldo2024, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($saldoDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($saldo2023, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Jumlah Ekuitas</td>
                <td style="text-align: right;">{{ number_format($modal2024 + $saldo2024, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modalDiff + $saldoDiff, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($modal2023 + $saldo2023, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
