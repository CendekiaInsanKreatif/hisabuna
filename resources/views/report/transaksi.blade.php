<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Transaksi</title>
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            max-width: 800px;
            margin: 0 auto;
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

        h2 {
            margin: 0;
            padding: 0;
        }


        h3 {
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 30px;
            padding: 0;


            @bottom-left {
                content: "{{ auth()->user()->company_name }}";
            }

            @bottom-right {
                content: counter(page);
            }
        }

        table {
            width: 100%;
        }

        th,
        td {
            padding: 2px;
            text-align: left;
        }

        #table-transaksi {
            page-break-after: always;
            /* counter-reset: page; */
        }

        /* .perPage {
            page-break-before: always;
            counter-increment: page;
        }

        .perPage:after
        {
            display: block;
            text-align: right;
            content: "Page " counter(page);
        }

        .perPage:first-of-type
        {
            page-break-before: avoid;
        } */
    </style>
</head>

<body onload="window.print()">
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2><u>{{ $jurnal['jenis'] == 'RV' ? 'JURNAL KAS MASUK' : ($jurnal['jenis'] == 'PV' ? 'JURNAL KAS KELUAR' : ($jurnal['jenis'] == 'JV' ? 'JURNAL UMUM' : '')) }}</u>
            </h2>
            <h3>{{ $jurnal['jenis'] == 'RV' ? 'RECEIVE VOUCHER' : ($jurnal['jenis'] == 'PV' ? 'PAYMENT VOUCHER' : ($jurnal['jenis'] == 'JV' ? 'JOURNAL VOUCHER' : '')) }}
            </h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%;">Keterangan Jurnal</td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top;">Nomor Transaksi</td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top;">Jenis Jurnal</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%; vertical-align: center;"><b><em>{{ $jurnal['keterangan'] }}</em></b></td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top; font-size: 35px; font-weight: bold;">
                    {{ $jurnal['no_urut_transaksi'] }}
                </td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top; font-size: 35px; font-weight: bold;">
                    {{ $jurnal['jenis'] }}
                </td>
            </tr>
        </tbody>
    </table>
    <table id="table-transaksi" style="table-layout: fixed;">
        <thead>
            <tr style="border: 1px solid black;">
                <th style="height: 25px; text-align:left; width: 8%;">Nomor Akun</th>
                <th colspan="2" style="height: 25px; text-align:left; width: 33%;">Nama Akun</th>
                <th style="height: 25px; text-align:right; width: 10%;">Debit</th>
                <th style="height: 25px; text-align:right; width: 10%;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
                $totalBilang = 0;
            @endphp
            @foreach ($jurnal['details'] as $index => $detail)
                @if (
                    $index == 0 ||
                        $jurnal['details'][$index]['parent']['nomor_akun'] != $jurnal['details'][$index - 1]['parent']['nomor_akun']
                )
                    <tr style="border-top: 1px solid black;">
                        <td style="font-weight: bold;">{{ $detail['parent']['nomor_akun'] }}</td>
                        <td colspan="2" style="font-weight: bold;">{{ $detail['parent']['nama_akun'] }}</td>
                        <td style="text-align: right;">
                            {{ $jurnal['debit'] ? number_format($jurnal['debit'], 0, ',', '.') : "-" }}</td>
                        <td style="text-align: right;">
                            {{ $jurnal['credit'] ? number_format($jurnal['credit'], 0, ',', '.') : "-" }}</td>
                    </tr>
                @endif
                <tr style="border-bottom: 1px solid black;">
                    <td>
                        @php
                            $formattedNomorAkun = preg_replace('/\D/', '', $detail['coa_akun']);
                            if (strlen($formattedNomorAkun) > 6) {
                                $formattedNomorAkun =
                                    substr($formattedNomorAkun, 0, 3) .
                                    '-' .
                                    substr($formattedNomorAkun, 3, 2) .
                                    '-' .
                                    substr($formattedNomorAkun, 5);
                            } elseif (strlen($formattedNomorAkun) > 4) {
                                $formattedNomorAkun =
                                    substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3);
                            } else {
                                $formattedNomorAkun = substr($formattedNomorAkun, 0, 3);
                            }
                        @endphp
                        &nbsp;&nbsp;&nbsp;{{ $formattedNomorAkun }}
                    </td>
                    <td colspan="2">&nbsp;&nbsp;&nbsp;{{ $detail['nama_akun'] }}</td>
                    <td style="text-align: right;">
                        {{ $detail['debit'] ? number_format($detail['debit'], 0, ',', '.') : "-" }}</td>
                    <td style="text-align: right;">
                        {{ $detail['credit'] ? number_format($detail['credit'], 0, ',', '.') : "-" }}</td>
                </tr>
                @php
                    $totalBilang += $detail['debit'] ? $detail['debit'] + $detail['credit'] : $detail['debit'] + $detail['credit'];
                    $totalDebit += $detail['debit'];
                    $totalCredit += $detail['credit'];
                @endphp
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5">
                    <hr style="border: 2px solid black; width: 100%;">
                </td>
            </tr>
            <tr>
                <td colspan="2"><i>Lampiran: {{ $jurnal['keterangan'] }}</i></td>
                <td style="text-align: right; font-weight: bold; margin-top: 10px;">Total:</td>
                <td style="text-align: right; margin-top: 10px;"><b>{{ number_format($totalDebit, 0, ',', '.') }}</b></td>
                <td style="text-align: right; margin-top: 10px;"><b>{{ number_format($totalCredit, 0, ',', '.') }}</b></td>
            </tr>
            <tr>
                <td colspan="2"><i>Terbilang : <b>{{ terbilang($totalBilang / 2) . ' Rupiah' }}</b></i></td>
            </tr>
            @for($i=0;$i<4;$i++)
                <tr>
                    <td colspan="5" style="text-align: center;">
                        &nbsp;
                    </td>
                </tr>
            @endfor
            <tr>
                <td colspan="5" style="text-align: center; border-bottom: 2px solid black; padding: 2px; width: 100%">
                    <div class="ttd" style="display: flex; justify-content: space-between;">
                        <div style="margin-left: 100px">Akuntan</div>
                        <div>Manajer</div>
                        <div style="margin-right: 100px">Direktur</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;">
                    &nbsp;
                </td>
            </tr>
            {{-- <tr>
                <td style="text-align: left;">
                    {{ strtoupper(auth()->user()->company_name) }}
                </td>
                <td style="text-align: center;">
                    &nbsp;
                </td>
                <td colspan="3" style="text-align:right;">
                    <div class="perPage"></div>
                </td>
            </tr> --}}
        </tfoot>
    </table>
</body>

</html>
