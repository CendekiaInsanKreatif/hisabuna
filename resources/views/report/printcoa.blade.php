<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COA List</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 3px;
            background-color: #f4f6f9;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 3px;
        }
        h4 {
            text-align: center;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>{{ auth()->user()->company_name }}</h1>
    <h4>Chart of Account</h4>

    <table>
        <thead>
            <tr>
                <th style="text-align: center">Kode Akun</th>
                <th style="text-align: center">Nama Akun</th>
                <th style="text-align: center">Level</th>
                <th style="text-align: center">Golongan</th>
                <th style="text-align: center">Saldo Normal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->nomor_akun }}</td>
                    <td style="padding-left: {{ $item->level * 15 }}px;">{{ $item->nama_akun }}</td>
                    <td style="text-align: center">{{ $item->level }}</td>
                    <td>{{ $item->golongan }}</td>
                    <td style="text-align: center">{{ $item->saldo_normal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
