<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <title>Laba Rugi</title>
    {{-- <script src="{{ asset('js/paged_old.js') }}"></script> --}}
    <style>
        @import url('{{ asset('css/rpt.css') }}');
    </style>
    <script>
        setTimeout(() => {
            window.print()
        }, 5000);
    </script>
</head>

<body>
    @include('report.partials.header', [
        'reportTitle' => 'Laporan Laba Rugi',
        'reportPeriod' => 'Periode ' . date('d/m/Y', strtotime($start)) . ' s/d ' . date('d/m/Y', strtotime($end)),
    ])

    <div class="report-container">
        @foreach ($dataChunked as $pageIndex => $data)
            <div class="page-break">
                @foreach ($data as $category => $details)
                    <table class="main-data">
                        <h3>{{ $category }}</h3>
                        @foreach ($details['Detail'] as $item => $amount)
                            <tr>
                                <td class="data-desc">{{ $item }}</td>
                                <td class="data-num">{{ number_format($amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="padding: 0px; margin: 0px; font-weight: bold; font-size: 14px;">Total
                                {{ $category }}</td>
                            <td class="data-num" style="text-align: right; font-weight: bold; font-size: 12px;">
                                {{ number_format($details['Jumlah'], 0, ',', '.') }}</td>
                        </tr>
                        <div style="height: 5px;"></div>
                    </table>
                @endforeach
                <div style="height: 5px;"></div>
                <table class="main-data">
                    <tr>
                        <td style="padding: 0px; margin: 0px; font-weight: bold; font-size: 16px;">Saldo Laba (Rugi)
                            Tahun Berjalan</td>
                        <td
                            style="text-align: right; border-top: 2px solid black; width: 20%; font-weight: bold; font-size: 12px;">
                            <h3>{{ number_format($labaRugiBersih, 0, ',', '.') }}</h3>
                        </td>
                    </tr>
                </table>

                @include('report.partials.footer', [
                    'location' => $paged['alamat'],
                    'date' => $paged['tanggal'],
                    'preparedBy' => $paged['dibuat'],
                    'position' => $paged['jabatan'],
                ])

                <!-- Footer Nomor Halaman -->
            </div>
        @endforeach
</body>

</html>
