<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca Saldo</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border-bottom: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
        }
        @media print {
            body {
                display: block;
            }
            .header {
                text-align: center;
                padding: 0;
            }
            .header h1, .header h2, .header h4 {
                margin: 0;
                font-size: inherit;
            }
            .header h1 {
                font-size: 2em;
            }
            .header h2 {
                font-size: 1.5em;
            }
            .header h4 {
                font-size: 1em;
            }
        }
        th { 
            background-color: #f2f2f2; 
        }
        h2, h1, h4 { 
            text-align: center; 
        }
        .indent { 
            padding-left: 20px; 
        }
        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: 0;
            font-size: 10px;
        }
        .footer .right::before {
            float: left;
            content: "Halaman " counter(page);
        }
        .footer .left {
            float: right;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
        <div class="header">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Laporan Neraca Saldo</h2>
            <h4>Periode: {{ $tanggal_mulai }} s/d {{ $tanggal_selesai }}</h4>
        </div>
    </header>
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $akun1 => $subcategories)
                <tr>
                    <td colspan="3"><strong>{{ $akun1 }}</strong></td>
                </tr>
                @foreach($subcategories as $akun2 => $accounts)
                    @if($akun2 !== 'Total')
                        <tr>
                            <td class="indent"><strong>{{ $akun2 }}</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($accounts as $xCoa => $balances)
                            @if($xCoa !== 'Total')
                                <tr>
                                    <td class="indent indent"><span>&nbsp;&nbsp;&nbsp;&nbsp;{{ $xCoa }}</span></td>
                                    <td style="text-align: right;">{{ number_format($balances['debit'], 0, ',', '.') }}</td>
                                    <td style="text-align: right;">{{ number_format($balances['kredit'], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td class="indent"><strong>Total {{ substr($akun2, 4) }}</strong></td>
                            <td style="text-align: right;"><strong>{{ number_format($accounts['Total']['debit'], 0, ',', '.') }}</strong></td>
                            <td style="text-align: right;"><strong>{{ number_format($accounts['Total']['kredit'], 0, ',', '.') }}</strong></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td><strong>Total {{ $akun1 }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($subcategories['Total']['debit'], 0, ',', '.') }}</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($subcategories['Total']['kredit'], 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <div class="left">{{ auth()->user()->company_name }}</div>
        <div class="right"></div>
    </div>
</body>
</html>
