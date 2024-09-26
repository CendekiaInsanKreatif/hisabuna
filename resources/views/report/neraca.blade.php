<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Laporan {{$label}}</title>
        <script src="{{ asset('js/paged_old.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style type="text/css">
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                max-width: 800px;
                margin: 0 auto;
            }

            h3 {
                margin: 0;
                padding: 0;
            }

            h2 {
                margin: 0;
                padding: 0;
            }

            p {
                margin: 0;
                padding: 0;
            }

            .amount {
                float: right;
                text-align: right;
                margin-left: 20px;
            }

            table {
                width: 100%;
            }

            .text-left {
                text-align: left;
            }

            .new-header {
                position: relative;
            }

            .company-logo {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100px;
            }

            /* #page-number-container {
                font-size: 12px;
                color: #000;
            }

            @media print {
                .page-number {
                    position: fixed;
                    bottom: 10px;
                    right: 10px;
                }
            } */

            @page {
            size: A4;
            margin: 30px;
            padding: 0;
            counter-reset: count 3;
            

            @bottom-center {
                content: counter(count);
            }
        }
        
        </style>
        <script>
        </script>
    </head>

    <body onload="window.print()">
        <header class="new-header">
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo" />
            <div class="header" style="text-align: center;">
                <h1>{{ auth()->user()->company_name }}</h1>
                <h2>Laporan Posisi Keuangan</h2>
                <h3>Per {{ $periode }}</h3>
            </div>
        </header>
        <hr style="border: 2px solid black; width: 100%;" />
        <table style="border-spacing: 6px 2px;">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    @foreach (array_keys($data) as $year)
                    <th style="text-align: right; font-size: 18px;">{{ $year }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data[array_key_first($data)] as $level1 => $level1Data) @if (is_array($level1Data))
                <tr>
                    <td colspan="{{ count($data) + 1 }}">
                        <h3>{{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
                    </td>
                </tr>
                @php $totalLevel1 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level1Data as $level2 => $level2Data) @if (is_array($level2Data))
                <tr>
                    <td colspan="{{ count($data) + 1 }}">
                        <h3>&nbsp;&nbsp;&nbsp;&nbsp; {{ $level2 }}</h3>
                    </td>
                </tr>
                @php $totalLevel2 = array_fill_keys(array_keys($data), 0); @endphp @foreach ($level2Data as $level3 => $amount)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $level3 }}</td>
                    @foreach ($data as $year => $yearData) @php $amount = $yearData[$level1][$level2][$level3] ?? 0; $totalLevel2[$year] += $amount; $totalLevel1[$year] += $amount; @endphp
                    <td style="text-align: right;">
                        {{ number_format($amount, 0, ',', '.') }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
                <tr>
                    <td>
                        <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah {{ $level2 }}</h3>
                    </td>
                    @foreach ($totalLevel2 as $year => $total)
                    <td style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                        <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                    </td>
                    @endforeach
                </tr>
                @endif @endforeach
                <tr>
                    <td>
                        <h3>JUMLAH {{ $level1 == '1' ? 'ASET' : strtoupper($level1) }}</h3>
                    </td>
                    @foreach ($totalLevel1 as $year => $total)
                    <td style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                        <h3>{{ number_format($total, 0, ',', '.') }}</h3>
                    </td>
                    @endforeach
                </tr>
                <br />
                @endif @endforeach
            </tbody>
        </table>
        <table style="width: 100%; margin-top: 70px;">
            <tr>
                <td style="text-align: center; width: 100%;">
                    <div style="font-size: 12px;">{{ $paged['alamat'] }}, {{ $paged['tanggal'] }}</div>
                    <div style="height: 80px;"></div>
                    <div style="font-size: 12px;">{{ $paged['dibuat'] }}</div>
                    <div style="border-bottom: 2px solid black; width: 100px; margin-left: auto; margin-right: auto;"></div>
                    <div style="font-size: 12px;">{{ $paged['jabatan'] }}</div>
                </td>
            </tr>
        </table>
    </body>
</html>

