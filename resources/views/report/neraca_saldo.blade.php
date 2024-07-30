<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            background-color: #fff;
        }
        th, td {
            padding: 2px;
            text-align: left;
        }
        th {
            text-align: center;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 24px;
            margin: 5px 0;
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
        {{-- <caption>Neraca Saldo</caption>
        <caption>{{ auth()->user()->company_name }}</caption> --}}
        <div class="footer content">
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo" class="company-logo">
            <span class="company-name">{{ auth()->user()->company_name }}</span>
        </div>
        <h2><u>Neraca Saldo</u></h2>
        <tr>
            <th>Keterangan</th>
            <th>Saldo</th>
        </tr>
        @foreach($data as $mainCategory => $subcategories)
            <tr>
                <td colspan="2"><strong>{{ $mainCategory }}</strong></td>
            </tr>
            @foreach($subcategories as $subcategory => $accounts)
                @if($subcategory !== 'Total')
                    @foreach($accounts as $account => $balance)
                        @if($account !== 'Total')
                            <tr>
                                <td style="padding-left: 30px;">{{ $account }}</td>
                                <td style="text-align: right">{{ number_format($balance, 0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td style="padding-left: 30px;"><strong>Total {{ $subcategory }}</strong></td>
                        <td style="text-align: right"><strong>{{ number_format($accounts['Total'], 0) }}</strong></td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td><strong>Total {{ $mainCategory }}</strong></td>
                <td style="text-align: right"><strong>{{ number_format($subcategories['Total'], 0) }}</strong></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
