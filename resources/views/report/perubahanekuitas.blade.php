<?php

$head = array_keys($data);
$sub = array_keys($data[$head[0]]);

foreach ($head as $header) {
    $data[$header]['Jumlah Ekuitas'] = $data[$header]['Modal Disetor'] + $data[$header]['Saldo Tahun Berjalan'];
}

$sub[] = 'Jumlah Ekuitas';

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container" style="text-align: center;">
    <h2>Laporan Perubahan Ekuitas</h2>
    <table>
        <thead>
            <tr>
                <th></th>
                @foreach ($head as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($sub as $subHeader)
                <tr>
                    <td>{{ $subHeader }}</td>
                    @foreach ($head as $header)
                        <td>{{ number_format($data[$header][$subHeader], 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
