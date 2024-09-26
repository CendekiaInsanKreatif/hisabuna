<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chart of Account</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            margin: 0;
            padding: 0;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .new-header {
            position: relative;
            margin-bottom: 20px;
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
    {{-- <div id="btnDownload" style="position: absolute; top: 10px; right: 10px; padding: 5px; background-color: #f8f9fa; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        @if ($bType == 'preview')
            <a href="{{ route('report.print-coa') }}" class="btn" style="background-color: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background-color 0.3s ease;">
                Download
            </a>
        @endif
    </div> --}}
    <header class="new-header">
        <img src="{{ asset('storage/' . auth()->user()->company_logo) }}" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1>{{ auth()->user()->company_name }}</h1>
            <h2>Chart of Account</h2>
        </div>
    </header>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="text-align: center">Kode Akun</th>
                <th style="text-align: center">Nama Akun</th>
                <th style="text-align: center">Level</th>
                <th style="text-align: center">Golongan</th>
                <th style="text-align: center">Saldo Normal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                @php
                    $formattedNomorAkun = preg_replace('/\D/', '', $item->nomor_akun);
                    if (strlen($formattedNomorAkun) > 6) {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3, 2) . '-' . substr($formattedNomorAkun, 5);
                    } elseif (strlen($formattedNomorAkun) > 4) {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3);
                    } else {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3);
                    }
                @endphp

                <tr>
                    <td>{{ $formattedNomorAkun }}</td>
                    <td style="padding-left: {{ $item->level * 15 }}px;">{{ $item->nama_akun }}</td>
                    <td style="text-align: center">{{ $item->level }}</td>
                    <td style="text-align: center">{{ $item->golongan }}</td>
                    <td style="text-align: center">{{ ucwords($item->saldo_normal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
