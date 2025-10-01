<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <title>Laporan Neraca Saldo</title>
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <style>
        @import url('{{ asset('css/rpt.css') }}');
        table {
            width: 100%;
        }
        tr:last-child td {
            margin: 0;
            padding: 0;
        }
    </style>
    <script>
        setTimeout(() => {
                window.print()
            }, 5000);
    </script>
</head>
<body>
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Neraca Saldo</h2>
            <h3>Per {{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->translatedFormat('d F Y') }}</h3>
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
    <table style="width: 100%; margin-top: 70px;">
        <tr>
            <td style="text-align: center; width: 100%;">
                {{ $paged['alamat'] }}, {{ $paged['tanggal'] }}
            </td>
            <td style="text-align: center; width: 100%;">
                {{ $paged['dibuat'] }}
            </td>
            <td style="text-align: center; width: 100%;">
                {{ $paged['jabatan'] }}
            </td>
        </tr>
    </table>
</body>
</html>
