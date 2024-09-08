<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3px;
            /* background-color: #fff; */
        }


        table.main-data tr:nth-child(odd) {
            /* background-color: #f4f4f5;? */
        }

        @media print {
            body {
                display: block;
            }
            footer {
                page-break-after: always;
            }
        }


        table {
            width: 100%;
            /* border-collapse: collapse; */
        }

        table.main-data tr td {
            padding: 2px 2px 2px 22px;
        }

        table.main-data tr:nth-last-child(2) td.data-num {
            border-bottom: solid 1px black;
        }

        .data-desc {
            width: 80%;
        }

        .data-num {
            text-align: right;
        }

        h3 {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        h2 {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }


        .total {
            font-size: 11px;
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
    </style>
</head>
<body onload="window.print()">
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Laporan Laba Rugi</h2>
            <h3>Periode : {{$start}} - {{$end}}</h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    {{-- <button id="pdf" style="background-color: red;"><p style="color: #ddd">Download PDF</p></button> --}}
    <div class="report-container">
        {{-- <header style="border-bottom: 2px solid #ddd; padding: 3px">
            <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
            <div style="overflow: hidden;">
                <h1 style="text-align:center; font-size: 20px;">{{ auth()->user()->company_name }}</h1>
                <p style="text-align:center;">Laporan Laba Rugi</p>
                <p style="text-align:center;">Periode {{$start}} - {{$end}}</p>
            </div>
        </header> --}}
        @foreach ($data as $category => $details)
        <table class="main-data">
            <h3>{{ $category }}</h3>
            @foreach ($details['Detail'] as $item => $amount)
                <tr>
                    <td class="data-desc"><h3>{{ $item }}</h3></td>
                    <td class="data-num">{{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td style="padding: 2px">Total {{ $category }}</td>
                <td style="text-align: right;">{{ number_format($details['Jumlah'], 0, ',', '.') }}</td>
            </tr>
        </table>
        @endforeach
        <div style="padding: 2px;">
            <table>
                <tr class="total">
                    <td><h2>Saldo Laba (Rugi) Tahun Berjalan</h2></td>
                    <td style="text-align: right; border-top: 4px solid black; border-bottom: 4px solid black; width: 20%;">{{ number_format($labaRugiBersih, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <footer>
            <table style="width: 100%; margin-top: 70px;">
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
                    <div><strong>Manajer Keuangan</strong></div>
                </td>
            </tr>
        </table>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $('#pdf').click(function() {
                var start = "{{ $start }}";
                var end = "{{ $end }}";
                $.ajax({
                    url     :'{{ route("report.labarugiprint") }}',
                    type    :'post',
                    data:{
                        start:start,
                        end:end
                    },
                    success : function(res) {

                    }
                })
            })
        })
    </script>
</body>
</html>
