<?php

namespace App\Exports;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Coa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JurnalExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $jurnalType;
    protected $includeDetails;

    public function __construct($startDate = null, $endDate = null, $jurnalType = null, $includeDetails = true)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->jurnalType = $jurnalType;
        $this->includeDetails = $includeDetails;
    }

    /**
     * Mengambil koleksi data jurnal untuk export
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Jurnal::with(['details.coa'])
            ->where('created_by', Auth::user()->id)
            ->whereNull('is_deleted')
            ->orderBy('jurnal_tgl', 'asc')
            ->orderBy('no_urut_transaksi', 'asc');

        // Filter berdasarkan tanggal jika disediakan
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('jurnal_tgl', [
                Carbon::parse($this->startDate)->format('Y-m-d'),
                Carbon::parse($this->endDate)->format('Y-m-d')
            ]);
        }

        // Filter berdasarkan jenis jurnal jika disediakan
        if ($this->jurnalType) {
            $query->where('jenis', $this->jurnalType);
        }

        $jurnals = $query->get();

        // Jika include details true, return detail jurnal
        if ($this->includeDetails) {
            $result = collect();
            foreach ($jurnals as $jurnal) {
                foreach ($jurnal->details as $detail) {
                    $result->push([
                        'jurnal' => $jurnal,
                        'detail' => $detail
                    ]);
                }
            }
            return $result;
        }

        // Jika false, return header jurnal saja
        return $jurnals->map(function ($jurnal) {
            return ['jurnal' => $jurnal, 'detail' => null];
        });
    }

    /**
     * Mapping data untuk setiap baris
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $jurnal = $row['jurnal'];
        $detail = $row['detail'];

        if ($this->includeDetails && $detail) {
            return [
                $jurnal->no_urut_transaksi,
                $jurnal->no_transaksi ?? '',
                Carbon::parse($jurnal->jurnal_tgl)->format('d/m/Y'),
                $jurnal->jenis,
                $jurnal->keterangan,
                $detail->coa_akun,
                $detail->coa ? $detail->coa->nama_akun : '',
                Carbon::parse($detail->tanggal_bukti)->format('d/m/Y'),
                $detail->debit > 0 ? number_format($detail->debit, 0, ',', '.') : '',
                $detail->credit > 0 ? number_format($detail->credit, 0, ',', '.') : '',
                $detail->keterangan,
                $detail->lampiran ?? ''
            ];
        } else {
            // Export header saja
            return [
                $jurnal->no_urut_transaksi,
                $jurnal->no_transaksi ?? '',
                Carbon::parse($jurnal->jurnal_tgl)->format('d/m/Y'),
                $jurnal->jenis,
                $jurnal->keterangan,
                number_format($jurnal->subtotal ?? 0, 0, ',', '.'),
                Carbon::parse($jurnal->created_at)->format('d/m/Y H:i:s')
            ];
        }
    }

    /**
     * Header kolom untuk export
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->includeDetails) {
            return [
                'No. Urut Transaksi',
                'No. Transaksi',
                'Tanggal Jurnal',
                'Jenis',
                'Keterangan Header',
                'Kode Akun',
                'Nama Akun',
                'Tanggal Bukti',
                'Debit',
                'Kredit',
                'Keterangan Detail',
                'Lampiran'
            ];
        } else {
            return [
                'No. Urut Transaksi',
                'No. Transaksi',
                'Tanggal Jurnal',
                'Jenis',
                'Keterangan',
                'Subtotal',
                'Tanggal Dibuat'
            ];
        }
    }

    /**
     * Format kolom
     *
     * @return array
     */
    public function columnFormats(): array
    {
        if ($this->includeDetails) {
            return [
                'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                'I' => NumberFormat::FORMAT_TEXT,
                'J' => NumberFormat::FORMAT_TEXT,
            ];
        } else {
            return [
                'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                'F' => NumberFormat::FORMAT_TEXT,
                'G' => NumberFormat::FORMAT_DATE_DATETIME
            ];
        }
    }

    /**
     * Styling untuk worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:' . ($this->includeDetails ? 'L1' : 'G1'))->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style untuk data
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $this->includeDetails ? 'L' : 'G';
        
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Alignment untuk kolom angka
        if ($this->includeDetails) {
            $sheet->getStyle('I2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Set row height untuk header
        $sheet->getRowDimension('1')->setRowHeight(25);

        return [];
    }
}