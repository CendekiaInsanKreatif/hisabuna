<!DOCTYPE html>
<html>
<head>
    <title>Neraca Perbandingan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5px;
        }
        h1 {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid black;
        }
        th {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .indent-1 {
            padding-left: 20px;
        }
        .indent-2 {
            padding-left: 40px;
        }
    </style>
</head>
<body>
    <h1>Neraca Perbandingan</h1>
    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>{{ $tahunSekarang }}</th>
                <th>{{ $tahunSebelumnya }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $parent)
                <tr>
                    <td class="bold">{{ $parent['keterangan'] }}</td>
                    <td>{{ number_format($parent[$tahunSekarang]) }}</td>
                    <td>{{ number_format($parent[$tahunSebelumnya]) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
