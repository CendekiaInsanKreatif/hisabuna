{{--
    Report Header Component

    Props:
    - $reportTitle: string (required) - Judul laporan (contoh: "Laporan Laba Rugi")
    - $reportPeriod: string (optional) - Periode laporan
    - $showLogo: boolean (optional, default: true) - Tampilkan logo perusahaan
    - $companyName: string (optional) - Nama perusahaan (default: auth user company)
--}}

@php
    $showLogo = $showLogo ?? true;
    $companyName = $companyName ?? auth()->user()->company_name;
    $companyLogo = auth()->user()->company_logo;
    
    // Use absolute file path for wkhtmltopdf PDF generation
    // This ensures logo displays correctly in generated PDFs
    $logoPath = $companyLogo ? public_path('storage/' . $companyLogo) : null;
    
    // Check if file exists to avoid broken images
    if ($logoPath && !file_exists($logoPath)) {
        $logoPath = null;
    }
@endphp

<header class="report-header">
    <div class="header-container">
        @if ($showLogo && $logoPath)
            <div class="logo-container">
                <img src="{{ $logoPath }}" alt="Logo Perusahaan" class="company-logo">
            </div>
        @endif

        <div class="header-content">
            <h1 class="company-name">{{ $companyName }}</h1>
            <h2 class="report-title">{{ $reportTitle }}</h2>
            @if (isset($reportPeriod) && $reportPeriod)
                <h3 class="report-period">{{ $reportPeriod }}</h3>
            @endif
        </div>
    </div>
</header>

<div class="header-divider"></div>

<style>
    .report-header {
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 10px 0;
    }

    .logo-container {
        flex-shrink: 0;
    }

    .company-logo {
        max-width: 80px;
        max-height: 80px;
        object-fit: contain;
    }

    .header-content {
        text-align: center;
        flex: 1;
    }

    .company-name {
        font-size: 20px;
        font-weight: bold;
        margin: 0 0 8px 0;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 5px 0;
        color: #2d3748;
    }

    .report-period {
        font-size: 13px;
        font-weight: normal;
        margin: 0;
        color: #4a5568;
    }

    .header-divider {
        border: none;
        border-top: 2px solid #000;
        margin: 15px 0 20px 0;
    }

    /* Print-specific styles */
    @media print {
        .report-header {
            margin-bottom: 15px;
        }

        .company-logo {
            max-width: 70px;
            max-height: 70px;
        }

        .company-name {
            font-size: 18px;
        }

        .report-title {
            font-size: 15px;
        }

        .report-period {
            font-size: 12px;
        }
    }

    /* Responsive for smaller screens */
    @media screen and (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 10px;
        }

        .company-logo {
            max-width: 60px;
            max-height: 60px;
        }

        .company-name {
            font-size: 16px;
        }

        .report-title {
            font-size: 14px;
        }

        .report-period {
            font-size: 12px;
        }
    }
</style>
