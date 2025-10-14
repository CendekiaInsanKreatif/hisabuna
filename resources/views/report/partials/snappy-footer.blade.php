<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            padding: 10px 15px;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .footer-container {
            width: 100%;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .signature-table {
            width: 100%;
            margin-top: 10px;
        }

        .signature-cell {
            text-align: center;
            vertical-align: bottom;
        }

        .location-date {
            font-size: 10px;
            margin-bottom: 50px;
        }

        .name {
            font-size: 10px;
            font-weight: bold;
            border-top: 2px solid #000;
            display: inline-block;
            padding-top: 3px;
            min-width: 150px;
        }

        .position {
            font-size: 9px;
            margin-top: 2px;
        }

        .page-number {
            font-size: 9px;
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="footer-container">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="location-date">{{ $location ?? 'Jakarta' }},
                        {{ $date ?? now()->locale('id')->translatedFormat('d F Y') }}</div>
                    <div class="name">{{ $preparedBy ?? 'Dibuat Oleh' }}</div>
                    <div class="position">{{ $position ?? 'Jabatan' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
