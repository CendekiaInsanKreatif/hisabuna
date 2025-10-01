<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $orderId }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 60px;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 0;
            width: 400px;
        }
        .invoice-container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            max-width: 700px;
            margin: auto;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 50px;
        }
        .invoice-title {
            font-size: 22px;
            margin-top: 10px;
            color: #000;
            font-weight: bold;
        }
        .divider {
            border-top: 1px solid #999;
            margin: 20px 0;
        }
        .info p {
            margin: 4px 0;
        }
        .info strong {
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
        }
        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-stamp {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: green;
            border: 2px solid green;
            display: inline-block;
            padding: 4px 12px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-top: 40px;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .qr-code img {
            width: 120px;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <img class="watermark" src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Watermark">

    <div class="invoice-container">
        <div class="header">
            <img src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Logo">
            <div class="invoice-title">INVOICE</div>
        </div>

        <div class="divider"></div>

        <div class="info">
            <p><strong>Order ID:</strong> {{ $orderId }}</p>
            <p><strong>Nama:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Tanggal:</strong> {{ now()->format('d M Y') }}</p>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   <td>
                        @if(Str::startsWith($orderId, 'RENEW-'))
                            Perpanjangan Langganan {{ $user->profile }}
                        @elseif(Str::contains($orderId, 'UPGRADE-STANDARD-'))
                            Upgrade ke Standard
                        @elseif(Str::contains($orderId, 'UPGRADE-PRO-'))
                            Upgrade ke Pro
                        @else
                            Pembelian Langganan
                        @endif
                    </td>
                    <td>Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Total: Rp {{ number_format($amount, 0, ',', '.') }}</p>

        <div class="status-stamp" style="color: {{ $status === 'PAID' ? 'green' : ($status === 'PENDING' ? 'orange' : 'red') }}; border-color: {{ $status === 'PAID' ? 'green' : ($status === 'PENDING' ? 'orange' : 'red') }};">
            STATUS: {{ $status }}
        </div>

        @if($status !== 'PAID')
        <div style="margin-top: 20px; text-align: center; color: red; font-weight: bold;">
            Harap segera melakukan pembayaran untuk mengaktifkan layanan.
        </div>
        @endif

        <div class="qr-code">
            <p>Scan QR untuk detail pembayaran:</p>
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
        </div>

        <div class="divider"></div>

        <div class="footer">
            Terima kasih telah menggunakan layanan kami. Invoice ini dihasilkan secara otomatis.<br>
            Jika ada pertanyaan, silakan hubungi tim kami.
        </div>
    </div>
</body>
</html>
