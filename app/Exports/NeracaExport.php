<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class NeracaExport implements FromView, WithStyles
{
    protected $data;
    protected $label;
    protected $periode;
    protected $paged;

    public function __construct($data, $label, $periode, $paged)
    {
        $this->data = $data;
        $this->label = $label;
        $this->periode = $periode;
        $this->paged = $paged;
    }

    public function view(): View
    {
        return view('report.neraca', [
            'data' => $this->data,
            'label' => $this->label,
            'periode' => $this->periode,
            'paged' => $this->paged,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A1', auth()->user()->company_name);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A2', 'Laporan Posisi Keuangan');
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true);

        $sheet->mergeCells('A3:B3');
        $sheet->getStyle('A3')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A3', 'Per '. \Carbon\Carbon::parse($this->periode)->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->setBold(true);
    }
}
