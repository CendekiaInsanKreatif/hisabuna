<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jurnal</title>
    <link rel="icon" href="{{ asset('storage/' . auth()->user()->company_logo) }}">
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <style>
        body {
        font-family: 'Arial', sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .container {
        width: 90%; /* Pastikan lebar container 100% */
        max-width: 1024px; /* Atur lebar maksimum agar tidak terlalu lebar di layar besar */
        margin: 0 auto; /* Pastikan margin otomatis untuk meratakan container di tengah */
        padding: 20px; /* Ruang di dalam container */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); /* Mengurangi intensitas shadow agar lebih halus */
        background-color: #fff; /* Tambahkan warna background putih agar lebih rapi */
    }

    @media print {
        .container {
            box-shadow: none; /* Hapus shadow saat dicetak */
            padding: 0; /* Mengurangi padding untuk cetakan */
            margin: 0; /* Hapus margin untuk cetakan agar sesuai dengan kertas */
        }

        table {
            page-break-inside: avoid; /* Menghindari pemotongan tabel di halaman cetak */
        }
    }


        .logo {
            width: 50px;
            height: 50px;
            background-color: #000;
            margin: 0;
            padding: 0;
            margin-left: 30px;
            align-self: flex-start;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #333;
            margin: auto; /* Memastikan tabel di tengah */
        }

        th, td {
            padding: 5px;
            font-size: 12px;
            text-align: left;
        }

        th:nth-child(1), th:nth-child(2), th:nth-child(4) {
            text-align: center;
            width: 10%; /* Lebar yang seragam untuk nomor transaksi dan jenis jurnal */
        }

        td:nth-child(1), td:nth-child(4) {
            text-align: center;
            width: 10%; /* Memastikan keseragaman lebar */
        }

        td:nth-child(3) {
            word-wrap: break-word;
            width: 60%; /* Mengatur kolom keterangan lebih besar tapi tidak menekan kolom lain */
            text-align: left; /* Pastikan teks rata kiri */
        }

        td:nth-child(4) {
            text-align: right;
            width: 20%; /* Lebar lebih besar untuk memastikan angka tidak menekan margin */
        }

        .period {
            text-align: center;
            margin-top: 5px;
            font-style: italic;
        }

        .new-header {
            position: relative;
        }

        .company-logo {
            position: absolute;
            top: 3%;
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
<body>
    <div class="container" style="overflow-x: auto; overflow-y: hidden; display: flex; justify-content: center;">
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Daftar Jurnal</h2>
            <h3>Jurnal ke {{ $tgl_awal }} s/d {{ $tgl_akhir }}</h3>
        </div>
    </header>
        <!-- <div class="header-content" style="text-align: center; width: 100%;">
            <div id="titleHeader" class="header-text" style="display: flex; align-items: center; justify-content: center; flex-direction: row;">
                <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo" style="width: 6rem; margin-right: 15px;">
                <div style="display: flex; align-items: center;">
                    <h2 style="padding: 0; margin: 0;">{{ auth()->user()->company_name }}</h2>
                </div>
            </div>
            <br>
            <div class="title">
                <h2>DAFTAR JURNAL</h2>
                <p class="period">Periode {{ $tgl_awal }} s/d {{ $tgl_akhir }}</p>
            </div>
        </div> -->

        <table>
            <thead style="border-bottom: 1px solid #333;">
                <tr>
                    <th style="text-align: center;">Nomor Transaksi</th>
                    <th style="text-align: center;">Jenis Jurnal</th>
                    <th style="text-align: left;">Keterangan Transaksi</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnal as $item)
                    <tr style="{{ $item->jenis == 'RV' ? 'background-color: #fee2e2;' : ($item->jenis == 'PV' ? 'background-color: #f4f4f5;' : ($item->jenis == 'JV' ? 'background-color: #fef9c3;' : '')) }} border-bottom: 1px solid #333;">
                        <td style="text-align: center;">{{ $item->no_urut_transaksi }}</td>
                        <td style="text-align: center;">{{ $item->jenis }}</td>
                        <td style="word-wrap: break-word; max-width: 60%;">{{ $item->keterangan }}</td>
                        <td style="text-align: right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



    </div>
</body>
</html>
