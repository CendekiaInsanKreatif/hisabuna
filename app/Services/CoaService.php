<?php

namespace App\Services;

use App\Models\Coa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoaService
{
    /**
     * Determine account level based on account number length
     */
    public function determineLevel(string $accountNumber): int
    {
        $cleanAccountNumber = str_replace('-', '', $accountNumber);
        $digitCount = strlen($cleanAccountNumber);

        return match ($digitCount) {
            1 => 1,
            2 => 2,
            3 => 3,
            5 => 4,
            8 => 5,
            default => throw new \InvalidArgumentException('Format nomor akun tidak valid')
        };
    }

    /**
     * Determine parent ID based on level
     */
    public function determineParentId(string $accountNumber, int $level): ?string
    {
        $cleanAccountNumber = str_replace('-', '', $accountNumber);

        return match ($level) {
            2 => substr($cleanAccountNumber, 0, 1),
            3 => substr($cleanAccountNumber, 0, 2),
            4 => substr($cleanAccountNumber, 0, 3),
            5 => substr($cleanAccountNumber, 0, 5),
            default => null
        };
    }

    /**
     * Determine account group and normal balance based on first digit
     */
    public function determineAccountProperties(string $accountNumber): array
    {
        $firstDigit = substr(str_replace('-', '', $accountNumber), 0, 1);

        return match ($firstDigit) {
            '1' => ['saldo_normal' => 'debit', 'golongan' => 'Aset'],
            '2' => ['saldo_normal' => 'credit', 'golongan' => 'Liabilitas'],
            '3' => ['saldo_normal' => 'credit', 'golongan' => 'Ekuitas'],
            '4' => ['saldo_normal' => 'credit', 'golongan' => 'Pendapatan'],
            '5' => ['saldo_normal' => 'debit', 'golongan' => 'Beban'],
            '6' => ['saldo_normal' => 'debit', 'golongan' => 'Beban Umum'],
            '7' => ['saldo_normal' => 'credit', 'golongan' => 'Pendapatan Lainnya'],
            '8' => ['saldo_normal' => 'debit', 'golongan' => 'Beban Lainnya'],
            default => ['saldo_normal' => 'credit', 'golongan' => 'Beban Umum']
        };
    }

    /**
     * Validate and find parent account
     */
    public function findParentAccount(string $parentId, int $parentLevel): ?Coa
    {
        return Coa::where('nomor_akun', $parentId)
            ->where('level', $parentLevel)
            ->where('created_by', Auth::id())
            ->first();
    }

    /**
     * Format account number for display
     */
    public function formatAccountNumber(string $accountNumber, int $level): string
    {
        return match ($level) {
            4 => substr($accountNumber, 0, -2) . '-' . substr($accountNumber, -2),
            5 => substr($accountNumber, 0, 3) . '-' . substr($accountNumber, 3, 2) . '-' . substr($accountNumber, 5, 3),
            default => $accountNumber
        };
    }

    /**
     * Get filtered COA data with pagination
     */
    public function getFilteredCoa(array $filters): array
    {
        $userId = Auth::id();
        $page = (int) ($filters['page'] ?? 1);
        $perPage = 7;
        $offset = ($page - 1) * $perPage;

        $query = Coa::select([
            'id',
            'nama_akun',
            'level',
            'saldo_normal',
            DB::raw("
                CASE
                    WHEN level = 4 THEN
                        CONCAT(SUBSTRING(nomor_akun, 1, LENGTH(nomor_akun) - 2), '-', SUBSTRING(nomor_akun, LENGTH(nomor_akun) - 1))
                    WHEN level = 5 THEN
                        CONCAT(SUBSTRING(nomor_akun, 1, 3), '-',
                            SUBSTRING(nomor_akun, 4, 2), '-',
                            SUBSTRING(nomor_akun, 6, 3))
                    ELSE
                        nomor_akun
                END AS nomor_akun
            ")
        ])
        ->whereNull('is_deleted')
        ->where('created_by', $userId);

        // Count query for total records
        $countQuery = Coa::whereNull('is_deleted')
            ->where('created_by', $userId);

        // Apply filters based on what's provided
        if (!empty($filters['level']) && !empty($filters['search'])) {
            // Level filter takes precedence when both are provided
            $query->where('level', $filters['level']);
            $countQuery->where('level', $filters['level']);
        } elseif (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
            $countQuery->where('level', $filters['level']);
        } elseif (!empty($filters['search'])) {
            $query->where('nama_akun', 'LIKE', '%' . $filters['search'] . '%');
            $countQuery->where('nama_akun', 'LIKE', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['kepala'])) {
            $query->where('nomor_akun', 'LIKE', $filters['kepala'] . '%');
            $countQuery->where('nomor_akun', 'LIKE', $filters['kepala'] . '%');
        }

        $total = $countQuery->count();
        $data = $query->offset($offset)->limit($perPage)->get();

        return [
            'data' => $data,
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    /**
     * Prepare COA data for creation
     */
    public function prepareCOAData(array $input): array
    {
        $cleanAccountNumber = str_replace('-', '', $input['nomor_akun']);
        $level = $this->determineLevel($input['nomor_akun']);
        $parentId = $this->determineParentId($input['nomor_akun'], $level);
        $properties = $this->determineAccountProperties($input['nomor_akun']);

        $parentCoa = null;
        if ($level > 1 && $parentId) {
            $parentLevel = $level - 1;
            $parentCoa = $this->findParentAccount($parentId, $parentLevel);

            if (!$parentCoa) {
                // dd("FAFA");
                throw new \Exception("Akun Level {$parentLevel} tidak ditemukan untuk parent dengan nomor akun {$parentId}");
            }
        }

        $periode = auth()->user()->periode;

        return [
            'parent_id' => $parentCoa?->id,
            'subchild' => ($parentCoa?->subchild ?? 0) + 1,
            'nomor_akun' => $cleanAccountNumber,
            'nama_akun' => $input['nama_akun'],
            'level' => $level,
            'saldo_normal' => $properties['saldo_normal'],
            'golongan' => $properties['golongan'],
            'arus_kas' => 'aktifitas_operasional',
            'saldo_awal_debit' => 0,
            'saldo_awal_credit' => 0,
            'saldo_berjalan_debit' => 0,
            'saldo_berjalan_credit' => 0,
            'created_at' => $periode . "-" . date('m-d'),
            'tgl_dibuat' => now(),
            'created_by' => Auth::id(),
            'periode' => $periode
        ];
    }
}
