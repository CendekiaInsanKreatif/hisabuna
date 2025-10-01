<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 xl:px-8 w-full overflow-hidden" x-data="coaTable" x-cloak>
    <?php
        $fields = [
            [
                'name' => 'nomor_akun',
                'type' => 'text',
                'label' => 'Nomor Akun',
                'required' => true,
            ],
            [
                'name' => 'nama_akun',
                'type' => 'text',
                'label' => 'Nama Akun',
                'required' => true,
            ],
        ];
    ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fields,'maxWidth' => 'sm','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fields),'maxWidth' => 'sm','focusable' => true]); ?>
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
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-emerald">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-secondary-800">Chart of Accounts</h1>
                <p class="text-secondary-600 font-medium">Kelola dan pantau struktur akun perusahaan Anda</p>
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
            <!-- Filter and Action Bar -->
            <div class="bg-secondary-50 p-4 rounded-lg border border-secondary-200 mb-6">
                <!-- Top Row: Preview, Import, Export buttons (left corner) -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('report.preview-coa')); ?>" target="_blank" class="h-10 px-3 bg-white border border-info-300 text-info-700 hover:bg-info-50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview
                        </a>

                        <button x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.import')); ?>', name: 'coas.import', title: 'Import Akun', type: 'custom' })" class="h-10 px-3 bg-white border border-warning-300 text-warning-700 hover:bg-warning-50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Import
                        </button>

                        <form method="POST" action="<?php echo e(route('coas.export')); ?>" class="inline-block">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="h-10 px-3 bg-white border border-success-300 text-success-700 hover:bg-success-50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bottom Row: Search, Filters, Reset, and Add buttons (aligned) -->
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
                    <!-- Search Input -->
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                            x-model="searchInput"
                            @keydown.enter="searchCoaTable"
                            class="pl-10 pr-4 h-10 w-64 text-sm border border-secondary-300 rounded-lg bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200"
                            placeholder="Cari nomor atau nama akun...">
                    </div>

                    <!-- Filter Dropdowns -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-secondary-700 whitespace-nowrap">Filter:</span>

                        <!-- Category Filter -->
                        <select x-model="filter"
                                class="h-10 px-3 bg-white border border-secondary-300 rounded-lg text-sm font-medium text-secondary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 min-w-[80px]">
                            <option value="all">Semua</option>
                            <option value="neraca">Neraca</option>
                            <option value="labarugi">L/R</option>
                        </select>

                        <!-- Level Filter -->
                        <select x-model="levelFilter" @change="filterLevel(levelFilter)"
                                class="h-10 px-3 bg-white border border-secondary-300 rounded-lg text-sm font-medium text-secondary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 min-w-[70px]">
                            <option value="all">Level</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>

                        <!-- Head Filter -->
                        <select x-model="headFilter" @change="filterByKepala(headFilter)"
                                class="h-10 px-3 bg-white border border-secondary-300 rounded-lg text-sm font-medium text-secondary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 min-w-[80px]">
                            <option value="all">Kepala</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                        <button @click="reloadPage()" type="button" class="h-10 px-3 bg-white border border-secondary-300 text-secondary-700 hover:bg-secondary-50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0V9a8 8 0 1115.356 2m-15.356 0H4"/>
                            </svg>
                            Reset
                        </button>

                        <button class="h-10 px-3 bg-primary-600 text-white hover:bg-primary-700 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
                                x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.store')); ?>', name: 'coas.create', title: 'Tambah Akun', type: 'form' })">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-hidden rounded-lg border border-secondary-200 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full bg-white table-fixed sm:table-auto" id="coaTable">
                        <thead>
                            <tr>
                                <th class="t-head text-left w-24 sm:w-32">
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <span class="text-xs sm:text-sm">Kode</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="t-head text-left">
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-secondary-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm">Nama Akun</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-secondary-400 hover:text-secondary-600 cursor-pointer transition-colors ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="t-head text-center w-16 sm:w-20">
                                    <div class="flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-info-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm">Level</span>
                                    </div>
                                </th>
                                <th class="t-head text-center w-20 sm:w-28 hidden sm:table-cell">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm">Saldo</span>
                                    </div>
                                </th>
                                <th class="t-head text-center w-20 sm:w-24">
                                    <div class="flex items-center justify-center">
                                        <span class="text-xs sm:text-sm">Aksi</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="coaTableBody" class="divide-y divide-secondary-100">
                            <template x-for="coa in paginatedData" :key="coa.id">
                                <tr class="hover:bg-secondary-50 transition-colors duration-200 group"
                                    x-on:click.prevent="$dispatch('open-modal', { route: `<?php echo e(route('coas.show', '')); ?>/${coa.id}`, name: 'coas.show', title: 'Lihat Akun', data: coa, type: 'form' })">

                                    <td class="px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm">
                                        <span class="font-mono font-semibold text-secondary-800 block truncate" x-text="formatNomorAkun(coa.nomor_akun)"></span>
                                    </td>

                                    <td class="px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm">
                                        <div class="font-medium text-secondary-800 truncate" x-text="coa.nama_akun"></div>
                                        <div class="text-xs text-secondary-500 mt-1 sm:hidden" x-text="`L${coa.level} | ${coa.saldo_normal === 'debit' ? 'D' : 'C'}`"></div>
                                    </td>

                                    <td class="px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 rounded-lg text-xs font-bold"
                                              :class="{
                                                  'bg-red-100 text-red-800': coa.level == 1,
                                                  'bg-orange-100 text-orange-800': coa.level == 2,
                                                  'bg-yellow-100 text-yellow-800': coa.level == 3,
                                                  'bg-green-100 text-green-800': coa.level == 4,
                                                  'bg-blue-100 text-blue-800': coa.level == 5
                                              }"
                                              x-text="coa.level">
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm text-center hidden sm:table-cell">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="coa.saldo_normal === 'debit' ? 'bg-success-100 text-success-800' : 'bg-warning-100 text-warning-800'"
                                              x-text="coa.saldo_normal === 'debit' ? 'Debit' : 'Credit'">
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <button class="p-1 sm:p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded transition-colors"
                                                    x-on:click.prevent.stop="$dispatch('open-modal', { route: `<?php echo e(route('coas.update', '')); ?>/${coa.id}`, name: 'coas.update', title: 'Edit Akun', data: coa, method: 'PUT', type: 'form' })"
                                                    title="Edit">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="p-1 sm:p-1.5 text-danger-600 hover:text-danger-800 hover:bg-danger-50 rounded transition-colors"
                                                    x-on:click.prevent.stop="$dispatch('open-modal', { route: `<?php echo e(route('coas.destroy', '')); ?>/${coa.id}`, name: 'coas.destroy', title: 'Hapus Akun', data: coa, method: 'DELETE', type: 'delete' })"
                                                    title="Hapus">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- Loading State -->
                            <template x-if="loading">
                                <tr>
                                    <td class="px-4 py-16 text-center sm:hidden" colspan="4">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                                            <p class="text-secondary-600 text-sm">Memuat data...</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-16 text-center hidden sm:table-cell" colspan="5">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                                            <p class="text-secondary-600">Memuat data akun...</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- Error State -->
                            <template x-if="!loading && error">
                                <tr>
                                    <td class="px-4 py-16 text-center sm:hidden" colspan="4">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-10 h-10 bg-danger-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="font-semibold text-secondary-800 text-sm">Error</h3>
                                                <p class="text-secondary-600 text-xs mt-1" x-text="error"></p>
                                            </div>
                                            <button @click="fetchCoaData()" class="px-3 py-1 bg-danger-600 text-white rounded text-xs">
                                                Coba Lagi
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-16 text-center hidden sm:table-cell" colspan="5">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-12 h-12 bg-danger-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="font-semibold text-secondary-800">Terjadi Kesalahan</h3>
                                                <p class="text-secondary-600 text-sm mt-1" x-text="error"></p>
                                            </div>
                                            <button @click="fetchCoaData()" class="btn btn-danger btn-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Coba Lagi
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- Empty State -->
                            <template x-if="!loading && !error && paginatedData.length === 0">
                                <tr>
                                    <td class="px-4 py-16 text-center sm:hidden" colspan="4">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-12 h-12 bg-secondary-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="font-semibold text-secondary-800 text-sm">Belum Ada Data</h3>
                                                <p class="text-secondary-600 text-xs mt-1">Tambahkan akun untuk memulai.</p>
                                            </div>
                                            <button class="px-3 py-1 bg-primary-600 text-white rounded text-xs"
                                                    x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.store')); ?>', name: 'coas.create', title: 'Tambah Akun', type: 'form' })">
                                                Tambah Akun
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-16 text-center hidden sm:table-cell" colspan="5">
                                        <div class="flex flex-col items-center space-y-4">
                                            <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="font-semibold text-secondary-800">Belum Ada Data</h3>
                                                <p class="text-secondary-600 text-sm mt-1">Belum ada akun dalam sistem. Tambahkan akun pertama untuk memulai.</p>
                                            </div>
                                            <button class="btn btn-primary btn-sm"
                                                    x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.store')); ?>', name: 'coas.create', title: 'Tambah Akun', type: 'form' })">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Tambah Akun
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                                </tbody>
                            </table>

                        <!-- Pagination -->
                        <div class="px-4 py-3 bg-white border-t border-secondary-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <!-- Mobile pagination -->
                                <div class="flex flex-1 justify-between sm:hidden">
                                    <button @click="previousPage" :disabled="currentPage === 1"
                                            class="btn btn-secondary btn-sm" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Sebelumnya
                                    </button>
                                    <span class="text-sm text-secondary-600">
                                        Halaman <span x-text="currentPage"></span> dari <span x-text="totalPage"></span>
                                    </span>
                                    <button @click="nextPage" :disabled="currentPage === totalPage"
                                            class="btn btn-secondary btn-sm" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPage }">
                                        Selanjutnya
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Desktop pagination -->
                                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-secondary-600">
                                            Menampilkan
                                            <span class="font-medium text-secondary-800" x-text="Math.min((currentPage - 1) * rowsPerPage + 1, filteredData.length)"></span>
                                            sampai
                                            <span class="font-medium text-secondary-800" x-text="Math.min(currentPage * rowsPerPage, filteredData.length)"></span>
                                            dari
                                            <span class="font-medium text-secondary-800" x-text="filteredData.length"></span>
                                            hasil
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <button @click="previousPage" :disabled="currentPage === 1"
                                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-secondary-300 bg-white text-sm font-medium text-secondary-500 hover:bg-secondary-50"
                                                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <template x-for="page in pagesToShow" :key="page">
                                                <button @click="goToPage(page)"
                                                        :class="{
                                                            'z-10 bg-primary-50 border-primary-500 text-primary-600': page === currentPage,
                                                            'bg-white border-secondary-300 text-secondary-500 hover:bg-secondary-50': page !== currentPage
                                                        }"
                                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                        x-text="page">
                                                </button>
                                            </template>

                                            <button @click="nextPage" :disabled="currentPage === totalPage"
                                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-secondary-300 bg-white text-sm font-medium text-secondary-500 hover:bg-secondary-50"
                                                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPage }">
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->startPush('script'); ?>
        <script>
            $(document).ready(function() {
                let table;
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log('CSRF Token:', csrfToken);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // Reset functionality now handled by Alpine.js

                // Level and search functionality now handled by Alpine.js

                function loadDataLevelAkun(level, page, search ='') {
                    $.post('filterCoaLevel', { level: level, page: page, search:search }).done((res, status, xhr) => {
                        $('#coaTableBody').empty();
                        $.each(res.data, function(index, item) {
                            // Kita tidak perlu stringify item, langsung gunakan dalam template literal
                            let itemDataLevel = JSON.stringify(item).replace(/"/g, '&quot;');
                            $('#coaTableBody').append(`
                                <tr>
                                    <td>${item.nomor_akun}</td>
                                    <td>${item.nama_akun}</td>
                                    <td>${item.level}</td>
                                    <td>${item.saldo_normal}</td>
                                    <td>
                                        <a class="btn btn-action-primary"
                                        x-on:click.prevent.stop="$dispatch('open-modal', {
                                            route: '<?php echo e(route('coas.update', '')); ?>/' + ${item.id},
                                            name: 'coas.update',
                                            title: 'Edit Akun',
                                            data: ${itemDataLevel},
                                            method: 'PUT',
                                            type: 'form' })">
                                        Edit
                                        </a>

                                        <a class="btn btn-action-danger"
                                        x-on:click.prevent.stop="$dispatch('open-modal', {
                                            route: '<?php echo e(route('deleteCoa', '')); ?>/${item.id}',
                                            name: 'coas.destroy',
                                            title: 'Hapus Akun',
                                            data: ${itemDataLevel},
                                            method: 'DELETE',
                                            type: 'delete' })">
                                        Delete
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });

                        // Render pagination baru
                        renderPaginationLevel(level, res, search);
                    }).fail((error) => {
                        console.error('Error saat memuat data:', error);
                    });
                }

                function renderPaginationLevel(level, res, search = '') {
                    const paginationDiv = $('#pageNumbers');
                    paginationDiv.empty(); // Kosongkan elemen pagination sebelumnya

                    // Mendapatkan halaman yang ditampilkan
                    const pagesToShow = [];
                    for (let i = 1; i <= res.last_page; i++) {
                        pagesToShow.push(i);
                    }

                    // Render tombol Previous
                    $('.prev').off('click').on('click', function(e) {
                        e.preventDefault();
                        if (res.current_page > 1) {
                            loadDataLevelAkun(level, res.current_page - 1, search); // Memuat halaman sebelumnya
                        }
                    });

                    // Render halaman berdasarkan `pagesToShow`
                    $.each(pagesToShow, function(index, page) {
                        paginationDiv.append(`
                            <button class="page-number py-1 px-3 rounded ${page === res.current_page ? 'bg-primary-600 text-white' : 'bg-gray-200'}" data-page="${page}">
                                ${page}
                            </button>
                        `);
                    });

                    // Event listener untuk tombol halaman
                    paginationDiv.find('.page-number').off('click').on('click', function(e) {
                        e.preventDefault();
                        const page = $(this).data('page');
                        loadDataLevelAkun(level, page, search); // Memuat halaman yang dipilih
                    });

                    // Render tombol Next
                    $('.next').off('click').on('click', function(e) {
                        e.preventDefault();
                        if (res.current_page < res.last_page) {
                            loadDataLevelAkun(level, res.current_page + 1, search); // Memuat halaman berikutnya
                        }
                    });
                }


                // Head account filter functionality now handled by Alpine.js

                // Search functionality now handled by Alpine.js

                function loadData(kepala, page, search ='') {
                    console.log('Memuat data halaman:', page); // Debugging
                    $.post('filterCoa', { kepala: kepala, page: page, search:search})
                        .done((res) => {
                            console.log('Response:', res); // Debugging
                            $('#coaTableBody').empty(); // Kosongkan tabel
                            // Looping untuk menambahkan data ke tabel
                            $.each(res.data, function(index, item) {
                            let itemData = JSON.stringify(item).replace(/"/g, '&quot;');  // Escape kutipan ganda
                            $('#coaTableBody').append(`
                                <tr>
                                    <td>${item.nomor_akun}</td>
                                    <td>${item.nama_akun}</td>
                                    <td>${item.level}</td>
                                    <td>${item.saldo_normal}</td>
                                    <td>
                                        <a class="btn btn-action-primary"
                                        x-on:click.prevent.stop="$dispatch('open-modal', {
                                            route: '<?php echo e(route('coas.update', '')); ?>/' + ${item.id},
                                            name: 'coas.update',
                                            title: 'Edit Akun',
                                            data: ${itemData},
                                            method: 'PUT',
                                            type: 'form' })">
                                        Edit
                                        </a>

                                        <a class="btn btn-action-danger"
                                        x-on:click.prevent.stop="$dispatch('open-modal', {
                                            route: '<?php echo e(route('deleteCoa', '')); ?>/${item.id}',
                                            name: 'coas.destroy',
                                            title: 'Hapus Akun',
                                            data: ${itemData},
                                            method: 'DELETE',
                                            type: 'delete' })">
                                        Delete
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });
                              // Render pagination baru
                            renderPagination(kepala, res, search);
                        }).fail((error) => {
                            console.error('Error saat memuat data:', error);
                        });
                }

                function renderPagination(kepala, res, search = '') {
                    const paginationDiv = $('#pageNumbers');
                    paginationDiv.empty(); // Kosongkan elemen pagination sebelumnya

                    // Mendapatkan halaman yang ditampilkan
                    const pagesToShow = [];
                    for (let i = 1; i <= res.last_page; i++) {
                        pagesToShow.push(i);
                    }

                    // Render tombol Previous
                    $('.prev').off('click').on('click', function(e) {
                        e.preventDefault();
                        if (res.current_page > 1) {
                            loadData(kepala, res.current_page - 1, search); // Memuat halaman sebelumnya
                        }
                    });

                    // Render halaman berdasarkan `pagesToShow`
                    $.each(pagesToShow, function(index, page) {
                        paginationDiv.append(`
                            <button class="page-number py-1 px-3 rounded ${page === res.current_page ? 'bg-primary-600 text-white' : 'bg-gray-200'}" data-page="${page}">
                                ${page}
                            </button>
                        `);
                    });

                    // Event listener untuk tombol halaman
                    paginationDiv.find('.page-number').off('click').on('click', function(e) {
                        e.preventDefault();
                        const page = $(this).data('page');
                        loadData(kepala, page, search); // Memuat halaman yang dipilih
                    });

                    // Render tombol Next
                    $('.next').off('click').on('click', function(e) {
                        e.preventDefault();
                        if (res.current_page < res.last_page) {
                            loadData(kepala, res.current_page + 1, search); // Memuat halaman berikutnya
                        }
                    });

                }

                // Fungsi untuk Edit
                window.editData = function(id) {
                    console.log(`Edit data dengan ID: ${id}`);
                };

                // Fungsi untuk Delete
                window.deleteData = function(id) {
                    console.log(`Hapus data dengan ID: ${id}`);
                };

            })
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('coaTable', () => ({
                    currentPage: 1,
                    rowsPerPage: 7,
                    totalRows: 0,
                    totalPage: 0,
                    sortDirection: 'asc',
                    filter: 'all',
                    levelFilter: 'all',
                    headFilter: 'all',
                    searchInput: '',
                    allData: [],
                    hover: false,
                    loading: true,
                    error: null,
                    get paginatedData() {
                        const filteredData = this.filteredData;
                        const start = (this.currentPage - 1) * this.rowsPerPage;
                        const end = start + this.rowsPerPage;
                        return filteredData.slice(start, end);
                    },
                    get pagesToShow() {
                        const startPage = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
                        const endPage = Math.min(startPage + 4, this.totalPage);
                        return Array.from({
                            length: endPage - startPage + 1
                        }, (_, i) => startPage + i);
                    },
                    changePage(page) {
                        this.currentPage = page;
                    },
                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                        }
                    },
                    nextPage() {
                        if (this.currentPage < this.totalPage) {
                            this.currentPage++;
                        }
                    },
                    async fetchCoaData() {
                        this.loading = true;
                        this.error = null;
                        const overlay = document.getElementById('overlay');
                        if (overlay) overlay.style.display = 'flex';

                        try {
                            const response = await fetch('api/coas', {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                }
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const result = await response.json();
                            console.log('Fetched COA data:', result); // Debug log

                            // Handle different response formats
                            let data = result;
                            if (result.data && Array.isArray(result.data)) {
                                data = result.data;
                            } else if (result.coas && Array.isArray(result.coas)) {
                                data = result.coas;
                            } else if (!Array.isArray(result)) {
                                // If it's not an array and doesn't have a data property, try to extract from Laravel pagination
                                if (result.data && result.data.data) {
                                    data = result.data.data;
                                } else {
                                    console.error('Unexpected data format:', result);
                                    this.error = 'Format data tidak sesuai dari server';
                                    return;
                                }
                            }

                            if (Array.isArray(data)) {
                                this.allData = data;
                                this.totalRows = data.length;
                                this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                                console.log(`Loaded ${data.length} COA records`); // Debug log
                            } else {
                                console.error('Data is not an array:', data);
                                this.error = 'Data bukan berupa array';
                            }
                        } catch (error) {
                            console.error('Error fetching COA data:', error);
                            this.error = `Error mengambil data COA: ${error.message}`;
                        } finally {
                            this.loading = false;
                            if (overlay) overlay.style.display = 'none';
                        }
                    },
                    get totalRows() {
                        return this.filteredData.length;
                    },
                    get totalPage() {
                        return Math.ceil(this.totalRows / this.rowsPerPage);
                    },
                    searchCoaTable() {
                        this.currentPage = 1;
                    },
                    filterCategory(category) {
                        this.filter = category;
                        this.headFilter = null; // Reset head filter when using category filter
                        this.currentPage = 1;
                    },
                    filterLevel(level) {
                        this.levelFilter = level.toString();
                        this.currentPage = 1;
                    },
                    reloadPage() {
                        this.filter = 'all';
                        this.headFilter = 'all';
                        this.levelFilter = 'all';
                        this.searchInput = '';
                        this.currentPage = 1;
                    },
                    filterByKepala(kepala) {
                        this.headFilter = kepala;
                        this.currentPage = 1;
                    },
                    previousPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                        }
                    },
                    goToPage(page) {
                        this.currentPage = page;
                    },
                    get filteredData() {
                        return this.allData.filter(coa => {
                            // Head account filter
                            let matchesHead = true;
                            if (this.headFilter !== '' && this.headFilter !== null && this.headFilter !== 'all') {
                                matchesHead = coa.nomor_akun.toString().charAt(0) == this.headFilter.toString();
                            }

                            // Level filter (check if levelFilter is set and not 'all')
                            let matchesLevel = true;
                            if (this.levelFilter !== '' && this.levelFilter !== null && this.levelFilter !== 'all') {
                                matchesLevel = coa.level == this.levelFilter;
                            }

                            // Category filter (only apply when not 'all')
                            let matchesCategory = true;
                            if (this.filter === 'neraca') {
                                matchesCategory = ['1', '2', '3'].includes(coa.nomor_akun.toString().charAt(0));
                            } else if (this.filter === 'labarugi') {
                                matchesCategory = ['4', '5', '6', '7', '8'].includes(coa.nomor_akun.toString().charAt(0));
                            }

                            // Search filter
                            const matchesSearch = this.searchInput === '' ||
                                coa.nama_akun.toLowerCase().includes(this.searchInput.toLowerCase()) ||
                                coa.nomor_akun.toString().toLowerCase().includes(this.searchInput.toLowerCase());

                            return matchesHead && matchesLevel && matchesCategory && matchesSearch;
                        });
                    },

                    get totalPages() {
                        return Math.ceil(this.filteredData.length / this.rowsPerPage);
                    },

                    get itemsPerPage() {
                        return this.rowsPerPage;
                    },
                    formatNomorAkun(nomor_akun) {
                        let formatted = nomor_akun.replace(/\D/g, ''); // Hapus karakter non-digit
                        if (formatted.length > 6) {
                            // Format untuk level 5, misalnya 111-11-011
                            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' +
                                formatted.slice(5);
                        } else if (formatted.length > 4) {
                            // Format untuk level 4, misalnya 111-11
                            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
                        } else {
                            // Format untuk level 3 atau kurang, misalnya 111
                            formatted = formatted.slice(0, 3);
                        }
                        return formatted;
                    },
                    exportCoaTable() {
                        window.location.href = '<?php echo e(route('coas.export')); ?>';
                    },
                    async init() {
                        console.log('Initializing COA table...'); // Debug log
                        try {
                            await this.fetchCoaData();
                            console.log('COA table initialized successfully'); // Debug log
                        } catch (error) {
                            console.error('Error initializing COA table:', error);
                        }
                    }
                }));
            });

        </script>
    <?php $__env->stopPush(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/coas/index.blade.php ENDPATH**/ ?>