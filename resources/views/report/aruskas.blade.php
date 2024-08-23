<!DOCTYPE html>
<html>
<head>
    <title>Arus Kas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .section-title {
            font-weight: bold;
            margin-top: 5px;
        }
        .total-row {
            font-weight: bold;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 5rem;
            height: 5rem;
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .report-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .report-period {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo" class="company-logo">
        <div class="company-info">
            <div class="company-name">{{ auth()->user()->company_name }}</div>
        </div>
    </div>
    <div class="report-title">
        <h2><u>Arus Kas</u></h2>
    </div>
    <div class="report-period">
        <p>Periode: {{ $start_date }} - {{ $end_date }}</p>
    </div>

    @php
        $totalKas = 0;
    @endphp

    @foreach ($data as $kategori => $item)
        @if($kategori != 'Total')
            <div class="section-title">Arus Kas Dari {{ ucwords(str_replace('_', ' ', $kategori)) }}</div>
            <table>
                <tbody>
                    @foreach ($item as $nama_akun => $nilai)
                        @if($nama_akun != 'Total')
                            <tr>
                                <td>
                                @if($nilai > 0)
                                    Kenaikan (Penurunan)
                                @else
                                    Penurunan (Kenaikan)
                                @endif
                                {{ $nama_akun }}
                                </td>
                                <td style="text-align: right;">{{ number_format($nilai, 0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    {{-- <tr class="total-row">
                        <td>Total {{ ucwords(str_replace('_', ' ', $kategori)) }}</td>
                        <td style="text-align: right;">{{ number_format($item['Total'], 0) }}</td>
                    </tr> --}}
                </tbody>
            </table>
            @php
                $totalKas += $item['Total'];
            @endphp
        @endif
    @endforeach

    <table>
        <tbody>
            <tr class="total-row">
                <td>Kenaikan (Penurunan) Kas dan Setara Kas</td>
                <td style="text-align: right;">{{ number_format($data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'], 0) }}</td>
            </tr>
            <tr class="total-row">
                <td>Kas dan Setara Kas Awal</td>
                <td style="text-align: right;">{{ number_format($data['Total']['Kas dan Setara Kas Awal'], 0) }}</td>
            </tr>
            <tr class="total-row">
                <td>Kas dan Setara Kas Akhir</td>
                <td style="text-align: right;">{{ number_format($data['Total']['Kas dan Setara Kas Akhir'], 0) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
