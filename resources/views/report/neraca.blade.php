<!DOCTYPE html>
<html>
<head>
    <title>Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .category, .total {
            font-weight: bold;
        }
        .total {
            background-color: #f2f2f2;
        }
        h2, h4 {
            margin: 0;
        }
    </style>
</head>
<body>
<table>
    @foreach ($data as $category => $subcategories)
        <tr>
            <th colspan="2" class="category"><h2>{{ $category }}</h2></th>
        </tr>
        @foreach ($subcategories as $subcategory => $items)
            <tr>
                <td colspan="2" class="subcategory"><h4>&nbsp;&nbsp;&nbsp;{{ $subcategory }}</h4></td>
            </tr>
            @foreach ($items as $item => $value)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item }}</td>
                    <td style="text-align: right">{{ number_format($value, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="total">Total {{ $subcategory }}</td>
                <td class="total" style="text-align: right">{{ number_format($totals[$category][$subcategory], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="total">Total {{ $category }}</td>
            <td class="total" style="text-align: right">{{ number_format($totals[$category]['Total'], 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
