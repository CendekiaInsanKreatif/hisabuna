<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            color: #374151;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(30, 58, 138, 0.10);
            border: 1px solid #e5e7eb;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
            padding: 32px 24px 24px 24px;
            color: white;
            text-align: center;
            position: relative;
        }

        .logo {
            display: block;
            margin: 0 auto 16px auto;
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .body {
            padding: 32px 24px 24px 24px;
            line-height: 1.7;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(90deg, #2563eb 0%, #059669 100%);
            color: #fff !important;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 28px;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
            transition: background 0.2s;
        }

        .btn:hover {
            background: linear-gradient(90deg, #059669 0%, #2563eb 100%);
        }

        .footer {
            background: #f3f4f6;
            text-align: center;
            padding: 18px;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .info {
            font-size: 14px;
            color: #059669;
            margin-top: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @php
                $companyLogo = auth()->user()->company_logo ?? null;
                $logoPath = $companyLogo ? public_path('storage/' . $companyLogo) : null;
            @endphp
            @if ($companyLogo && file_exists($logoPath))
                <img src="{{ asset('storage/' . $companyLogo) }}" alt="Logo" class="logo">
            @else
                <img src="{{ asset('images/icons/hisabuna-favicon.png') }}" alt="Logo" class="logo">
            @endif
            <h1>Permintaan Reset Password</h1>
        </div>
        <div class="body">
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mengatur ulang password akun Anda.<br>Silakan klik tombol di bawah ini
                untuk membuat password baru:</p>

            <a href="{{ $url }}" class="btn">Reset Password</a>

            <div class="info">Tautan ini hanya berlaku selama 60 menit.</div>
            <p style="margin-top:24px; color:#6b7280;">Jika Anda tidak meminta reset password, abaikan email ini.</p>

            <p style="margin-top:32px;">Salam hangat,<br><strong>Tim {{ config('app.name') }}</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
