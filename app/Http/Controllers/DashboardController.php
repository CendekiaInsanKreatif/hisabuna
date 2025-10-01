<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Jurnal;
use Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama
     */
    public function index()
    {
        $user = auth()->user();

        // Validate active user
        if (!$this->isUserActive($user)) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->with('message', 'Akun Anda tidak aktif')
                ->with('color', 'red');
        }

        // Get subscription status
        $masaAktif = $user->is_active ? 'Aktif' : 'Tidak Aktif';

        // Handle expiration date
        try {
            $expiredDateRaw = $this->getExpiredDate($user);
            $expirationDate = $expiredDateRaw ? Carbon::parse($expiredDateRaw) : null;

            $daysUntilExpiration = $expirationDate ? now()->diffInDays($expirationDate, false) : null;
            $formattedExpiredDate = $expirationDate ? $expirationDate->translatedFormat('d F Y') : '-';

            // Show modal if expiration is within 30 days
            session()->now('show_expiration_modal',
                $daysUntilExpiration !== null && $daysUntilExpiration <= 30 && $daysUntilExpiration > 0);

        } catch (\Exception $e) {
            \Log::error("Error processing expiration date for user {$user->id}: " . $e->getMessage());
            $daysUntilExpiration = null;
            $formattedExpiredDate = '-';
        }

        // Get financial data
        $currentYear = Carbon::now()->year;
        $monthlyData = $this->getMonthlyRevenueAndExpense($user->id, $currentYear);
        $totalJurnal = $this->getTotalJurnal($user->id);
        $totalJurnalComparison = $this->getTotalJurnalComparison($user->id);
        $recentActivities = $this->getRecentActivities($user->id);
        $months = $this->getMonthNames();

        // Get additional dashboard data
        $dashboardStats = $this->getDashboardStats($user->id);
        $cashFlow = $this->getCashFlowData($user->id);
        $topExpenses = $this->getTopExpenses($user->id);

        return view('dashboard.index', array_merge($monthlyData, [
            'user' => $user,
            'totalJurnal' => $totalJurnal,
            'recentActivities' => $recentActivities,
            'months' => $months,
            'masaAktif' => $masaAktif,
            'formattedExpiredDate' => $formattedExpiredDate,
            'totalJurnalComparison' => $totalJurnalComparison,
            'daysUntilExpiration' => $daysUntilExpiration,
            'year' => $currentYear,
            'dashboardStats' => $dashboardStats,
            'cashFlow' => $cashFlow,
            'topExpenses' => $topExpenses
        ]));
    }

    /**
     * Cek apakah user aktif
     */
    protected function isUserActive($user)
    {
        return $user && $user->is_active != 0;
    }

    /**
     * Ambil data pendapatan dan pengeluaran bulanan user di tahun tertentu
     */
    protected function getMonthlyRevenueAndExpense($userId, $year)
    {
        $revenues = [];
        $expenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $revenues[] = $this->getMonthlyRevenue($userId, $year, $month);
            $expenses[] = $this->getMonthlyExpense($userId, $year, $month);
        }

        return compact('revenues', 'expenses');
    }

    /**
     * Ambil total pendapatan bulan tertentu
     */
    protected function getMonthlyRevenue($userId, $year, $month)
    {
        return DB::table('jurnal_details')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->where('jurnal_headers.created_by', $userId)
            ->where('jurnal_headers.periode', $year)
            ->whereNull('jurnal_headers.is_deleted')
            ->whereMonth('jurnal_headers.created_at', $month)
            ->whereYear('jurnal_headers.created_at', $year)
            ->where('jurnal_details.coa_akun', 'like', '4%') // Revenue accounts
            ->sum(DB::raw('COALESCE(jurnal_details.credit, 0)'));
    }

    /**
     * Ambil total pengeluaran bulan tertentu
     */
    protected function getMonthlyExpense($userId, $year, $month)
    {
        return DB::table('jurnal_details')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->where('jurnal_headers.created_by', $userId)
            ->where('jurnal_headers.periode', $year)
            ->whereNull('jurnal_headers.is_deleted')
            ->whereMonth('jurnal_headers.created_at', $month)
            ->whereYear('jurnal_headers.created_at', $year)
            ->where('jurnal_details.coa_akun', 'like', '5%') // Expense accounts
            ->sum(DB::raw('COALESCE(jurnal_details.debit, 0)'));
    }

    /**
     * Ambil total jumlah jurnal user
     */
    protected function getTotalJurnal($userId)
    {
        return Jurnal::where('created_by', $userId)
            ->whereNull('is_deleted')
            ->count();
    }

    private function getTotalJurnalComparison($userId)
    {
        $currentPeriode = auth()->user()->periode;
        $lastPeriode = $currentPeriode - 1;

        // Count jurnal headers for current periode
        $thisYearCount = DB::table('jurnal_headers')
            ->where('created_by', $userId)
            ->where('periode', $currentPeriode)
            ->whereNull('is_deleted')
            ->count();

        // Count jurnal headers for previous periode
        $lastYearCount = DB::table('jurnal_headers')
            ->where('created_by', $userId)
            ->where('periode', $lastPeriode)
            ->whereNull('is_deleted')
            ->count();

        // Calculate growth percentage
        $growth = $lastYearCount > 0 ? (($thisYearCount - $lastYearCount) / $lastYearCount) * 100 : 0;

        return [
            'thisYear' => $thisYearCount,
            'lastYear' => $lastYearCount,
            'growth' => round($growth, 1)
        ];
    }

    /**
     * Ambil aktivitas terbaru user dari database
     */
    protected function getRecentActivities($userId)
    {
        $userPeriode = auth()->user()->periode;

        return DB::table('jurnal_headers')
            ->where('created_by', $userId)
            ->where('periode', $userPeriode)
            ->whereNull('is_deleted')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['jenis', 'keterangan', 'subtotal', 'created_at', 'no_urut_transaksi'])
            ->map(function ($jurnal) {
                return [
                    'activity' => "Jurnal {$jurnal->jenis} #{$jurnal->no_urut_transaksi}",
                    'description' => $jurnal->keterangan ?: 'Tidak ada keterangan',
                    'amount' => floatval($jurnal->subtotal ?? 0),
                    'time' => Carbon::parse($jurnal->created_at)->diffForHumans(),
                    'date' => Carbon::parse($jurnal->created_at)->format('d M Y')
                ];
            });
    }

    /**
     * Nama-nama bulan untuk tampilan
     */
    protected function getMonthNames()
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    }

    protected function getExpiredDate($user)
    {
        if ($user->profile === 'trial') {
            return $user->trial_ends_at ?? null;
        } elseif ($user->is_subscribed) {
            return $user->subscribed_until ?? null;
        }
        return null;
    }

    /**
     * Get dashboard statistics
     */
    protected function getDashboardStats($userId)
    {
        $userPeriode = auth()->user()->periode;

        // Total COA - filter by user and periode
        $totalCoa = DB::table('coas')
            ->where('created_by', $userId)
            ->whereNull('is_deleted')
            ->where('periode', $userPeriode)
            ->count();

        // Total Saldo Aktual - hitung berdasarkan saldo berjalan COA
        $totalSaldo = DB::table('coas')
            ->where('created_by', $userId)
            ->whereNull('is_deleted')
            ->where('periode', $userPeriode)
            ->sum(DB::raw('COALESCE(saldo_berjalan_debit, 0) - COALESCE(saldo_berjalan_credit, 0)'));

        // Total Transaksi Bulan Ini - berdasarkan periode user
        $totalTransaksiMonth = DB::table('jurnal_headers')
            ->where('created_by', $userId)
            ->whereNull('is_deleted')
            ->where('periode', $userPeriode)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', $userPeriode)
            ->count();

        // Rata-rata Transaksi per Hari (30 hari terakhir dalam periode)
        $startDate = Carbon::create($userPeriode, now()->month, 1)->startOfMonth();
        $endDate = min(now(), Carbon::create($userPeriode, 12, 31)->endOfYear());
        $daysInPeriod = max(1, $startDate->diffInDays($endDate));

        $totalTransaksiInPeriod = DB::table('jurnal_headers')
            ->where('created_by', $userId)
            ->whereNull('is_deleted')
            ->where('periode', $userPeriode)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgTransaksi = $totalTransaksiInPeriod / $daysInPeriod;

        // Statistik tambahan
        $totalPendapatan = $this->getTotalRevenueForPeriod($userId, $userPeriode);
        $totalPengeluaran = $this->getTotalExpenseForPeriod($userId, $userPeriode);

        return [
            'totalCoa' => $totalCoa,
            'totalSaldo' => $totalSaldo,
            'totalTransaksiMonth' => $totalTransaksiMonth,
                        'avgTransaksi' => round($avgTransaksi, 1),
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'netIncome' => $totalPendapatan - $totalPengeluaran
        ];
    }

    /**
     * Get total revenue for the entire periode
     */
    protected function getTotalRevenueForPeriod($userId, $periode)
    {
        return DB::table('jurnal_details')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->where('jurnal_headers.created_by', $userId)
            ->where('jurnal_headers.periode', $periode)
            ->whereNull('jurnal_headers.is_deleted')
            ->where('jurnal_details.coa_akun', 'like', '4%') // Revenue accounts
            ->sum(DB::raw('COALESCE(jurnal_details.credit, 0)'));
    }

    /**
     * Get total expense for the entire periode
     */
    protected function getTotalExpenseForPeriod($userId, $periode)
    {
        return DB::table('jurnal_details')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->where('jurnal_headers.created_by', $userId)
            ->where('jurnal_headers.periode', $periode)
            ->whereNull('jurnal_headers.is_deleted')
            ->where('jurnal_details.coa_akun', 'like', '5%') // Expense accounts
            ->sum(DB::raw('COALESCE(jurnal_details.debit, 0)'));
    }

    /**
     * Get cash flow data (6 months in current periode)
     */
    protected function getCashFlowData($userId)
    {
        $userPeriode = auth()->user()->periode;
        $cashFlow = [];

        // Get last 6 months within the user's periode
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($userPeriode, 12, 1)->subMonths($i);

            // Skip if month is in the future
            if ($date->isFuture()) {
                continue;
            }

            $month = $date->month;
            $year = $userPeriode; // Always use user's periode year

            $income = $this->getMonthlyRevenue($userId, $year, $month);
            $expense = $this->getMonthlyExpense($userId, $year, $month);

            $cashFlow[] = [
                'month' => $date->format('M Y'),
                'income' => floatval($income),
                'expense' => floatval($expense),
                'net' => floatval($income - $expense)
            ];
        }

        return $cashFlow;
    }

    /**
     * Get top expenses by category for current month in user's periode
     */
    protected function getTopExpenses($userId)
    {
        $userPeriode = auth()->user()->periode;

        return DB::table('jurnal_details')
            ->join('jurnal_headers', 'jurnal_details.jurnal_id', '=', 'jurnal_headers.id')
            ->join('coas', function($join) use ($userId, $userPeriode) {
                $join->on('jurnal_details.coa_akun', '=', 'coas.nomor_akun')
                     ->where('coas.created_by', $userId)
                     ->where('coas.periode', $userPeriode)
                     ->whereNull('coas.is_deleted');
            })
            ->where('jurnal_headers.created_by', $userId)
            ->where('jurnal_headers.periode', $userPeriode)
            ->whereNull('jurnal_headers.is_deleted')
            ->where('jurnal_details.coa_akun', 'like', '5%') // Expense accounts
            ->whereMonth('jurnal_headers.created_at', now()->month)
            ->whereYear('jurnal_headers.created_at', $userPeriode)
            ->groupBy('coas.nama_akun', 'jurnal_details.coa_akun')
            ->select(
                'coas.nama_akun as category',
                'jurnal_details.coa_akun as account_code',
                DB::raw('SUM(COALESCE(jurnal_details.debit, 0)) as total_amount')
            )
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();
    }
}
