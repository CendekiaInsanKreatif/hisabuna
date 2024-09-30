<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca Saldo</title>
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <style>
        body { 
            font-family: Arial, sans-serif;
            font-size: 12px;
            max-width: 800px;
            margin: 0 auto;
        }
        table { 
            width: 100%;
            /* margin-top: 20px;  */
        }
        th, td {
            padding: 2px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        h3 {
            margin: 0;
            padding: 0;
        }

        h2 {
            margin: 0;
            padding: 0;
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

        .new-header {
            position: relative;
        }

        .company-logo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100px;

        }

        @page {
            size: A4;
            margin: 30px;
            padding: 0;
            @bottom-center {
                content: counter(page);
            }
        }
    </style>
</head>
<body onload="window.print()">
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Neraca Saldo</h2>
            <h3>Per {{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->format('d F Y') }}</h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalKredit = 0;
            @endphp
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
                                    <td class="indent indent"><span>&nbsp;&nbsp;&nbsp;&nbsp;{{ formatNomorAkun($xCoa) }}</span></td>
                                    <td style="text-align: right;">{{ number_format($balances['debit'], 0, ',', '.') }}</td>
                                    <td style="text-align: right;">{{ number_format($balances['kredit'], 0, ',', '.') }}</td>
                                </tr>
                                @php
                                    $totalDebit += $balances['debit'];
                                    $totalKredit += $balances['kredit'];
                                @endphp
                            @endif
                        @endforeach
                        <tr>
                            <td class="indent"><strong>Total {{ pisah($akun2) }}</strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;"><strong>{{ number_format($accounts['Total']['debit'], 0, ',', '.') }}</strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;"><strong>{{ number_format($accounts['Total']['kredit'], 0, ',', '.') }}</strong></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td><strong>Total {{ pisah($akun1) }}</strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;"><strong>{{ number_format($subcategories['Total']['debit'], 0, ',', '.') }}</strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;"><strong>{{ number_format($subcategories['Total']['kredit'], 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Keseluruhan</strong></td>
                <td style="text-align: right; border-top: 2px solid black;"><strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
                <td style="text-align: right; border-top: 2px solid black;"><strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
