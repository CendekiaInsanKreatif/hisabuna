<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            padding: 10px 15px;
            font-family: Arial, sans-serif;
        }

        .header-container {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .logo-cell {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }

        .logo-cell img {
            max-width: 70px;
            max-height: 70px;
            display: block;
        }

        .content-cell {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 3px 0;
        }

        .report-period {
            font-size: 11px;
            margin: 0;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="header-container">
        @if (($showLogo ?? true) && auth()->user()->company_logo)
            <div class="logo-cell">
                <img src="{{ public_path('storage/' . auth()->user()->company_logo) }}" alt="Logo">
            </div>
        @endif
        <div class="content-cell">
            <div class="company-name">{{ $companyName ?? auth()->user()->company_name }}</div>
            <div class="report-title">{{ $reportTitle ?? '' }}</div>
            @if (isset($reportPeriod) && $reportPeriod)
                <div class="report-period">{{ $reportPeriod }}</div>
            @endif
        </div>
    </div>
</body>

</html>
