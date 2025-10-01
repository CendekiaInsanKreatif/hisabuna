@php
    $fieldSelect = [
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

$report = [
    [
        'label' => 'Neraca',
        'route' => '/report/neraca'
    ],
    [
        'label' => 'Neraca Perbandingan',
        'route' => '/report/neraca-perbandingan'
    ],
    [
        'label' => 'Laba / Rugi',
        'route' => '/report/labarugi'
    ],
    [
        'label' => 'Laporan Perubahan Modal',
        'route' => '/report/perubahanekuitas'
    ],
    [
        'label' => 'Laporan Arus Kas',
        'route' => '/report/aruskas'
    ],
    [
        'label' => 'Neraca Saldo',
        'route' => '/report/neraca-saldo'
    ],
    [
        'label' => 'Mutasi Saldo',
        'route' => '/report/mutasi-saldo'
    ],
    [
        'label' => 'Buku Besar',
        'route' => '/report/bukubesar'
    ]
];
@endphp

@extends('layouts.app')
@section('content')
<x-modal :field="$fieldSelect" :data="@$coa" maxWidth="2xl" focusable />
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8" x-data="">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">📊 Pusat Laporan</h1>
                    <p class="text-gray-600">Akses semua laporan keuangan dalam satu tempat</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="bg-white rounded-lg border border-gray-200 px-4 py-2">
                        <div class="flex items-center space-x-2 text-sm">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-700 font-medium">Periode: {{ auth()->user()->periode }}</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-medium">{{ count($report) }} Jenis Laporan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="space-y-6 mb-8">
            <!-- Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="report-search" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200" placeholder="Cari laporan (contoh: neraca, laba rugi, arus kas...)">
                </div>
            </div>

            <!-- Date Filter Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter Periode Laporan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Tanggal Mulai
                        </label>
                        <input type="text" id="start_date" name="start_date"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 hover:border-gray-400"
                               placeholder="Pilih tanggal mulai">
                    </div>
                    <div class="space-y-2">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Tanggal Selesai
                        </label>
                        <input type="text" id="end_date" name="end_date"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 hover:border-gray-400"
                               placeholder="Pilih tanggal selesai">
                    </div>
                </div>
                <!-- Quick Date Selections -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pilihan Cepat:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="quick-date-btn px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition-colors" data-period="today">
                            Hari Ini
                        </button>
                        <button type="button" class="quick-date-btn px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition-colors" data-period="this-month">
                            Bulan Ini
                        </button>
                        <button type="button" class="quick-date-btn px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition-colors" data-period="last-month">
                            Bulan Lalu
                        </button>
                        <button type="button" class="quick-date-btn px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition-colors" data-period="this-year">
                            Tahun Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="reports-grid">
            @foreach ($report as $i)
            @php
                $reportIcons = [
                    'Neraca' => '📋',
                    'Neraca Perbandingan' => '📊',
                    'Laba / Rugi' => '💰',
                    'Laporan Perubahan Modal' => '📈',
                    'Laporan Arus Kas' => '💸',
                    'Neraca Saldo' => '⚖️',
                    'Mutasi Saldo' => '🔄',
                    'Buku Besar' => '📚'
                ];
                $icon = $reportIcons[$i['label']] ?? '📄';
            @endphp
            <div class="container-card-report">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 group report-card-hover">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="text-2xl">{{ $icon }}</div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">
                                        {{ $i['label'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        @if($i['route'] == '/report/neraca')
                                            Laporan posisi keuangan perusahaan
                                        @elseif($i['route'] == '/report/neraca-perbandingan')
                                            Perbandingan neraca periode sebelumnya
                                        @elseif($i['route'] == '/report/labarugi')
                                            Laporan pendapatan dan beban
                                        @elseif($i['route'] == '/report/perubahanekuitas')
                                            Perubahan modal pemilik
                                        @elseif($i['route'] == '/report/aruskas')
                                            Arus kas masuk dan keluar
                                        @elseif($i['route'] == '/report/neraca-saldo')
                                            Saldo akhir setiap akun
                                        @elseif($i['route'] == '/report/mutasi-saldo')
                                            Pergerakan saldo akun
                                        @elseif($i['route'] == '/report/bukubesar')
                                            Detail transaksi per akun
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Buku Besar Special Filter -->
                        @if ($i['route'] == '/report/bukubesar')
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                            <label for="akun" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Filter Akun:
                            </label>
                            <div class="flex space-x-2">
                                <input type="text" id="akun" name="akun" placeholder="Pilih akun untuk filter buku besar" readonly
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                <button type="button"
                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center space-x-2"
                                        @click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Pilih</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            @if (in_array($i['route'], ['/report/neraca', '/report/neraca-perbandingan', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas']))
                            <button @click="showReport('{{ $loop->index }}', '{{ $i['route'] }}')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Opsi Tambahan
                            </button>
                            @endif
                            <button @click="downloadReport('{{ $i['route'] }}', {{ $loop->index }}, 0)"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Lihat Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Additional Options Panel -->
            @if (in_array($i['route'], ['/report/neraca', '/report/neraca-perbandingan', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas']))
            <div id="showReport{{ $loop->index }}" class="hidden mt-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Pengaturan Tanda Tangan & Lokasi
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <label for="alamat_{{$loop->index}}" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Tempat
                            </label>
                            <input type="text" id="alamat_{{$loop->index}}"
                                   oninput="funcState(this.value, 'alamat', {{ $loop->index }})"
                                   name="alamat_{{$loop->index}}"
                                   placeholder="Jakarta"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_{{$loop->index}}" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Tanggal
                            </label>
                            <input type="text" id="tanggal_{{$loop->index}}"
                                   oninput="funcState(this.value, 'tanggal', {{ $loop->index }})"
                                   name="tanggal_{{$loop->index}}"
                                   placeholder="Pilih tanggal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="dibuat_{{$loop->index}}" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Ditandatangani Oleh
                            </label>
                            <input type="text" id="dibuat_{{$loop->index}}"
                                   oninput="funcState(this.value, 'dibuat', {{ $loop->index }})"
                                   name="dibuat_{{$loop->index}}"
                                   placeholder="Nama penandatangan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="jabatan_{{$loop->index}}" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6z"></path>
                                </svg>
                                Jabatan
                            </label>
                            <input type="text" id="jabatan_{{$loop->index}}"
                                   oninput="funcState(this.value, 'jabatan', {{ $loop->index }})"
                                   name="jabatan_{{$loop->index}}"
                                   placeholder="Direktur/Manager"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="hidden text-center py-12">
            <div class="mx-auto max-w-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada laporan ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500">Coba kata kunci pencarian yang berbeda.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .report-card-hover {
        transition: all 0.3s ease;
    }
    .report-card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    }
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .quick-date-btn:active {
        transform: scale(0.95);
    }
    .container-card-report {
        transition: all 0.3s ease;
    }
    /* Smooth scrolling for better UX */
    html {
        scroll-behavior: smooth;
    }
    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            let periode = @js(auth()->user()->periode);
            let val = 0;
            let defaultDateStart = new Date(new Date(new Date().getFullYear(), new Date().getMonth(), 1).setHours(0, 0, 0, 0))
            let defaultDateEnd = new Date(new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).setHours(23, 59, 59, 999))

            // Add smooth animations to report cards
            $('.container-card-report').addClass('fade-in');

            // Loading overlay functions
            function showLoading() {
                $('body').append('<div class="loading-overlay"><div class="loading-spinner"></div></div>');
            }

            function hideLoading() {
                $('.loading-overlay').remove();
            }

            // Search functionality
            $('#report-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                let visibleCount = 0;

                $('.container-card-report').each(function() {
                    const reportTitle = $(this).find('h3').text().toLowerCase();
                    const reportDesc = $(this).find('p').text().toLowerCase();

                    if (reportTitle.includes(searchTerm) || reportDesc.includes(searchTerm)) {
                        $(this).parent().show().addClass('fade-in');
                        visibleCount++;
                    } else {
                        $(this).parent().hide();
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0 && searchTerm.length > 0) {
                    $('#no-results').removeClass('hidden');
                    $('#reports-grid').addClass('hidden');
                } else {
                    $('#no-results').addClass('hidden');
                    $('#reports-grid').removeClass('hidden');
                }
            });

            // Quick date selection
            $('.quick-date-btn').on('click', function() {
                const period = $(this).data('period');
                const today = new Date();
                let startDate, endDate;

                switch(period) {
                    case 'today':
                        startDate = endDate = today;
                        break;
                    case 'this-month':
                        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'last-month':
                        startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this-year':
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = new Date(today.getFullYear(), 11, 31);
                        break;
                }

                // Format dates
                const formatDate = (date) => {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                };

                $('#start_date').val(formatDate(startDate));
                $('#end_date').val(formatDate(endDate));

                // Update flatpickr instances
                if (window.startDatePicker) window.startDatePicker.setDate(startDate);
                if (window.endDatePicker) window.endDatePicker.setDate(endDate);

                // Visual feedback
                $('.quick-date-btn').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
                $(this).removeClass('bg-gray-100 text-gray-700').addClass('bg-emerald-100 text-emerald-700');
            });

            tambahLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                $('#jumlahLaman'+index).val(jumlah + 1);
            }

            kurangiLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                if (jumlah > 1) {
                    $('#jumlahLaman'+index).val(jumlah - 1);
                }
            }

            showReport = (index, route) => {
                if (route === '/report/neraca-perbandingan' || route === '/report/neraca' || route === '/report/labarugi' || route === '/report/aruskas' || route === '/report/perubahanekuitas') {
                    const element = $('#showReport'+index);
                    element.slideToggle(300, function() {
                        if (element.is(':visible')) {
                            element.addClass('fade-in');
                        }
                    });
                }
            }

            funcState = (value, field, index) => {
                const containers = document.querySelectorAll('.container-card-report');
                const tanggal = flatpickr(`#tanggal_${index}`, {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: `01-01-${periode}`,
                    maxDate: `31-12-${periode}`,
                    defaultDate: defaultDateStart
                });
                containers.forEach((container) => {
                    const input = container.querySelector(`#${field}_${index}`);
                    if (input) {
                        const inputsToUpdate = document.querySelectorAll(`input[id^="${field}_"]:not(#${field}_${index-1})`);
                        inputsToUpdate.forEach(inputToUpdate => {
                            inputToUpdate.value = value;
                        });
                    }
                });
            }

            const currentYear   = new Date().getFullYear();
            const defaultDate   = `01-01-${currentYear}`;

            // Initialize flatpickr and store instances globally
            window.startDatePicker = flatpickr('#start_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-1990',
                maxDate: '31-12-' + periode,
                defaultDate: defaultDate,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                    // Reset quick date button styles
                    $('.quick-date-btn').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
                }
            });

            window.endDatePicker = flatpickr('#end_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-' + periode,
                maxDate: '31-12-' + periode,
                defaultDate: defaultDateEnd,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                    var startDate = $('#start_date').val();
                    if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Perhatian!',
                                text: 'Tanggal selesai tidak boleh kurang dari tanggal mulai',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#10b981'
                            });
                        } else {
                            alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                        }
                        instance.clear();
                    }
                    // Reset quick date button styles
                    $('.quick-date-btn').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-700');
                }
            });

            window.$('#signature').on('change', function() {
                $('#signature1').toggleClass('hidden', !$(this).is(':checked'));
                $('#signature2').toggleClass('hidden', !$(this).is(':checked'));
            });


            downloadReport = function(route, index, jenis = 0) {
                // Validation
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                if (!startDate || !endDate) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Silakan pilih tanggal mulai dan tanggal selesai terlebih dahulu.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#10b981'
                        });
                    } else {
                        alert('Silakan pilih tanggal mulai dan tanggal selesai terlebih dahulu.');
                    }
                    return;
                }

                // Show loading
                showLoading();

                let akun = $('#akun').val();
                let alamat = $('#alamat_'+index).val();
                let tanggal = $('#tanggal_'+index).val();
                let dibuat = $('#dibuat_'+index).val();
                let jabatan = $('#jabatan_'+index).val();
                let jumlahLaman = $('#jumlahLaman').val();
                let token = $('meta[name="csrf-token"]').attr("content");
                let routenya = '';

                if(jenis == 0){
                    routenya = `${route}?excel=0`
                }else{
                    routenya = `${route}?excel=1`
                }

                var form = $('<form>', {
                    'method': 'POST',
                    'action': routenya,
                    'target': '_blank'
                }).append($('<input>', {
                    'name': 'start_date',
                    'value': startDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'end_date',
                    'value': endDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'akun',
                    'value': akun,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'alamat',
                    'value': alamat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'tanggal',
                    'value': tanggal,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'dibuat',
                    'value': dibuat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jabatan',
                    'value': jabatan,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jumlahLaman',
                    'value': jumlahLaman,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': '_token',
                    'value': token,
                    'type': 'hidden'
                }));

                form.appendTo('body').submit().fail(function(jqXHR) {
                    hideLoading();
                    if (jqXHR.status === 419) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Session Expired',
                                text: 'Sesi Anda telah berakhir. Halaman akan dimuat ulang.',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('Sesi Anda telah berakhir. Halaman akan dimuat ulang.');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memproses laporan.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ef4444'
                            });
                        } else {
                            alert('Terjadi kesalahan saat memproses laporan.');
                        }
                    }
                });

                // Hide loading after a short delay (for user feedback)
                setTimeout(() => {
                    hideLoading();
                }, 1000);
            };

        });

    </script>

@endpush
