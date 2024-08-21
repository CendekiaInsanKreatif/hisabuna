<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .report-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    <button type="submit" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4">Submit</button>
    <div class="report-container">
        <header style="border-bottom: 2px solid #ddd; padding: 10px 20px;">
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
            <div style="overflow: hidden;">
                <h1 style="text-align:center; font-size: 20px;">{{ auth()->user()->company_name }}</h1>
                <p style="text-align:center;">Laporan Laba Rugi</p>
                <p style="text-align:center;">Periode {{$start}} - {{$end}}</p>
            </div>
        </header>
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
    </div>
</body>
</html>