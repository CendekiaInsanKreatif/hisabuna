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
    @include('report.partials.header', [
        'reportTitle' => 'Neraca Saldo',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->translatedFormat('d F Y'),
    ])
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
            @foreach ($data as $akun1 => $subcategories)
                <tr>
                    <td colspan="3"><strong>{{ $akun1 }}</strong></td>
                </tr>
                @foreach ($subcategories as $akun2 => $accounts)
                    @if ($akun2 !== 'Total')
                        <tr>
                            <td class="indent"><strong>{{ $akun2 }}</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($accounts as $xCoa => $balances)
                            @if ($xCoa !== 'Total')
                                <tr>
                                    <td class="indent indent">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;{{ formatNomorAkun($xCoa) }}</span></td>
                                    <td style="text-align: right;">{{ number_format($balances['debit'], 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($balances['kredit'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @php
                                    $totalDebit += $balances['debit'];
                                    $totalKredit += $balances['kredit'];
                                @endphp
                            @endif
                        @endforeach
                        <tr>
                            <td class="indent"><strong>Total {{ pisah($akun2) }}</strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;">
                                <strong>{{ number_format($accounts['Total']['debit'], 0, ',', '.') }}</strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;">
                                <strong>{{ number_format($accounts['Total']['kredit'], 0, ',', '.') }}</strong></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td><strong>Total {{ pisah($akun1) }}</strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;">
                        <strong>{{ number_format($subcategories['Total']['debit'], 0, ',', '.') }}</strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;">
                        <strong>{{ number_format($subcategories['Total']['kredit'], 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Keseluruhan</strong></td>
                <td style="text-align: right; border-top: 2px solid black;">
                    <strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
                <td style="text-align: right; border-top: 2px solid black;">
                    <strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @include('report.partials.footer', [
        'location' => $paged['alamat'],
        'date' => $paged['tanggal'],
        'preparedBy' => $paged['dibuat'],
        'position' => $paged['jabatan'],
    ])
</body>

</html>
