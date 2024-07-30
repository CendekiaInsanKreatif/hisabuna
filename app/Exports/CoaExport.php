<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Coa;
use Illuminate\Support\Facades\Auth;

class CoaExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new CoaImportSheet(),
            new CoaExistingSheet()
        ];
    }
}

class CoaImportSheet implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'Kode Akun',
            'Nama Akun',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle('Data Coa untuk Import');

                // Set lebar kolom
                $sheet->getColumnDimension('A')->setWidth(15); // Kode Akun
                $sheet->getColumnDimension('B')->setWidth(35); // Nama Akun

                // Set tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(20);

                // Set gaya header
                $sheet->getStyle('A1:B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E0E0E0',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                for ($row = 2; $row <= 10; $row++) {
                    $sheet->setCellValue("D{$row}", '=IF(ISBLANK(A' . $row . '), "", IF(ISNUMBER(FIND("-", A' . $row . ')), 5, LEN(A' . $row . ')))');
                }
            }
        ];
    }
}

class CoaExistingSheet implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents
{
    public function collection()
    {
        $coa = Coa::whereNull('is_deleted')
            ->where('created_by', Auth::user()->id)
            ->orderBy('nomor_akun')
            ->get(['nomor_akun', 'nama_akun']);

        $coa->transform(function ($item) {
            $item->level = $this->calculateLevel($item->nomor_akun);
            return $item;
        });

        return $coa;
    }

    public function headings(): array
    {
        return [
            'Kode Akun',
            'Nama Akun',
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set lebar kolom
                $sheet->getColumnDimension('A')->setWidth(15); // Kode Akun
                $sheet->getColumnDimension('B')->setWidth(35); // Nama Akun

                // Set tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(20);

                $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('A1:B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '90EE90',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->setTitle('Data Coa Yang Sudah Ada');
            }
        ];
    }

    private function calculateLevel($kodeAkun)
    {
        if (strpos($kodeAkun, '-') !== false) {
            return 5;
        }
        return strlen($kodeAkun);
    }
}
