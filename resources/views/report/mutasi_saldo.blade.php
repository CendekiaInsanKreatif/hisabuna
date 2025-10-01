<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Mutasi Saldo</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            max-width: 1000px;
        }

        .new-header {
            position: relative;
            text-align: center;
        }

        .company-logo {
            position: absolute;
            top: 5%;
            left: 0;
            transform: translateY(-50%);
            width: 100px;
        }

	    table tbody {
            page-break-inside: avoid; /* Hindari pemisahan tabel */
        }

        .fill-space {
            flex: 1 1 auto;
            height: auto; /* Elemen ini akan mengisi ruang kosong */
        }

        tfoot {
            page-break-inside: avoid; /* Pastikan footer tidak terpotong */
        }

        hr {
            border: 2px solid black;
            width: 100%;
        }

        .nowrap {
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        h2, h3 {
            margin: 0;
            padding: 0;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        th, td {
            border-bottom: 1px solid #ccc;
            padding: 3px;
            text-align: left;
            width: 100px; /* Ubah angka ini sesuai kebutuhan */
            word-break: break-all;
            vertical-align: top;
        }

        .page {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh; /* Pastikan setiap halaman penuh */
            page-break-before: always;
        }

        #page-footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
        }

        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
                page-break-inside: avoid;
            }

            tbody {
                page-break-inside: avoid;
            }

            .manual-header {
                display: none;
            }
        }

    </style>
    <script>
        setTimeout(() => {
            window.print()
        }, 5000);
    </script>
</head>
<body>
    @foreach ($dataChunked as $pageIndex => $data)
    <div class="{{ $pageIndex == 0 ? 'page-break-first' : 'page-break' }}">
        @if ($pageIndex == 0)
        <header>
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
            <div class="new-header">
                <h1>{{ auth()->user()->company_name }}</h1>
                <h2>Laporan Mutasi Saldo</h2>
                <h3>Per {{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY') }}</h3>
            </div>
        </header>
        <hr>
        @endif

        <table>
            @if ($pageIndex === 0)
            <thead>
                <tr>
                    <th rowspan="2" class="nomor_akun">Nomor Akun</th>
                    <th rowspan="2" class="nama_akun">Nama Akun</th>
                    <th colspan="2" class="text-center">Saldo Awal</th>
                    <th colspan="2" class="text-center">Mutasi</th>
                    <th colspan="2" class="text-center">Saldo Akhir</th>
                </tr>
                <tr>
                    <th class="text-center">Debit</th>
                    <th class="text-center">Kredit</th>
                    <th class="text-center">Debit</th>
                    <th class="text-center">Kredit</th>
                    <th class="text-center">Debit</th>
                    <th class="text-center">Kredit</th>
                </tr>
            </thead>
            @endif
            <tbody>
            @foreach ($data as $golongan => $golonganData)
                <tr>
                    <td colspan="8"><h2 style="text-align: left;">{{ $golongan }}</h2></td>
                </tr>
                @php
                    $subtotal_saldo_awal_debit = 0;
                    $subtotal_saldo_awal_credit = 0;
                    $subtotal_mutasi_debit = 0;
                    $subtotal_mutasi_credit = 0;
                    $subtotal_saldo_akhir_debit = 0;
                    $subtotal_saldo_akhir_credit = 0;
                @endphp
                @foreach ($golonganData as $nomor_akun => $detail)
                    <tr>
                        <td class="nowrap">{{ formatNomorAkun($nomor_akun) }}</td>
                        <td>{{ $detail['nama_akun'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($detail['saldo_awal']['debit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail['saldo_awal']['credit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail['mutasi']['debit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail['mutasi']['credit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail['saldo_akhir']['debit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail['saldo_akhir']['credit'], 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $subtotal_saldo_awal_debit += $detail['saldo_awal']['debit'];
                        $subtotal_saldo_awal_credit += $detail['saldo_awal']['credit'];
                        $subtotal_mutasi_debit += $detail['mutasi']['debit'];
                        $subtotal_mutasi_credit += $detail['mutasi']['credit'];
                        $subtotal_saldo_akhir_debit += $detail['saldo_akhir']['debit'];
                        $subtotal_saldo_akhir_credit += $detail['saldo_akhir']['credit'];
                    @endphp
                @endforeach
                <tr style="border-top: 2px solid black;">
                    <td colspan="2" class="text-right"><strong>Jumlah {{ $golongan }} :</strong></td>
                    <td class="text-right">{{ number_format($subtotal_saldo_awal_debit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subtotal_saldo_awal_credit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subtotal_mutasi_debit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subtotal_mutasi_credit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subtotal_saldo_akhir_debit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($subtotal_saldo_akhir_credit, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Footer Nomor Halaman -->
        {{-- <div id="page-footer">
            @php
                $nopage = $jumlahLaman;
            @endphp
            <small>Halaman ke {{ $nopage + $pageIndex }} dari {{ $dataChunked->count() }} Halaman</small>
        </div> --}}

    </div>
    @endforeach
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <tbody>
            @foreach ($subTotal as $subIndex => $subTotalValue)
            <tr>
                <td colspan="2" style="width: 100%;"><h2 style="text-align: left;">{{$subTotal['Jumlah']['nama_akun']}}</h2></td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['saldo_awal']['debit'], 0, ',', '.')}}</td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['saldo_awal']['kredit'], 0, ',', '.') }}</td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['mutasi']['debit'], 0, ',', '.') }}</td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['mutasi']['kredit'], 0, ',', '.') }}</td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['saldo_akhir']['debit'], 0, ',', '.') }}</td>
                <td class="jumlah" style="width: 0%; text-align: right;">{{ number_format($subTotal['Jumlah']['saldo_akhir']['kredit'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


</body>
</html>
