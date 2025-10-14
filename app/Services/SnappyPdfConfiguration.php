<?php

namespace App\Services;

use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use Illuminate\Support\Facades\Auth;

trait SnappyPdfConfiguration
{
    /**
     * Configure Snappy PDF with standard settings for reports
     *
     * @param  string  $orientation  'portrait' or 'landscape'
     * @param  array  $options  Additional options to override defaults
     */
    protected function configureSnappyPdf(string $orientation = 'portrait', array $options = []): void
    {
        SnappyPDF::setBinary(config('snappy.pdf.binary'));
    }

    /**
     * Generate header HTML for Snappy PDF
     */
    protected function generateSnappyHeader(string $reportTitle, ?string $reportPeriod = null, array $options = []): string
    {
        return view('report.partials.snappy-header', array_merge([
            'reportTitle' => $reportTitle,
            'reportPeriod' => $reportPeriod,
            'showLogo' => true,
            'companyName' => Auth::user()->company_name ?? '',
        ], $options))->render();
    }

    /**
     * Generate footer HTML for Snappy PDF
     */
    protected function generateSnappyFooter(array $footerData = []): string
    {
        return view('report.partials.snappy-footer', array_merge([
            'location' => 'Jakarta',
            'date' => now()->locale('id')->translatedFormat('d F Y'),
            'preparedBy' => Auth::user()->name ?? 'Dibuat Oleh',
            'position' => 'Jabatan',
        ], $footerData))->render();
    }

    /**
     * Get standard Snappy PDF options
     */
    protected function getSnappyOptions(string $orientation = 'portrait', array $customOptions = []): array
    {
        $defaultOptions = [
            'margin-top' => 30,
            'margin-right' => 15,
            'margin-bottom' => 25,
            'margin-left' => 15,
            'encoding' => 'UTF-8',
            'enable-local-file-access' => true,
            'footer-right' => 'Halaman [page] dari [toPage]',
            'footer-font-size' => 9,
            'footer-spacing' => 5,
            'footer-font-name' => 'Arial',
        ];

        return array_merge($defaultOptions, $customOptions);
    }

    /**
     * Create PDF with standard configuration
     *
     * @return mixed
     */
    protected function createSnappyPdf(string $view, array $data, string $orientation = 'portrait', array $options = [])
    {
        $this->configureSnappyPdf($orientation, $options);

        $pdf = SnappyPDF::loadView($view, $data);

        $paperSize = $options['paperSize'] ?? 'A4';
        $snappyOptions = $this->getSnappyOptions($orientation, $options);

        $pdf->setPaper($paperSize, $orientation);

        foreach ($snappyOptions as $key => $value) {
            $pdf->setOption($key, $value);
        }

        return $pdf;
    }

    /**
     * Create PDF with header and footer
     *
     * @return mixed
     */
    protected function createSnappyPdfWithHeaderFooter(
        string $view,
        array $data,
        string $reportTitle,
        ?string $reportPeriod = null,
        array $footerData = [],
        string $orientation = 'portrait',
        array $options = []
    ) {
        $this->configureSnappyPdf($orientation, $options);

        $pdf = SnappyPDF::loadView($view, $data);

        $paperSize = $options['paperSize'] ?? 'A4';
        $snappyOptions = $this->getSnappyOptions($orientation, $options);

        // Generate header and footer HTML files temporarily
        $headerHtml = $this->generateSnappyHeader($reportTitle, $reportPeriod);
        $footerHtml = $this->generateSnappyFooter($footerData);

        // Save to temp files
        $headerPath = storage_path('app/temp/header-'.uniqid().'.html');
        $footerPath = storage_path('app/temp/footer-'.uniqid().'.html');

        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        file_put_contents($headerPath, $headerHtml);
        file_put_contents($footerPath, $footerHtml);

        $snappyOptions['header-html'] = $headerPath;
        $snappyOptions['footer-html'] = $footerPath;

        $pdf->setPaper($paperSize, $orientation);

        foreach ($snappyOptions as $key => $value) {
            $pdf->setOption($key, $value);
        }

        // Clean up temp files after PDF generation
        register_shutdown_function(function () use ($headerPath, $footerPath) {
            if (file_exists($headerPath)) {
                unlink($headerPath);
            }
            if (file_exists($footerPath)) {
                unlink($footerPath);
            }
        });

        return $pdf;
    }
}
