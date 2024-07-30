<!DOCTYPE html>
<html>
<head>
    <title>Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        
        .footer.content {
            display: flex;
            align-items: center;
        }

        .company-logo {
            width: 5rem;
            height: 5rem;
            margin-right: 8rem;
        }

        .company-name {
            font-size: 1.25rem; /* Ukuran font yang sesuai */
            position: relative;
            top: -1.5rem; /* Sesuaikan nilai ini sesuai kebutuhan */
        }
    </style>
</head>
<body>
<table>
    <div class="footer content">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo" class="company-logo">
        <span class="company-name">{{ auth()->user()->company_name }}</span>
    </div>
    <h2><u>NERACA</u></h2>  
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
<table style="width: 100%; margin-top: 70px; border-top: 1px solid black; padding-top: 20px;">
    <tr>
        <td style="text-align: center; width: 35%;">
            <div>Dibuat oleh, {{ $ttd1 }}</div>
            <div style="height: 80px;"></div>
            <div><strong>Staff Keuangan</strong></div>
        </td>
        <td style="width: 10%;"></td>
        <td style="width: 10%;"></td>
        <td style="width: 10%;"></td>
        <td style="text-align: center; width: 35%;">
            <div>Disetujui oleh, {{ $ttd2 }}</div>
            <div style="height: 80px;"></div>
            <div><strong>Manager Keuangan</strong></div>
        </td>
    </tr>
</table>

</body>
</html>
