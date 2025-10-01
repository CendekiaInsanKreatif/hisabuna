<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="jurnalTable" x-cloak>
    <?php
        $fields = [
            [
                'name' => 'no_urut_transaksi',
                'label' => 'No. Transaksi',
                'type' => 'text',
                'disabled' => true,
            ],
            [
                'name' => 'tanggal',
                'label' => 'Tanggal',
                'type' => 'date',
                'disabled' => true,
            ],
            [
                'name' => 'jenis',
                'label' => 'Jenis Jurnal',
                'type' => 'text',
                'disabled' => true,
            ],
            [
                'name' => 'keterangan',
                'label' => 'Keterangan',
                'type' => 'text',
                'disabled' => true,
            ],
        ];
    ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fields,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fields),'focusable' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-3 sm:gap-4 mb-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-blue flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-secondary-800">Jurnal</h1>
                <p class="text-sm sm:text-base text-secondary-600 font-medium">Kelola semua transaksi jurnal perusahaan</p>
            </div>
        </div>
    </div>
    <!-- Main Card -->
    <div class="panel overflow-hidden">
        <div class="panel-header">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <h2 class="text-lg sm:text-xl font-bold text-secondary-800">Daftar Jurnal</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs sm:text-sm text-secondary-600 bg-secondary-100 px-2 sm:px-3 py-1 rounded-lg">
                        Total: <span class="font-semibold">
                            <span x-show="!isLoading" x-text="totalItems || 0"></span>
                            <span x-show="isLoading" class="inline-block w-4 h-4 border border-secondary-400 border-t-transparent rounded-full animate-spin"></span>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <!-- Filter and Action Bar -->
            <div class="bg-gradient-to-r from-secondary-50 to-white p-6 rounded-2xl border border-secondary-200/50 shadow-soft mb-6">
                <div class="space-y-6">
                    <!-- Filter, Search, and Actions Row -->
                    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                        <!-- Filter Dropdown -->
                        <div class="relative" x-data="{ isOpen: false }">
                            <button @click="isOpen = !isOpen"
                                    class="inline-flex items-center justify-between gap-3 px-4 py-2.5 text-sm font-medium bg-white text-secondary-700 border border-secondary-300 rounded-xl shadow-soft hover:shadow-medium transition-all duration-200 min-w-[140px]">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    <span x-text="selectedCategory === 'all' ? 'Semua Jenis' :
                                                 selectedCategory === 'rv' ? 'Receipt Voucher' :
                                                 selectedCategory === 'pv' ? 'Payment Voucher' :
                                                 selectedCategory === 'jv' ? 'Journal Voucher' : 'Semua Jenis'"></span>
                                </div>
                                <svg class="w-4 h-4 text-secondary-400 transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="isOpen"
                                 @click.away="isOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-0 mt-2 w-56 bg-white border border-secondary-200 rounded-xl shadow-lg z-50">
                                <div class="py-1">
                                    <button @click="filterCategory('all'); isOpen = false"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors duration-150"
                                            :class="selectedCategory === 'all' ? 'bg-primary-50 text-primary-700 font-medium' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span>Semua Jenis</span>
                                    </button>
                                    <button @click="filterCategory('rv'); isOpen = false"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors duration-150"
                                            :class="selectedCategory === 'rv' ? 'bg-success-50 text-success-700 font-medium' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Receipt Voucher (RV)</span>
                                    </button>
                                    <button @click="filterCategory('pv'); isOpen = false"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors duration-150"
                                            :class="selectedCategory === 'pv' ? 'bg-warning-50 text-warning-700 font-medium' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>Payment Voucher (PV)</span>
                                    </button>
                                    <button @click="filterCategory('jv'); isOpen = false"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors duration-150"
                                            :class="selectedCategory === 'jv' ? 'bg-info-50 text-info-700 font-medium' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Journal Voucher (JV)</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Search Input -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg x-show="!isSearching" class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <div x-show="isSearching" class="w-5 h-5 border-2 border-secondary-300 border-t-secondary-600 rounded-full animate-spin"></div>
                            </div>
                            <input type="text"
                                x-model="searchInput"
                                @input="searchJurnalTable()"
                                class="form-input pl-12 pr-4 py-2.5 w-full text-sm border-secondary-300 rounded-xl shadow-soft focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all duration-200"
                                placeholder="Cari nomor transaksi, jenis, atau keterangan..."
                                :disabled="isSearching">
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button type="button" class="btn-md btn-info" onclick="toggleModal('exampleModal')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Daftar Jurnal
                            </button>

                            
                            <a href="<?php echo e(route('jurnal.create')); ?>" class="btn-md btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Jurnal
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <!-- Jurnal Detail Modal -->

            <div class="fixed z-50 inset-0 hidden overflow-y-auto bg-secondary-900 bg-opacity-50" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="panel max-w-md w-full">
                        <div class="panel-header">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-secondary-800">Download Daftar Jurnal</h3>
                                <button type="button" class="text-secondary-500 hover:text-secondary-700 transition-colors" onclick="toggleModal('exampleModal')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="space-y-4">
                                <div>
                                    <label for="total-jurnal" class="form-label">Total Jurnal</label>
                                    <div class="relative">
                                        <input type="text" id="total-jurnal" class="form-input" readonly placeholder="Memuat data...">
                                        <button id="detail" type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-secondary-400 hover:text-secondary-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="dari" class="form-label">Dari Jurnal Ke</label>
                                        <input id="dari" type="number" class="form-input" placeholder="1" min="1">
                                    </div>
                                    <div>
                                        <label for="sampai" class="form-label">Sampai Jurnal Ke</label>
                                        <input id="sampai" type="number" class="form-input" placeholder="10" min="1">
                                    </div>
                                </div>
                                <div class="bg-info-50 border border-info-200 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-info-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="text-sm text-info-700">
                                            <p class="font-medium">Catatan:</p>
                                            <p>Modal akan tertutup otomatis setelah download selesai.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <div class="flex justify-end gap-3">
                                <button type="button" class="btn-secondary" onclick="toggleModal('exampleModal')">Batal</button>
                                <button id="print" type="button" class="btn-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download PDF
                                </button>
                            </div>
                        </div>
                        <a id="downloadLink" href="#" style="display: none;">Download PDF</a>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-hidden rounded-2xl border border-secondary-200/50 shadow-soft">

                <!-- Loading State with Skeleton -->
                <div x-show="isLoading" class="space-y-4">
                    <!-- Mobile Loading Skeleton -->
                    <div class="lg:hidden space-y-4 p-4">
                        <template x-for="i in 8" :key="'mobile-loading-' + i">
                            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="h-3 animate-pulse bg-gray-200 rounded w-20 mb-2" :style="`animation-delay: ${i * 75}ms`"></div>
                                        <div class="h-5 skeleton-shimmer rounded" :class="i % 3 === 0 ? 'w-24' : i % 3 === 1 ? 'w-28' : 'w-32'" :style="`animation-delay: ${i * 100}ms`"></div>
                                    </div>
                                    <div class="h-5 skeleton-shimmer rounded-full" :class="i % 4 === 0 ? 'w-10' : i % 4 === 1 ? 'w-12' : i % 4 === 2 ? 'w-14' : 'w-16'" :style="`animation-delay: ${i * 125}ms`"></div>
                                </div>
                                <div class="mb-4">
                                    <div class="h-3 animate-pulse bg-gray-200 rounded w-16 mb-2" :style="`animation-delay: ${i * 150}ms`"></div>
                                    <div class="h-4 skeleton-shimmer rounded w-full mb-2" :style="`animation-delay: ${i * 175}ms`"></div>
                                    <div class="h-4 animate-pulse bg-gray-200 rounded" :class="i % 2 === 0 ? 'w-3/4' : 'w-5/6'" :style="`animation-delay: ${i * 200}ms`"></div>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                    <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 80}ms`"></div>
                                    <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 110}ms`"></div>
                                    <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 140}ms`"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Desktop Loading Skeleton -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full min-w-full bg-white">
                            <thead>
                                <tr class="border-b border-secondary-200">
                                    <th class="t-head text-left">
                                        <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                                    </th>
                                    <th class="t-head text-center">
                                        <div class="h-4 bg-gray-200 rounded w-20 mx-auto animate-pulse"></div>
                                    </th>
                                    <th class="t-head text-left">
                                        <div class="h-4 bg-gray-200 rounded w-20 animate-pulse"></div>
                                    </th>
                                    <th class="t-head text-center">
                                        <div class="h-4 bg-gray-200 rounded w-16 mx-auto animate-pulse"></div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="i in 10" :key="'desktop-loading-' + i">
                                    <tr class="t-row">
                                        <td class="t-cell">
                                            <div class="h-4 skeleton-shimmer rounded" :class="i % 4 === 0 ? 'w-16' : i % 4 === 1 ? 'w-20' : i % 4 === 2 ? 'w-24' : 'w-28'" :style="`animation-delay: ${i * 100}ms`"></div>
                                        </td>
                                        <td class="t-cell text-center">
                                            <div class="h-5 skeleton-shimmer rounded-full mx-auto" :class="i % 3 === 0 ? 'w-12' : i % 3 === 1 ? 'w-14' : 'w-16'" :style="`animation-delay: ${i * 150}ms`"></div>
                                        </td>
                                        <td class="t-cell">
                                            <div class="space-y-2">
                                                <div class="h-4 skeleton-shimmer rounded max-w-xs" :class="i % 2 === 0 ? 'w-full' : 'w-4/5'" :style="`animation-delay: ${i * 120}ms`"></div>
                                                <div class="h-3 animate-pulse bg-gray-200 rounded" :class="i % 3 === 0 ? 'w-1/2' : i % 3 === 1 ? 'w-2/3' : 'w-3/4'" :style="`animation-delay: ${i * 180}ms`"></div>
                                            </div>
                                        </td>
                                        <td class="t-cell">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 100}ms`"></div>
                                                <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 120}ms`"></div>
                                                <div class="h-8 w-8 animate-pulse bg-gray-200 rounded-lg" :style="`animation-delay: ${i * 140}ms`"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Search Loading State -->
                <div x-show="isSearching && !isLoading" class="flex items-center justify-center py-8">
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 border-2 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                        <p class="text-secondary-600 text-sm">Mencari data...</p>
                    </div>
                </div>

                <!-- Error State -->
                <div x-show="hasError && !isLoading" class="text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 bg-danger-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-800 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-secondary-600 mb-4" x-text="errorMessage"></p>
                    <div class="flex gap-3 justify-center">
                        <button @click="hasError = false; fetchJurnalData()" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Coba Lagi
                        </button>
                        <button @click="console.log('Debug data:', {allData, totalItems, isLoading, hasError, errorMessage})" class="btn-secondary">
                            Debug Info
                        </button>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div x-show="!isLoading && !hasError" class="lg:hidden space-y-4 p-4">
                    <!-- Empty State -->
                    <div x-show="!isSearching && paginatedData.length === 0" class="text-center py-16">
                        <div class="w-16 h-16 mx-auto mb-4 bg-secondary-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-secondary-800 mb-2">Belum ada data jurnal</h3>
                        <p class="text-secondary-600 mb-4">Mulai buat jurnal pertama Anda</p>
                        <a href="<?php echo e(route('jurnal.create')); ?>" class="btn-primary inline-flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Jurnal
                        </a>
                    </div>

                    <!-- Search Loading Skeleton for Mobile -->
                    

                    <template x-show="!isSearching" x-for="(jurnal, index) in paginatedData" :key="jurnal.id">
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-gray-600">No. Transaksi</span>
                                    </div>
                                    <div class="font-semibold text-gray-900" x-text="jurnal.no_urut_transaksi"></div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-primary-100 text-primary-800': jurnal.jenis === 'umum',
                                          'bg-success-100 text-success-800': jurnal.jenis === 'penjualan',
                                          'bg-info-100 text-info-800': jurnal.jenis === 'pembelian',
                                          'bg-warning-100 text-warning-800': jurnal.jenis === 'kas',
                                          'bg-secondary-100 text-secondary-800': true
                                      }"
                                      x-text="jurnal.jenis"></span>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600">Keterangan</span>
                                </div>
                                <div class="text-gray-900 break-words" x-text="jurnal.keterangan"></div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <!-- Edit Button -->
                                <a :href="`<?php echo e(url('jurnal')); ?>/${jurnal.id}/edit`"
                                   class="inline-flex items-center justify-center p-2.5 rounded-xl border border-warning-200 bg-warning-50 text-warning-700 hover:bg-warning-100 hover:border-warning-300 transition-all duration-200 shadow-soft hover:shadow-medium"
                                   title="Edit Jurnal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <!-- View Button -->
                                <button @click="showJurnalDetail(jurnal.id)"
                                        class="inline-flex items-center justify-center p-2.5 rounded-xl border border-primary-200 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:border-primary-300 transition-all duration-200 shadow-soft hover:shadow-medium"
                                        title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                <!-- Print Button -->
                                <a :href="`<?php echo e(route('report.transaksi', '')); ?>/${jurnal.id}`"
                                   target="_blank"
                                   class="inline-flex items-center justify-center p-2.5 rounded-xl border border-secondary-200 bg-secondary-50 text-secondary-700 hover:bg-secondary-100 hover:border-secondary-300 transition-all duration-200 shadow-soft hover:shadow-medium"
                                   title="Print Laporan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Desktop Table View -->
                <div x-show="!isLoading && !hasError" class="hidden lg:block overflow-x-auto">

                    <!-- Empty State for Desktop -->
                    <div x-show="!isSearching && paginatedData.length === 0" class="text-center py-16">
                        <div class="w-16 h-16 mx-auto mb-4 bg-secondary-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-secondary-800 mb-2">Belum ada data jurnal</h3>
                        <p class="text-secondary-600 mb-4">Mulai buat jurnal pertama Anda</p>
                        <a href="<?php echo e(route('jurnal.create')); ?>" class="btn-primary inline-flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Jurnal
                        </a>
                    </div>

                    <table x-show="!isSearching && paginatedData.length > 0" class="w-full min-w-full bg-white" id="jurnalTable">
                        <thead>
                            <tr class="border-b border-secondary-200">
                                <th class="t-head text-left">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        No. Transaksi
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
                                        Jenis Jurnal
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Keterangan
                                        <span class="ml-auto">
                                            <svg class="w-4 h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                        </span>
                                    </div>
                                </th>
                                <th class="t-head text-center">
                                    <div class="flex items-center gap-2 justify-center">
                                        <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="jurnalTableBody" class="divide-y divide-gray-200">
                            <!-- Search Loading Skeleton Rows -->
                            

                            <template x-show="!isSearching" x-for="(jurnal, index) in paginatedData" :key="jurnal.id">
                                <tr class="t-row hover:bg-gray-50/50 transition-colors duration-200">
                                    <td class="t-cell font-medium text-gray-900" x-text="jurnal.no_urut_transaksi"></td>
                                    <td class="t-cell text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-primary-100 text-primary-800': jurnal.jenis === 'umum',
                                                  'bg-success-100 text-success-800': jurnal.jenis === 'penjualan',
                                                  'bg-info-100 text-info-800': jurnal.jenis === 'pembelian',
                                                  'bg-warning-100 text-warning-800': jurnal.jenis === 'kas',
                                                  'bg-secondary-100 text-secondary-800': true
                                              }"
                                              x-text="jurnal.jenis"></span>
                                    </td>
                                    <td class="t-cell">
                                        <div class="max-w-xs break-words text-gray-900" x-text="jurnal.keterangan"></div>
                                    </td>
                                    <td class="t-cell">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Edit Button -->
                                            <a :href="`<?php echo e(url('jurnal')); ?>/${jurnal.id}/edit`"
                                               class="inline-flex items-center justify-center p-2.5 rounded-xl border border-warning-200 bg-warning-50 text-warning-700 hover:bg-warning-100 hover:border-warning-300 transition-all duration-200 group shadow-soft hover:shadow-medium"
                                               title="Edit Jurnal">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- View Button -->
                                            <button @click="showJurnalDetail(jurnal.id)"
                                                    class="inline-flex items-center justify-center p-2.5 rounded-xl border border-primary-200 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:border-primary-300 transition-all duration-200 group shadow-soft hover:shadow-medium"
                                                    title="Lihat Detail">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            <!-- Print Button -->
                                            <a :href="`<?php echo e(route('report.transaksi', '')); ?>/${jurnal.id}`"
                                               target="_blank"
                                               class="inline-flex items-center justify-center p-2.5 rounded-xl border border-secondary-200 bg-secondary-50 text-secondary-700 hover:bg-secondary-100 hover:border-secondary-300 transition-all duration-200 group shadow-soft hover:shadow-medium"
                                               title="Print Laporan">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div x-show="!isLoading && !isSearching && paginatedData.length > 0" class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <button @click="prevPage"
                                    :disabled="currentPage === 1"
                                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg">
                                Previous
                            </button>
                            <button @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg">
                                Next
                            </button>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-semibold" x-text="((currentPage - 1) * itemsPerPage) + 1"></span>
                                    to
                                    <span class="font-semibold" x-text="Math.min(currentPage * itemsPerPage, totalItems)"></span>
                                    of
                                    <span class="font-semibold" x-text="totalItems"></span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-lg shadow-sm" aria-label="Pagination">
                                    <button @click="prevPage"
                                            :disabled="currentPage === 1"
                                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed text-gray-300' : 'hover:bg-gray-100 text-gray-500'"
                                            class="relative inline-flex items-center rounded-l-lg px-3 py-2 text-sm font-semibold bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <span class="sr-only">Previous</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>

                                    <template x-for="page in pagesToShow" :key="page">
                                        <button @click="changePage(page)"
                                                :class="{
                                                    'bg-primary-600 text-white border-primary-600': page === currentPage,
                                                    'bg-white text-gray-500 border-gray-300 hover:bg-gray-50': page !== currentPage
                                                }"
                                                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold border focus:z-20 focus:outline-offset-0"
                                                x-text="page"></button>
                                    </template>

                                    <button @click="nextPage"
                                            :disabled="currentPage === totalPages"
                                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-gray-300' : 'hover:bg-gray-100 text-gray-500'"
                                            class="relative inline-flex items-center rounded-r-lg px-3 py-2 text-sm font-semibold bg-white border border-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <span class="sr-only">Next</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('styles'); ?>
        <style>
            /* Enhanced skeleton animations */
            @keyframes shimmer {
                0% { background-position: -200px 0; }
                100% { background-position: calc(200px + 100%) 0; }
            }

            .skeleton-shimmer {
                background: linear-gradient(90deg,
                    #f0f0f0 25%,
                    #e0e0e0 50%,
                    #f0f0f0 75%);
                background-size: 200px 100%;
                animation: shimmer 1.5s infinite linear;
            }

            .skeleton-pulse-slow {
                animation: pulse 2s infinite;
            }

            .skeleton-pulse-fast {
                animation: pulse 1s infinite;
            }

            /* Staggered animation delays */
            .animate-delay-75 { animation-delay: 75ms; }
            .animate-delay-150 { animation-delay: 150ms; }
            .animate-delay-225 { animation-delay: 225ms; }
            .animate-delay-300 { animation-delay: 300ms; }

            /* Modal Animation */
            #exampleModal:not(.hidden) {
                animation: modalFadeIn 0.3s ease-out;
            }

            #exampleModal.hidden {
                animation: modalFadeOut 0.2s ease-in;
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes modalFadeOut {
                from {
                    opacity: 1;
                    transform: scale(1);
                }
                to {
                    opacity: 0;
                    transform: scale(0.95);
                }
            }

            /* Medium Button Styles */
            .btn-md {
                @apply inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl border transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-soft hover:shadow-medium;
            }

            .btn-md.btn-primary {
                @apply bg-primary-600 text-white border-primary-600 hover:bg-primary-700 hover:border-primary-700 focus:ring-primary-500 shadow-emerald hover:shadow-emerald-lg;
            }

            .btn-md.btn-secondary {
                @apply bg-white text-secondary-700 border-secondary-300 hover:bg-secondary-50 hover:border-secondary-400 focus:ring-secondary-500;
            }

            .btn-md.btn-success {
                @apply bg-success-600 text-white border-success-600 hover:bg-success-700 hover:border-success-700 focus:ring-success-500 shadow-lg hover:shadow-xl;
            }

            .btn-md.btn-info {
                @apply bg-info-600 text-white border-info-600 hover:bg-info-700 hover:border-info-700 focus:ring-info-500 shadow-blue hover:shadow-blue-lg;
            }

            .btn-md.btn-warning {
                @apply bg-warning-600 text-white border-warning-600 hover:bg-warning-700 hover:border-warning-700 focus:ring-warning-500;
            }

            .btn-md.btn-danger {
                @apply bg-danger-600 text-white border-danger-600 hover:bg-danger-700 hover:border-danger-700 focus:ring-danger-500;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('script'); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            window.jurnalConfig = {
                printReportUrl: "<?php echo e(route('printReport')); ?>",
                csrfToken: "<?php echo e(csrf_token()); ?>"
            };
        </script>
        <script src="<?php echo e(asset('js/jurnal.js')); ?>?v=<?php echo e(filemtime(public_path('js/jurnal.js'))); ?>"></script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/jurnal/index.blade.php ENDPATH**/ ?>