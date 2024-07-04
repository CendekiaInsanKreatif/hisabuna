<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;
use App\Models\Coa;

class CoaExport implements FromCollection, WithHeadings, WithColumnFormatting, WithEvents
{
    /**
     * Mengembalikan koleksi kosong karena kita ingin barisnya kosong
     * kecuali dropdown di A2 hingga A1000
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([]);
    }

    /**
     * Mendefinisikan header untuk kolom
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'no akun',
            'debit',
            'kredit',
            'keterangan'
        ];
    }

    /**
     * Mendefinisikan format kolom (tidak ada format khusus)
     *
     * @return array
     */
    public function columnFormats(): array
    {
        return [];
    }

    /**
     * Mendaftarkan event untuk lembar setelah di-generate
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(50);
                $sheet->getRowDimension(1)->setRowHeight(20);

                $sheet->getStyle('A1:D1')->applyFromArray([
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

                $coaSheet = $event->sheet->getParent()->createSheet();
                $coaSheet->setTitle('DataCoa');
                $coaData = $this->getCoa();

                $row = 1;
                foreach ($coaData as $no_akun => $nama_akun) {
                    $coaSheet->setCellValue("A{$row}", "$no_akun - $nama_akun");
                    $row++;
                }

                $sheet->getParent()->addNamedRange(
                    new NamedRange('COA_LIST', $coaSheet, 'A1:A' . ($row - 1))
                );

                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1('=COA_LIST');
                $validation->setFormula2('100');

                for ($row = 2; $row <= 1000; $row++) {
                    $sheet->getCell("A$row")->setDataValidation(clone $validation);
                }
            }
        ];
    }

    /**
     * Mendapatkan semua nomor dan nama COA sebagai array
     *
     * @return array
     */
    private function getCoa()
    {
        $coas = Coa::whereNull('is_deleted')
                    ->where('created_by', Auth::user()->id)
                    ->get(['nomor_akun', 'nama_akun']);
        return $coas->pluck('nama_akun', 'nomor_akun')->toArray();
    }
}
