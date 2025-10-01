<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        @import url('{{ asset('css/rpt.css') }}');
    </style>
</head>
<body>
    <div class="report-container">
        <header style="border-bottom: 2px solid #ddd; padding: 10px 20px;">
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
            <div style="overflow: hidden;">
                <h1 style="text-align:center; font-size: 20px;">{{ auth()->user()->company_name }}</h1>
                <p style="text-align:center;">Laporan Laba Rugi</p>
                <p style="text-align:center;">Periode {{ date('d/m/Y', strtotime($start)) }} s/d {{ date('d/m/Y', strtotime($end)) }}</p>
            </div>
        </header>
        @foreach ($data as $category => $details)
        <table class="main-data">
            <h3 style="font-size: 20px; padding-bottom: 16px;">{{ $category }}</h3>
            @foreach ($details['Detail'] as $item => $amount)
                <tr>
                    <td class="data-desc; font-size: 12px;">{{ $item }}</td>
                    <td class="data-num">{{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total" style="border-bottom: 2px solid black;             background-color: #f4f4f5;">
                <td style="padding: 8px 4px 8px 12px; font-size: 16px; font-weight: bold;">Total {{ $category }}</td>
                <td style="text-align: right; font-size: 16px; font-weight: bold;">{{ number_format($details['Jumlah'], 0, ',', '.') }}</td>
            </tr>
        </table>
        @endforeach
        <div style="padding: 24px 0 24px 0;border-bottom: 2px solid black">
            <table>
                <tr class="total" style=" font-size: 20px; ">
                    <td>Saldo Laba (Rugi) Tahun Berjalan</td>
                    <td style="text-align: right; font-size: 16px; font-weight: bold;">{{ number_format($labaRugiBersih, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <table style="width: 100%; margin-top: 70px;">
            <tr>
                <td style="text-align: center; width: 35%;">
                    <div>Dibuat oleh, {{ $ttd1 }}</div>
                    <div style="height: 80px;"></div>
                    <div><strong>Staff Keuangan</strong></div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 10%;"></td>
                <td style="width: 10%;"></td>
                <td style="text-align: center; width: 35%;">
                    <div>Disetujui oleh, {{ $ttd2 }}</div>
                    <div style="height: 80px;"></div>
                    <div><strong>Manager Keuangan</strong></div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
