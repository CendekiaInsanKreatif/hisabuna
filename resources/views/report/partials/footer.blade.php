{{--
    Report Footer Component

    Props:
    - $location: string (optional) - Lokasi penandatanganan (default: alamat perusahaan)
    - $date: string (optional) - Tanggal penandatanganan (default: tanggal hari ini)
    - $preparedBy: string (optional) - Nama pembuat laporan (default: "Dibuat Oleh")
    - $position: string (optional) - Jabatan penandatangan (default: "Direktur")
    - $showSignature: boolean (optional, default: true) - Tampilkan area tanda tangan
    - $signatureHeight: string (optional, default: '80px') - Tinggi area tanda tangan
--}}

@php
// dd($location);
    $location = $location ?? (auth()->user()->company_address ?? 'Jakarta');
    $date = $date ?? \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
    $preparedBy = $preparedBy ?? 'Dibuat Oleh';
    $position = $position ?? 'Direktur';
    $showSignature = $showSignature ?? true;
    $signatureHeight = $signatureHeight ?? '80px';
@endphp

<footer class="report-footer">
    <div class="footer-container">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-location">{{ $location }}, {{ $date }}</div>

                    @if ($showSignature)
                        <div class="signature-space" style="height: {{ $signatureHeight }};"></div>
                    @endif

                    <div class="signature-name">{{ $preparedBy }}</div>
                    <div class="signature-line"></div>
                    <div class="signature-position">{{ $position }}</div>
                </td>
            </tr>
        </table>
    </div>
</footer>

<style>
    .report-footer {
        margin-top: 40px;
        page-break-inside: avoid;
    }

    .footer-container {
        width: 100%;
    }

    .signature-table {
        width: 100%;
        margin-top: 30px;
        border-collapse: collapse;
    }

    .signature-cell {
        text-align: center;
        width: 100%;
        vertical-align: top;
        padding: 10px;
    }

    .signature-location {
        font-size: 12px;
        color: #2d3748;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .signature-space {
        margin: 0 auto;
        min-height: 60px;
    }

    .signature-name {
        font-size: 12px;
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .signature-line {
        width: 150px;
        border-bottom: 2px solid #000;
        margin: 0 auto 5px auto;
    }

    .signature-position {
        font-size: 12px;
        color: #4a5568;
        font-weight: 500;
    }

    /* Print-specific styles */
    @media print {
        .report-footer {
            margin-top: 30px;
        }

        .signature-table {
            margin-top: 20px;
        }

        .signature-location,
        .signature-name,
        .signature-position {
            font-size: 11px;
        }

        .signature-line {
            width: 130px;
        }
    }

    /* Multiple signature layout (optional, can be used with slot override) */
    .signature-row {
        display: flex;
        justify-content: space-around;
        gap: 40px;
        margin-top: 30px;
    }

    .signature-box {
        flex: 1;
        text-align: center;
        max-width: 200px;
    }

    /* Page number footer (for multi-page reports) */
    .page-number {
        position: fixed;
        bottom: 10px;
        right: 20px;
        font-size: 10px;
        color: #718096;
    }

    @media print {
        .page-number {
            position: fixed;
            bottom: 5px;
            right: 15px;
        }
    }
</style>

{{-- Optional: Page number display (uncomment if needed) --}}
{{-- <div class="page-number">
    Halaman <span class="page"></span> dari <span class="topage"></span>
</div> --}}
