<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<!-- Modal Peringatan Masa Aktif -->
<?php if(session('show_expiration_modal') && $daysUntilExpiration !== null): ?>
    <div id="expirationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-warning-100 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-warning-600">Peringatan Masa Aktif</h3>
            </div>

            <p class="mb-4">
                Masa aktif langganan Anda akan berakhir dalam
                <strong><?php echo e(floor($daysUntilExpiration)); ?> hari</strong>
                (<?php echo e($formattedExpiredDate); ?>).
                Segera perpanjang untuk menghindari pemutusan layanan.
            </p>

            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('expirationModal').style.display='none'"
                        class="px-4 py-2 text-sm rounded-lg border border-secondary-300 hover:bg-secondary-50 text-secondary-700 transition-all duration-300">
                    Nanti Saja
                </button>
                <a href="<?php echo e(route('subscription.upgrade')); ?>"
                    class="px-4 py-2 text-sm rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white transition-all duration-300 shadow-emerald hover:shadow-emerald-lg">
                    Perpanjang Sekarang
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6 flex items-center gap-3 text-secondary-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-4h6v4m-6 4h6a2 2 0 002-2v-6h2a2 2 0 002-2V7a2 2 0 00-2-2h-2V3a2 2 0 00-2-2H9a2 2 0 00-2 2v2H5a2 2 0 00-2 2v4a2 2 0 002 2h2v6a2 2 0 002 2z" />
        </svg>
        Selamat datang, <?php echo e(auth()->user()->name); ?> (<?php echo e(auth()->user()->company_name ?? 'Nama PT Tidak Ada'); ?>)
    </h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Status Akun Card -->
        <div class="bg-gradient-to-br from-info-500 to-info-600 p-6 rounded-xl shadow-blue text-white hover:shadow-blue-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium bg-white/20 px-2 py-1 rounded-full uppercase tracking-wide"><?php echo e(auth()->user()->profile ?? 'Basic'); ?></span>
            </div>
            <h3 class="text-sm font-medium text-info-100 mb-1">Status Akun</h3>
            <p class="text-2xl font-bold"><?php echo e($masaAktif); ?></p>
            <p class="text-xs text-info-100 mt-2">Sampai: <?php echo e($formattedExpiredDate); ?></p>
        </div>

        <!-- Total Jurnal Card -->
        <div class="bg-gradient-to-br from-success-500 to-success-600 p-6 rounded-xl shadow-emerald text-white hover:shadow-emerald-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <?php
                    $growth = $totalJurnalComparison['lastYear'] > 0
                        ? (($totalJurnalComparison['thisYear'] - $totalJurnalComparison['lastYear']) / $totalJurnalComparison['lastYear']) * 100
                        : 0;
                ?>
                <?php if($growth > 0): ?>
                    <span class="text-xs font-medium bg-white/20 px-2 py-1 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        +<?php echo e(number_format($growth, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
            <h3 class="text-sm font-medium text-success-100 mb-1">Total Jurnal</h3>
            <p class="text-2xl font-bold"><?php echo e(number_format($totalJurnalComparison['thisYear'] ?? 0)); ?></p>
            <p class="text-xs text-success-100 mt-2">Periode <?php echo e(auth()->user()->periode); ?></p>
        </div>

        <!-- Total COA Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-xl shadow-purple text-white hover:shadow-purple-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H3m16 8H1"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-purple-100 mb-1">Total Akun COA</h3>
            <p class="text-2xl font-bold"><?php echo e(number_format($dashboardStats['totalCoa'] ?? 0)); ?></p>
            <p class="text-xs text-purple-100 mt-2">Chart of Accounts</p>
        </div>

        <!-- Transaksi Bulan Ini Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-orange text-white hover:shadow-orange-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-orange-100 mb-1">Transaksi Bulan Ini</h3>
            <p class="text-2xl font-bold"><?php echo e(number_format($dashboardStats['totalTransaksiMonth'] ?? 0)); ?></p>
            <p class="text-xs text-orange-100 mt-2"><?php echo e(now()->format('F Y')); ?></p>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Total Saldo Card -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-100 p-3 rounded-xl">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-secondary-800">Total Saldo</h3>
                </div>
                <?php
                    $saldo = $dashboardStats['totalSaldo'] ?? 0;
                    $saldoColor = $saldo >= 0 ? 'text-emerald-600' : 'text-red-600';
                ?>
                <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo e($saldo >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'); ?>">
                    <?php echo e($saldo >= 0 ? 'Positif' : 'Negatif'); ?>

                </span>
            </div>
            <p class="text-2xl font-bold <?php echo e($saldoColor); ?> mb-2">
                Rp <?php echo e(number_format(abs($saldo), 0, ',', '.')); ?>

            </p>
            <p class="text-sm text-secondary-500">Saldo berjalan periode <?php echo e(auth()->user()->periode); ?></p>
        </div>

        <!-- Rata-rata Transaksi Card -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-secondary-800">Rata-rata Transaksi</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-secondary-800 mb-2">
                <?php echo e($dashboardStats['avgTransaksi'] ?? 0); ?>

            </p>
            <p class="text-sm text-secondary-500">Per hari (30 hari terakhir)</p>
        </div>

        <!-- Total Pendapatan Card -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-secondary-800">Total Pendapatan</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600 mb-2">
                Rp <?php echo e(number_format($dashboardStats['totalPendapatan'] ?? 0, 0, ',', '.')); ?>

            </p>
            <p class="text-sm text-secondary-500">Periode <?php echo e(auth()->user()->periode); ?></p>
        </div>

        <!-- Total Pengeluaran Card -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 p-3 rounded-xl">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-secondary-800">Total Pengeluaran</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600 mb-2">
                Rp <?php echo e(number_format($dashboardStats['totalPengeluaran'] ?? 0, 0, ',', '.')); ?>

            </p>
            <p class="text-sm text-secondary-500">Periode <?php echo e(auth()->user()->periode); ?></p>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50 mb-8">
        <h3 class="text-lg font-semibold text-secondary-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Aksi Cepat
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="<?php echo e(route('jurnal.create')); ?>" class="flex items-center gap-3 p-4 rounded-lg bg-gradient-to-r from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 text-primary-700 transition-all duration-200 border border-primary-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="font-medium">Buat Jurnal Baru</span>
            </a>
            <a href="<?php echo e(route('jurnal.index')); ?>" class="flex items-center gap-3 p-4 rounded-lg bg-gradient-to-r from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 text-secondary-700 transition-all duration-200 border border-secondary-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium">Lihat Semua Jurnal</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg bg-gradient-to-r from-emerald-50 to-emerald-100 hover:from-emerald-100 hover:to-emerald-200 text-emerald-700 transition-all duration-200 border border-emerald-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="font-medium">Laporan Keuangan</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-4 rounded-lg bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 text-purple-700 transition-all duration-200 border border-purple-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-medium">Pengaturan COA</span>
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Profit Loss Chart -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-secondary-800">
                <svg class="h-6 w-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Laba Rugi Tahun <?php echo e($year); ?>

            </h2>
            <div class="relative h-64 w-full bg-secondary-50/30 rounded-lg p-4">
                <canvas id="profitLossChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-secondary-800">
                <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Cash Flow (6 Bulan Terakhir)
            </h2>
            <div class="relative h-64 w-full bg-secondary-50/30 rounded-lg p-4">
                <canvas id="cashFlowChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Expenses -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-secondary-800">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Top Pengeluaran Bulan Ini
            </h2>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $topExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-secondary-50 hover:bg-secondary-100 transition-colors duration-200">
                        <div class="flex-1">
                            <p class="font-medium text-secondary-800"><?php echo e($expense->category); ?></p>
                            <p class="text-sm text-secondary-500"><?php echo e($expense->account_code); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-red-600">Rp <?php echo e(number_format($expense->total_amount, 0, ',', '.')); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-secondary-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
                        </svg>
                        <p>Belum ada data pengeluaran bulan ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white p-6 rounded-xl shadow-soft hover:shadow-medium transition-all duration-300 border border-secondary-200/50">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-secondary-800">
                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aktivitas Terbaru
            </h2>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-secondary-50 hover:bg-secondary-100 transition-colors duration-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-secondary-800"><?php echo e($activity['activity']); ?></p>
                            <p class="text-sm text-secondary-500 truncate"><?php echo e($activity['description']); ?></p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-secondary-400"><?php echo e($activity['time']); ?></span>
                                <span class="text-sm font-medium text-primary-600">
                                    Rp <?php echo e(number_format($activity['amount'], 0, ',', '.')); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-secondary-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Belum ada aktivitas terbaru</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global Chart.js configuration to prevent infinite loops
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.resizeDelay = 200;

    // Store chart instances to prevent memory leaks
    let profitLossChartInstance = null;
    let cashFlowChartInstance = null;

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Data validation to prevent loops
            const monthsData = <?php echo json_encode($months ?? [], 15, 512) ?>;
            const revenuesData = <?php echo json_encode($revenues ?? [], 15, 512) ?>;
            const expensesData = <?php echo json_encode($expenses ?? [], 15, 512) ?>;
            const cashFlowData = <?php echo json_encode($cashFlow ?? [], 15, 512) ?>;

            // Validate data arrays to prevent infinite loops
            if (!Array.isArray(monthsData) || !Array.isArray(revenuesData) || !Array.isArray(expensesData)) {
                console.error('Chart data is not in correct format');
                return;
            }

            // Ensure data length consistency
            const maxLength = Math.max(monthsData.length, revenuesData.length, expensesData.length);
            if (maxLength === 0) {
                console.warn('No chart data available');
                return;
            }

            // Profit Loss Chart
            const profitLossCanvas = document.getElementById('profitLossChart');
            console.log('Profit Loss Canvas:', profitLossCanvas);
            console.log('Months data:', monthsData);
            console.log('Revenues data:', revenuesData);
            console.log('Expenses data:', expensesData);

            if (profitLossCanvas) {
                // Destroy existing chart instance if it exists
                if (profitLossChartInstance) {
                    profitLossChartInstance.destroy();
                }

                const ctx1 = profitLossCanvas.getContext('2d');
            profitLossChartInstance = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: monthsData,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: revenuesData.map(val => parseFloat(val) || 0),
                            backgroundColor: 'rgba(34,197,94,0.8)',
                            borderColor: 'rgba(34,197,94,1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                        {
                            label: 'Pengeluaran',
                            data: expensesData.map(val => parseFloat(val) || 0),
                            backgroundColor: 'rgba(239,68,68,0.8)',
                            borderColor: 'rgba(239,68,68,1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                maxTicksLimit: 10,
                                callback: function(value) {
                                    if (isNaN(value)) return '0';
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y || 0;
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
                });
            }

            // Cash Flow Chart
            const cashFlowCanvas = document.getElementById('cashFlowChart');
            console.log('Cash Flow Canvas:', cashFlowCanvas);
            console.log('Cash Flow data:', cashFlowData);

            if (cashFlowCanvas && Array.isArray(cashFlowData) && cashFlowData.length > 0) {
                // Destroy existing chart instance if it exists
                if (cashFlowChartInstance) {
                    cashFlowChartInstance.destroy();
                }

                const ctx2 = cashFlowCanvas.getContext('2d');
            cashFlowChartInstance = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: cashFlowData.map(item => item.month || ''),
                    datasets: [
                        {
                            label: 'Net Cash Flow',
                            data: cashFlowData.map(item => parseFloat(item.net) || 0),
                            borderColor: 'rgba(59,130,246,1)',
                            backgroundColor: 'rgba(59,130,246,0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(59,130,246,1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                        },
                        {
                            label: 'Pendapatan',
                            data: cashFlowData.map(item => parseFloat(item.income) || 0),
                            borderColor: 'rgba(34,197,94,1)',
                            backgroundColor: 'rgba(34,197,94,0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(34,197,94,1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        },
                        {
                            label: 'Pengeluaran',
                            data: cashFlowData.map(item => parseFloat(item.expense) || 0),
                            borderColor: 'rgba(239,68,68,1)',
                            backgroundColor: 'rgba(239,68,68,0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(239,68,68,1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                maxTicksLimit: 10,
                                callback: function(value) {
                                    if (isNaN(value)) return '0';
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y || 0;
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
                });
            } else {
                console.warn('Cash flow chart not created - no data or canvas not found');
            }

        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    });

    // Auto tampilkan modal jika masa aktif <= 30 hari
    <?php if(isset($daysUntilExpiration) && $daysUntilExpiration <= 30 && $daysUntilExpiration > 0): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('expirationModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    });
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/dashboard/index.blade.php ENDPATH**/ ?>