<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arus Kas</title>
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    {{-- <script src="{{ asset('js/paged_old.js') }}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <header class="new-header">
        <img src="{{ asset('storage/' . (auth()->user()->company_logo ?? asset('images/icons/hisabuna-favicon.png'))) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Laporan Arus Kas </h2>
            <h3>Periode {{ $start_date }} s/d {{ $end_date }}</h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">

    @php
        $totalKas = 0;
    @endphp
@foreach ($dataChunked as $pageIndex => $data)
    @foreach ($data as $kategori => $item)
        @if($kategori != 'Total')
            <div class="section-title" style="font-size: 12px; font-weight: bold;">Arus Kas Dari {{ ucwords(str_replace('_', ' ', $kategori)) }}</div>
            <table>
                <tbody>
                    @php
                        $kasbersih = 0;
                    @endphp
                    @foreach ($item as $nama_akun => $nilai)
                        @if($nama_akun != 'Total')
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;
                                @if($nilai > 0)
                                    Kenaikan (Penurunan)
                                @else
                                    Penurunan (Kenaikan)
                                @endif
                                {{ $nama_akun }}
                                </td>
                                <td style="text-align: right; font-size: 12px; width: 20%;">
                                    @if($nilai < 0)
                                        ({{ number_format($nilai, 0, ',', '.') }})
                                    @else
                                        {{ number_format($nilai, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                            @php
                                $kasbersih += $nilai;
                            @endphp
                        @endif
                        @if($nama_akun == 'Total')
                            <tr>
                                <td style="font-weight: bold; font-size: 12px;">Kas Bersih dari {{ ucwords(str_replace('_', ' ', $kategori)) }}</td>
                                <td style="text-align: right; border-top: 1.5px solid black; font-weight: bold; font-size: 12px; width: 20%; margin-left: 20px;">
                                    {{ number_format($kasbersih, 0, ',', '.') }}
                                </td>
                            </tr>
                            <div style="height: 10px;"></div>
                        @endif
                    @endforeach
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
                <td style="font-weight: bold; font-size: 12px;">Kenaikan (Penurunan) Kas dan Setara Kas</td>
                <td style="text-align: right; font-weight: bold; font-size: 12px; width: 20%;">{{ number_format($data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'], 0, ',','.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Kas dan Setara Kas Awal</td>
                <td style="text-align: right; border-bottom: 1.5px solid black; width: 20%;">{{ number_format($data['Total']['Kas dan Setara Kas Awal'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td style="font-weight: bold; font-size: 12px;">Kas dan Setara Kas Akhir</td>
                <td style="text-align: right; font-weight: bold; font-size: 12px width: 20%;">{{ number_format($data['Total']['Kas dan Setara Kas Akhir'], 0, ',', '.') }}</td>
            </tr>
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
@endforeach
</body>
</html>
