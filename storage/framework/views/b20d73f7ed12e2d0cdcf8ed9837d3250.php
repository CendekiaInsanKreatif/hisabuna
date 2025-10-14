<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="coaTable()">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-emerald">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-secondary-800">Saldo Awal</h1>
                <p class="text-secondary-600 font-medium">Pengaturan saldo awal untuk semua akun</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="panel overflow-hidden">
        <div class="panel-header">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-secondary-800">Daftar Chart of Accounts</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-secondary-600 bg-secondary-100 px-3 py-1 rounded-lg">
                        Total Akun: <span class="font-semibold" x-text="allData.length"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <form action="<?php echo e(route('saldo-awal.update')); ?>" method="post" @submit.prevent="validateAndSubmit">
                <?php echo csrf_field(); ?>
                <?php echo method_field('put'); ?>

                <!-- Filter and Action Bar -->
                <div class="bg-gradient-to-r from-secondary-50 to-white p-4 rounded-2xl border border-secondary-200/50 shadow-soft mb-6">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <!-- Left Section: Search and Filter -->
                        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                            <!-- Search Input -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                    x-model="searchQuery"
                                    @input="updateTotals()"
                                    class="pl-10 pr-4 py-3 w-64 text-sm border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300"
                                    placeholder="Cari nomor atau nama akun...">
                            </div>

                            <!-- Filter Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex items-center justify-between w-48 px-4 py-3 text-sm border border-secondary-300 rounded-xl bg-white hover:bg-secondary-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                        </svg>
                                        <span x-text="filter === 'all' ? 'Semua Akun' : filter === 'neraca' ? 'Akun Neraca' : 'Akun Laba Rugi'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-secondary-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-10 mt-2 w-48 bg-white rounded-xl shadow-lg border border-secondary-200 overflow-hidden" style="display: none;">
                                    <div class="py-1">
                                        <button type="button" @click="filterCategory('all'); open = false"
                                            :class="filter === 'all' ? 'bg-primary-50 text-primary-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full" :class="filter === 'all' ? 'bg-primary-500' : 'bg-transparent'"></div>
                                            Semua Akun
                                        </button>
                                        <button type="button" @click="filterCategory('neraca'); open = false"
                                            :class="filter === 'neraca' ? 'bg-info-50 text-info-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full" :class="filter === 'neraca' ? 'bg-info-500' : 'bg-transparent'"></div>
                                            Akun Neraca
                                        </button>
                                        <button type="button" @click="filterCategory('labarugi'); open = false"
                                            :class="filter === 'labarugi' ? 'bg-warning-50 text-warning-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full" :class="filter === 'labarugi' ? 'bg-warning-500' : 'bg-transparent'"></div>
                                            Akun Laba Rugi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button @click="reset" type="button" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-secondary-700 bg-white border border-secondary-300 rounded-xl hover:bg-secondary-50 focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0V9a8 8 0 1115.356 2m-15.356 0H4"/>
                                </svg>
                                Reset
                            </button>
                            <button type="submit" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-white bg-primary-600 border border-primary-600 rounded-xl hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Summary Cards - Horizontal Layout -->
                <div class="bg-gradient-to-r from-secondary-50 to-white p-6 rounded-2xl border border-secondary-200/50 shadow-soft mb-6">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                        <!-- Saldo Debit -->
                        <div class="flex items-center gap-4 bg-gradient-to-r from-success-50 to-success-100 p-4 rounded-xl border border-success-200/50 shadow-soft min-w-[200px]">
                            <div class="w-12 h-12 bg-success-500 rounded-xl flex items-center justify-center shadow-soft">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-success-700 mb-1">Total Saldo Debit</p>
                                <p class="text-xl font-bold text-success-800" x-text="totalSaldoAwalDebit">Rp 0</p>
                            </div>
                        </div>

                        <!-- Saldo Kredit -->
                        <div class="flex items-center gap-4 bg-gradient-to-r from-info-50 to-info-100 p-4 rounded-xl border border-info-200/50 shadow-soft min-w-[200px]">
                            <div class="w-12 h-12 bg-info-500 rounded-xl flex items-center justify-center shadow-soft">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-info-700 mb-1">Total Saldo Kredit</p>
                                <p class="text-xl font-bold text-info-800" x-text="totalSaldoAwalKredit">Rp 0</p>
                            </div>
                        </div>

                        <!-- Selisih -->
                        <div class="flex items-center gap-4 bg-gradient-to-r from-warning-50 to-warning-100 p-4 rounded-xl border border-warning-200/50 shadow-soft min-w-[200px]">
                            <div class="w-12 h-12 bg-warning-500 rounded-xl flex items-center justify-center shadow-soft">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-warning-700 mb-1">Selisih Saldo</p>
                                <p class="text-xl font-bold text-warning-800" x-text="selisihSaldoAwal">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="overflow-hidden rounded-2xl border border-secondary-200/50 shadow-soft">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full bg-white" id="coaTable">
                            <thead>
                                <tr class="border-b border-secondary-200">
                                    <th class="t-head text-left">
                                        <div class="flex items-center gap-2">
                                            Nomor Akun
                                            <span class="ml-auto">
                                                <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="t-head text-left">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                            Nama Akun
                                            <span class="ml-auto">
                                                <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="t-head text-right">
                                        <div class="flex items-center gap-2 justify-end">
                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                            </svg>
                                            Saldo Debit
                                            <span class="ml-2">
                                                <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="t-head text-right">
                                        <div class="flex items-center gap-2 justify-end">
                                            <svg class="w-4 h-4 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                            </svg>
                                            Saldo Kredit
                                            <span class="ml-2">
                                                <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="coaTableBody" class="divide-y divide-secondary-100">
                                <template x-for="coa in filteredData" :key="coa.id">
                                    <tr class="hover:bg-secondary-50 transition-colors duration-200 group">
                                        <input type="hidden" name="id[]" x-model="coa.id">
                                        <input type="hidden" name="nomor_akun[]" x-model="coa.nomor_akun">
                                        <input type="hidden" name="nama_akun[]" x-model="coa.nama_akun">

                                        <td class="px-4 py-4 text-sm">
                                            <span class="font-mono font-semibold text-secondary-800" x-text="formatNomorAkun(coa.nomor_akun)"></span>
                                        </td>

                                        <td class="px-4 py-4 text-sm">
                                            <div class="font-medium text-secondary-800" x-text="coa.nama_akun"></div>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-right">
                                            <input type="text"
                                                name="saldo_awal_debit[]"
                                                class="form-input text-right"
                                                :class="(coa.saldo_normal == 'credit' || coa.saldo_normal == 'kredit') ? 'bg-secondary-100 cursor-not-allowed' : 'focus:ring-success-500 focus:border-success-500'"
                                                x-model="coa.formatted_saldo_awal_debit"
                                                x-on:input="formatCurrencyInput($event, 'debit', coa)"
                                                :readonly="coa.saldo_normal == 'credit' || coa.saldo_normal == 'kredit'"
                                                placeholder="0">
                                        </td>

                                        <td class="px-4 py-4 text-sm text-right">
                                            <input type="text"
                                                name="saldo_awal_credit[]"
                                                class="form-input text-right"
                                                :class="(coa.saldo_normal == 'debit' || coa.saldo_normal == 'db') ? 'bg-secondary-100 cursor-not-allowed' : 'focus:ring-info-500 focus:border-info-500'"
                                                x-model="coa.formatted_saldo_awal_credit"
                                                x-on:input="formatCurrencyInput($event, 'credit', coa)"
                                                :readonly="coa.saldo_normal == 'debit' || coa.saldo_normal == 'db'"
                                                placeholder="0">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('coaTable', () => ({
                allData: [],
                changedData: {},
                totalSaldoAwalDebit: 0,
                totalSaldoAwalKredit: 0,
                selisihSaldoAwal: 0,
                filter: 'all',
                searchQuery: '',
                filterCategory(category) {
                    this.filter = category;
                    this.updateTotals();
                },
                get filteredData() {
                    let data = this.allData;
                    const filter = this.filter;
                    const searchQuery = this.searchQuery;

                    // Return early if no filters
                    if (filter === 'all' && !searchQuery) {
                        return data;
                    }

                    // Combined filter for better performance
                    return data.filter(coa => {
                        const firstChar = coa.nomor_akun.charAt(0);

                        // Category filter
                        if (filter === 'neraca' && !['1', '2', '3'].includes(firstChar)) {
                            return false;
                        }
                        if (filter === 'labarugi' && !['4', '5', '6'].includes(firstChar)) {
                            return false;
                        }

                        // Search filter
                        if (searchQuery) {
                            const query = searchQuery.toLowerCase();
                            const matchesNomor = coa.nomor_akun_lower.includes(query);
                            const matchesNama = coa.nama_akun_lower.includes(query);
                            return matchesNomor || matchesNama;
                        }

                        return true;
                    });
                },
                updateTotals() {
                    // Use requestAnimationFrame for smooth UI updates
                    requestAnimationFrame(() => {
                        const filteredData = this.filteredData;
                        let totalDebit = 0;
                        let totalKredit = 0;

                        // Single loop optimization
                        for (let i = 0; i < filteredData.length; i++) {
                            totalDebit += parseFloat(filteredData[i].saldo_awal_debit || 0);
                            totalKredit += parseFloat(filteredData[i].saldo_awal_credit || 0);
                        }

                        this.totalSaldoAwalDebit = 'Rp ' + this.formatCurrency(totalDebit);
                        this.totalSaldoAwalKredit = 'Rp ' + this.formatCurrency(totalKredit);
                        this.selisihSaldoAwal = 'Rp ' + this.formatCurrency(totalDebit - totalKredit);
                    });
                },
                validateAndSubmit() {
                    if (this.filter === 'neraca' && parseFloat(this.selisihSaldoAwal) !== 0) {
                        alert('Selisih untuk kategori Neraca tidak boleh berbeda antara Debit dan Kredit.');
                        return false;
                    } else {
                        if (this.filter === 'all') {
                            const neracaAccounts = this.allData.filter(coa => ['1', '2', '3'].includes(coa.nomor_akun.charAt(0)));
                            const totalDebit = neracaAccounts.reduce((acc, coa) => acc + parseFloat(coa.saldo_awal_debit || 0), 0);
                            const totalKredit = neracaAccounts.reduce((acc, coa) => acc + parseFloat(coa.saldo_awal_credit || 0), 0);
                            const selisihNeraca = totalDebit - totalKredit;

                            if (selisihNeraca !== 0) {
                                alert('Selisih untuk Akun Neraca tidak boleh berbeda antara Debit dan Kredit.');
                                return false;
                            }
                        }

                        this.$el.submit();
                    }
                },
                async fetchCoaData() {
                    // const overlay = document.getElementById('overlay');
                    // overlay.style.display = 'flex';

                    try {
                        const response = await fetch('/api/saldo-awal');
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (!Array.isArray(data)) {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format. Please check the data returned from the server.');
                            return;
                        }

                        // Pre-process data for performance
                        this.allData = data.map(coa => {
                            const debit = parseFloat(coa.saldo_awal_debit || 0);
                            const credit = parseFloat(coa.saldo_awal_credit || 0);

                            return {
                                ...coa,
                                saldo_awal_debit: debit,
                                saldo_awal_credit: credit,
                                formatted_saldo_awal_debit: this.formatCurrency(debit),
                                formatted_saldo_awal_credit: this.formatCurrency(credit),
                                // Pre-compute lowercase for search optimization
                                nomor_akun_lower: coa.nomor_akun.toString().toLowerCase(),
                                nama_akun_lower: coa.nama_akun.toLowerCase()
                            };
                        });

                        this.updateTotals();
                    } catch (error) {
                        console.error('Error fetching COA data:', error);
                        alert('Error fetching COA data. Please try again later.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },
                async reset() {
                    await this.fetchCoaData();
                    this.changedData = {};
                    this.searchQuery = '';
                    this.filter = 'all';
                },
                formatCurrency(value) {
                    // Fast path for zero or empty values
                    if (!value || value === 0) return '0';

                    // Parse value once
                    const numValue = typeof value === 'number' ? value :
                        parseFloat(value.toString().replace(/\./g, '').replace(/,/g, '.'));

                    if (isNaN(numValue)) return '0';

                    return numValue.toLocaleString('id-ID');
                },
                formatCurrencyInput(event, type, coa) {
                    const input = event.target;
                    let value = input.value.replace(/[^0-9,-]/g, '').replace(/\./g, '').replace(/,/g, '.');

                    // Parse and validate
                    let numValue = parseFloat(value);
                    if (isNaN(numValue) || numValue < 0) {
                        numValue = 0;
                    }

                    // Format for display
                    const formatted = this.formatCurrency(numValue);
                    input.value = formatted;

                    // Update model
                    if (type === 'debit') {
                        coa.saldo_awal_debit = numValue;
                        coa.formatted_saldo_awal_debit = formatted;
                    } else {
                        coa.saldo_awal_credit = numValue;
                        coa.formatted_saldo_awal_credit = formatted;
                    }

                    // Track changes
                    this.changedData[coa.id] = coa;

                    // Debounce total updates
                    if (this.updateTimeout) {
                        clearTimeout(this.updateTimeout);
                    }
                    this.updateTimeout = setTimeout(() => this.updateTotals(), 150);
                },
                formatNomorAkun(nomor_akun) {
                    let formatted = nomor_akun.toString().padEnd(8, '0');
                    if (formatted.length >= 3) {
                        formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
                    }
                    if (formatted.length >= 6) {
                        formatted = formatted.slice(0, 6) + '-' + formatted.slice(6);
                    }
                    return formatted;
                },
                init() {
                    this.fetchCoaData();
                }
            }));
        });
    </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/saldo-awal/index.blade.php ENDPATH**/ ?>