<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Transaksi</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            padding: 20px;
        }
        .header h1, .header h2, .header h4 {
            margin: 0;
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 2px;
            text-align: left;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>{{ auth()->user()->company_name }}</h1>
        <br>
        <br>
        <h2><u>{{ $jurnal['jenis'] == 'RV' ? 'JURNAL KAS MASUK' : ($jurnal['jenis'] == 'PV' ? 'JURNAL KAS KELUAR' : ($jurnal['jenis'] == 'JV' ? 'JURNAL UMUM' : '')) }}</u></h2>
        <h4>{{ $jurnal['jenis'] == 'RV' ? 'RECEIVE VOUCHER' : ($jurnal['jenis'] == 'PV' ? 'PAYMENT VOUCHER' : ($jurnal['jenis'] == 'JV' ? 'JOURNAL VOUCHER' : '')) }}</h4>
    </header>
    <br>
    <table>
        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%;">Informasi Tambahan</td>
                <td style="width: 40%;">&nbsp;&nbsp;Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ \Carbon\Carbon::parse($jurnal['jurnal_tgl'])->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; vertical-align: top; width: 60%; height: 5px; border: 1px solid black;"><b><em>{{ $jurnal['keterangan'] }}</em></b></td>
                <td style="width: 40%; height: 5px;">&nbsp;&nbsp;Jenis Jurnal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $jurnal['jenis'] }}</td>
            </tr>
            <tr>
                <td style="width: 40%; height: 5px">&nbsp;&nbsp;Nomor Transaksi&nbsp;&nbsp;&nbsp;: {{ $jurnal['id'] }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <thead style="border: 1px solid black;">
            <tr>
                <th style="height: 25px; text-align:center">Nomor Akun</th>
                <th style="height: 25px; text-align:center">Nama Akun</th>
                <th style="height: 25px; text-align:center">Subtotal</th>
                <th style="height: 25px; text-align:center">Debit</th>
                <th style="height: 25px; text-align:center">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach ($jurnal['details'] as $index => $detail)
            @if ($index == 0 || $jurnal['details'][$index]['parent']['nomor_akun'] != $jurnal['details'][$index - 1]['parent']['nomor_akun'])
                <tr style="border-top: 1px solid black">
                    <td style="font-weight: bold;">{{ $detail['parent']['nomor_akun'] }}</td>
                    <td style="font-weight: bold;">{{ $detail['parent']['nama_akun'] }}</td>
                    <td style="text-align: right;">{{ $jurnal['subtotal'] ? number_format($jurnal['subtotal'], 0, ',', '.') : 0 }}</td>
                    <td style="text-align: right;">{{ $jurnal['debit'] ? number_format($jurnal['debit'], 0, ',', '.') : 0 }}</td>
                    <td style="text-align: right;">{{ $jurnal['credit'] ? number_format($jurnal['credit'], 0, ',', '.') : 0 }}</td>
                </tr>
            @endif
            <tr style="border-bottom: 1px solid black;">
                <td>&nbsp;&nbsp;&nbsp;{{ $detail['coa_akun'] }}</td>
                <td>&nbsp;&nbsp;&nbsp;{{ $detail['nama_akun'] }}</td>
                <td style="text-align: right;">{{ $detail['subtotal'] ? number_format($detail['subtotal'], 0, ',', '.') : 0 }}</td>
                <td style="text-align: right;">{{ $detail['debit'] ? number_format($detail['debit'], 0, ',', '.') : 0 }}</td>
                <td style="text-align: right;">{{ $detail['credit'] ? number_format($detail['credit'], 0, ',', '.') : 0 }}</td>
            </tr>
            @php
                $totalDebit += $detail['debit'];
                $totalCredit += $detail['credit'];
            @endphp
            @endforeach
            <tr style="border-top: 2px solid black;">
                <td colspan="3" style="text-align: right; font-weight: bold; margin-top: 10px;">Total:</td>
                <td style="text-align: right; margin-top: 10px;">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td style="text-align: right; margin-top: 10px;">{{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <tbody>
            <tr>
                <td><i>Lampiran: {{ $jurnal['keterangan'] }}</i></td>
            </tr>
            <tr>
                <td><i>Terbilang : {{ terbilang($totalDebit).' Rupiah' }}</i></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
