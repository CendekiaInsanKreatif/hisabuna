<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jurnal</title>
    <script src="{{ asset('js/paged_old.js') }}"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            margin: auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        }
        th, td {
            padding: 5px;
            font-size: 12px;
            text-align: left;
        }
        th:nth-child(1), th:nth-child(4), th:nth-child(8) {
            text-align: center;
        }
        td:nth-child(1), td:nth-child(8) {
            text-align: right;
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
            <h2>Laporan Laba Rugi</h2>
            <h3>Periode {{ $tgl_awal }} s/d {{ $tgl_akhir }}</h3>
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
                    <th style="text-align: center; width: 30px;">Nomor Transaksi</th>
                    <th style="text-align: center; width: 30px;">Jenis Jurnal</th>
                    <th style="text-align: center">Keterangan Transaksi</th>
                    <th>Jumlah</th>
                </tr>
                <tr>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnal as $item)
                    <tr style="{{ $item->jenis == 'RV' ? 'background-color: #fee2e2;' : ($item->jenis == 'PV' ? 'background-color: #f4f4f5;' : ($item->jenis == 'JV' ? 'background-color: #fef9c3;' : '')) }} border-bottom: 1px solid #333;">
                        <td style="text-align: center">{{ $item->no_urut_transaksi }}</td>
                        <td style="text-align: center">{{ $item->jenis }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td style="text-align: right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
