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
    <div class="footer content">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Company Logo" class="company-logo">
        <span class="company-name">{{ auth()->user()->company_name }}</span>
    </div>
    <h2><u>Arus Kas</u></h2>
    {{-- <h1>Arus Kas</h1> --}}
    <p>Periode: {{ $start_date }} - {{ $end_date }}</p>

    @php
        $totalKas = 0;
    @endphp

    @foreach ($data as $kategori => $item)
        <div class="section-title">Arus Kas Dari {{ ucwords(str_replace('_', ' ', $kategori)) }}</div>
        <table>
            <tbody>
                @foreach ($item['Detail'] as $nama_akun => $nilai)
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
                @endforeach
                <tr class="total-row">
                    <td>Total {{ ucwords(str_replace('_', ' ', $kategori)) }}</td>
                    <td style="text-align: right;">{{ number_format($item['Jumlah'], 0) }}</td>
                </tr>
            </tbody>
        </table>
        @php
            $totalKas += $item['Jumlah'];
        @endphp
    @endforeach

    <div class="section-title">Kas dan Setara Kas Akhir</div>
    <table>
        <tbody>
            <tr class="total-row">
                <td>Total Kas dan Setara Kas Akhir</td>
                <td style="text-align: right;">{{ number_format($totalKas, 0) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
