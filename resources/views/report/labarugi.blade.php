<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }


        .company-logo {
            max-width: 80px;
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .report-title {
            font-size: 24px;
            width: 100%;
            text-align: center;
            padding-bottom: 12px;
        }

        table.main-data tr:nth-child(odd) {
            background-color: #f4f4f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-data tr td {
            padding: 4px 4px 4px 32px;
        }

        table.main-data tr:nth-last-child(2) td.data-num {
            border-bottom: solid 1px black;
        }

        .data-desc {
            width: 80%;
        }

        .data-num {
            text-align: right;
        }


        .total {
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    {{-- <h2><u>LAPORAN LABA RUGI</u></h2>
<h4>{{ auth()->user()->company_name }}</h4> --}}

    {{-- <div class="header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo">
        <h3>{{ auth()->user()->company_name }}</h3>
    </div> --}}

    <table style="padding-bottom: 8px; border-bottom: black 1px solid;">
        <tr>
            <td>
                <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company logo"
                    class="company-logo" />
            </td>
            <td style="text-align: right;">
                <h3 class="company-name">{{ auth()->user()->company_name }}</h3>
            </td>
        </tr>
    </table>
    <h1 class="report-title">LAPORAN LABA RUGI</h1>

    @foreach ($data as $category => $details)
        <table class="main-data">
            <h3 style="font-size: 20px; padding-bottom: 16px;">{{ $category }}</h3>
            @foreach ($details['Detail'] as $item => $amount)
                <tr>
                    <td class="data-desc">{{ $item }}</td>
                    <td class="data-num">{{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total" style="border-bottom: 2px solid black;             background-color: #f4f4f5;">
                <td style="padding: 8px 4px 8px 12px;">Total {{ $category }}</td>
                <td style="text-align: right;">{{ number_format($details['Jumlah'], 0, ',', '.') }}</td>
            </tr>
        </table>
    @endforeach

    <div style="padding: 24px 0 24px 0;border-bottom: 2px solid black">
        <table>
            <tr class="total" style=" font-size: 20px; ">
                <td>Saldo Laba (Rugi) Tahun Berjalan</td>
                <td style="text-align: right;">{{ number_format($labaRugiBersih, 0, ',', '.') }}</td>
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
</body>

</html>
