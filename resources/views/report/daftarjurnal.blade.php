<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jurnal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 100%;
            margin: auto;
            background-color: #fff;
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
        }
        th, td {
            padding: 8px;
            font-size: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
    </style>
</head>
<body>
    <div class="container" style="overflow-x: auto; overflow-y: hidden; display: flex; justify-content: center;">
        <div class="header-content" style="text-align: center; width: 100%;">
            <div id="titleHeader" class="header-text" style="display: flex; align-items: center; justify-content: center; flex-direction: row;">
                <img src="https://cdn.pixabay.com/photo/2017/07/25/11/59/logo-2537871_1280.png" alt="Logo" class="logo" style="margin-right: 10px; align-self: center;">
                <div>
                    <h2 style="padding: 0; margin: 0; margin-top: 20px;">{{ auth()->user()->company_name }}</h2>
                </div>
            </div>
            <br>
            <div class="title">
                <h2>DAFTAR JURNAL</h2>
                <p class="period">Periode 01/11/2023 s/d 30/11/2023</p>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th colspan="2" style="text-align: center">Transaksi</th>
                    <th rowspan="2" style="text-align: center">Keterangan Jurnal</th>
                    <th colspan="3">Nomor Jurnal</th>
                    <th rowspan="2">Jumlah</th>
                </tr>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>RV</th>
                    <th>PV</th>
                    <th>JV</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnal as $item)
                    <tr style="{{ $item->jenis == 'RV' ? 'background-color: #fee2e2;' : ($item->jenis == 'PV' ? 'background-color: #f4f4f5;' : ($item->jenis == 'JV' ? 'background-color: #fef9c3;' : '')) }}">
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->jurnal_tgl)->format('j/m/y') }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>{{ $item->jenis == 'RV' ? $item->no_transaksi : '' }}</td>
                        <td>{{ $item->jenis == 'PV' ? $item->no_transaksi : '' }}</td>
                        <td>{{ $item->jenis == 'JV' ? $item->no_transaksi : '' }}</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
