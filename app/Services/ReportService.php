<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\JurnalDetail;

class ReportService
{
    /**
     * Get journal data for a specific period
     */
    public function getJurnalByPeriod($startDate, $endDate, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return Jurnal::with('details')
            ->where('created_by', $userId)
            ->whereNull('is_deleted')
            ->whereBetween('jurnal_tgl', [$startDate, $endDate])
            ->orderBy('jurnal_tgl')
            ->get();
    }

    /**
     * Get COA data for user
     */
    public function getCoaByUser($userId = null, $level = null)
    {
        $userId = $userId ?? Auth::id();

        $query = Coa::whereNull('is_deleted')
            ->where('created_by', $userId)
            ->orderBy('nomor_akun');

        if ($level) {
            $query->where('level', $level);
        }

        return $query->get();
    }

    /**
     * Calculate totals for P&L categories
     */
    public function calculateProfitLossTotals($journals, $coa)
    {
        $totalPendapatan = 0;
        $totalHPP = 0;
        $totalBeban = 0;
        $totalModal = 0;
        $namaAkun = '';

        foreach ($journals as $item) {
            foreach ($item->details as $detail) {
                $nomorAkun = substr($detail->coa_akun, 0, 1);

                switch ($nomorAkun) {
                    case '4': // Pendapatan
                        $totalPendapatan += $detail->credit - $detail->debit;
                        break;
                    case '5': // HPP
                        $totalHPP += $detail->debit - $detail->credit;
                        break;
                    case '6': // Beban
                        $totalBeban += $detail->debit - $detail->credit;
                        break;
                    case '3': // Modal
                        $noKun = substr($detail->coa_akun, 0, 4);
                        $coaRecord = $coa->where('nomor_akun', $noKun)->first();
                        if ($coaRecord) {
                            $namaAkun = $coaRecord->nama_akun;
                        }
                        $totalModal += $detail->debit + $detail->credit;
                        break;
                }
            }
        }

        return [
            'pendapatan' => $totalPendapatan,
            'hpp' => $totalHPP,
            'beban' => $totalBeban,
            'modal' => $totalModal,
            'namaAkun' => $namaAkun
        ];
    }

    /**
     * Generate cash flow data
     */
    public function generateCashFlowData($journals, $coas, $startDate, $endDate)
    {
        $data = [
            'aktifitas_operasional' => ['Jumlah' => 0, 'Detail' => []],
            'aktifitas_pendanaan' => ['Jumlah' => 0, 'Detail' => []],
            'aktifitas_investasi' => ['Jumlah' => 0, 'Detail' => []],
        ];

        foreach ($journals as $entry) {
            foreach ($entry->details as $detail) {
                $parent = $coas->get(substr($detail->coa_akun, 0, 1));
                $child = $coas->get($detail->coa_akun);
                $aruskas = $coas->get(substr($detail->coa_akun, 0, 4));

                if ($parent && $child && $aruskas) {
                    $nilai = ($parent->saldo_normal == 'db' || $parent->saldo_normal == 'debit')
                        ? $child->saldo_awal_debit + $detail->debit - $detail->credit
                        : $child->saldo_awal_credit + $detail->credit - $detail->debit;

                    $kategori = $this->mapToActivityCategory($aruskas->arus_kas);

                    if ($kategori && isset($data[$kategori])) {
                        $data[$kategori]['Jumlah'] += $nilai;
                        if (isset($child->nama_akun)) {
                            $data[$kategori]['Detail'][$child->nama_akun] =
                                ($data[$kategori]['Detail'][$child->nama_akun] ?? 0) + $nilai;
                        }
                    }
                }
            }
        }

        // Remove empty categories
        return array_filter($data, function($value) {
            return $value['Jumlah'] != 0;
        });
    }

    /**
     * Map arus kas to activity category
     */
    private function mapToActivityCategory($arusKas)
    {
        $mapping = [
            'operasional' => 'aktifitas_operasional',
            'pendanaan' => 'aktifitas_pendanaan',
            'investasi' => 'aktifitas_investasi',
        ];

        return $mapping[strtolower($arusKas)] ?? null;
    }

    /**
     * Generate balance sheet data
     */
    public function generateBalanceSheetData($tanggal, $labaRugi = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $year = substr($tanggal, 0, 4);

        $jurnal = JurnalDetail::where('created_by', $userId)
            ->where(function($query) {
                $query->where('coa_akun', 'like', '1%')
                    ->orWhere('coa_akun', 'like', '2%')
                    ->orWhere('coa_akun', 'like', '3%');
            })
            ->where('tanggal_bukti', '<=', $tanggal)
            ->orderBy('coa_akun', 'asc')
            ->get()
            ->keyBy('coa_akun');

        if ($jurnal->isEmpty()) {
            return [];
        }

        $coa = Coa::whereNull('is_deleted')
            ->where(function($query) {
                $query->where('nomor_akun', 'like', '1%')
                    ->orWhere('nomor_akun', 'like', '2%')
                    ->orWhere('nomor_akun', 'like', '3%');
            })
            ->where('created_by', $userId)
            ->whereYear('created_at', $year)
            ->orderBy('nomor_akun', 'asc')
            ->get()
            ->keyBy('nomor_akun');

        return $this->processBalanceSheetStructure($jurnal, $coa, $year, $labaRugi);
    }

    /**
     * Process balance sheet structure
     */
    private function processBalanceSheetStructure($jurnal, $coa, $year, $labaRugi)
    {
        $data = [];

        foreach ($jurnal as $row) {
            $nomorAkun = $row->coa_akun;

            // Get COA hierarchy
            $parent = $coa->get(substr($nomorAkun, 0, 1));
            $child = $coa->get(substr($nomorAkun, 0, 2));
            $subChild = $coa->get(substr($nomorAkun, 0, 3));
            $detail = $coa->get($nomorAkun);

            if (!$parent || !$child || !$subChild || !$detail) {
                continue;
            }

            // Calculate balance
            $balance = $this->calculateAccountBalance($row, $detail);

            if ($balance != 0) {
                $data[$year][$parent->golongan][$child->nama_akun][$subChild->nama_akun] = $balance;
            }
        }

        // Add equity adjustments
        if ($labaRugi !== null) {
            $data = $this->addEquityAdjustments($data, $year, $labaRugi);
        }

        return $data;
    }

    /**
     * Calculate account balance based on normal balance
     */
    private function calculateAccountBalance($journalRow, $coaDetail)
    {
        $saldoNormal = strtolower($coaDetail->saldo_normal);
        $saldoAwal = ($saldoNormal == 'db' || $saldoNormal == 'debit')
            ? $coaDetail->saldo_awal_debit
            : $coaDetail->saldo_awal_credit;

        if ($saldoNormal == 'db' || $saldoNormal == 'debit') {
            return $saldoAwal + $journalRow->debit - $journalRow->credit;
        } else {
            return $saldoAwal + $journalRow->credit - $journalRow->debit;
        }
    }

    /**
     * Add equity adjustments to balance sheet
     */
    private function addEquityAdjustments($data, $year, $labaRugi)
    {
        // Combine liabilities and equity
        if (isset($data[$year][2]) && isset($data[$year][3])) {
            $data[$year]['Liabilitas dan Ekuitas'] = array_merge(
                $data[$year][2] ?? [],
                $data[$year][3] ?? []
            );

            // Add current year earnings
            $equityKeys = array_keys($data[$year][3] ?? []);
            if (!empty($equityKeys)) {
                $data[$year]['Liabilitas dan Ekuitas'][$equityKeys[0]]['Saldo Tahun Berjalan'] = $labaRugi;
            }

            // Remove original liability and equity sections
            unset($data[$year][2], $data[$year][3]);
        }

        return $data;
    }

    /**
     * Format currency for display
     */
    public function formatCurrency($amount, $decimals = 0)
    {
        return number_format($amount, $decimals, ',', '.');
    }

    /**
     * Validate date range
     */
    public function validateDateRange($startDate, $endDate)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            if ($start->gt($end)) {
                throw new \Exception('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Format tanggal tidak valid: ' . $e->getMessage());
        }
    }
}
