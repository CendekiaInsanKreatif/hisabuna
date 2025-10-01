@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="userTable">
    @php
        $fields = [
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Nama User',
                'required' => true,
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'Email',
                'required' => true,
            ],
            [
                'name' => 'no_hp',
                'type' => 'number',
                'label' => 'Nomor HP',
                'required' => true,
            ],
            [
                'name' => 'no_telp',
                'type' => 'number',
                'label' => 'Nomor Telepon',
                'required' => false,
            ],
            [
                'name' => 'company_name',
                'type' => 'text',
                'label' => 'Nama Perusahaan',
                'required' => true,
            ],
            [
                'name' => 'company_logo',
                'type' => 'file',
                'label' => 'Logo Perusahaan',
                'required' => true,
            ],
            [
                'name' => 'periode',
                'type' => 'number',
                'label' => 'Periode',
                'required' => true,
            ],
            [
                'name' => 'password',
                'type' => 'password',
                'label' => 'Password',
                'required' => true,
            ],
            [
                'name' => 'profile',
                'type' => 'select',
                'label' => 'Profile',
                'required' => true,
                'options' => [
                    'trial' => 'Trial',
                    'standard' => 'Standard',
                    'pro' => 'Pro',
                    'enterprise' => 'Enterprise',
                ],
            ],
            [
                'name' => 'is_active',
                'type' => 'select',
                'label' => 'Status',
                'required' => true,
                'options' => [
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif',
                ],
            ],
        ];
    @endphp
    <x-modal :field="$fields" maxWidth="2xl" focusable />

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-emerald">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-secondary-800">Manajemen Pengguna</h1>
                <p class="text-secondary-600 font-medium">Kelola data pengguna dan akses sistem</p>
            </div>
        </div>
    </div>
    <!-- Main Card -->
    <div class="panel overflow-hidden">
        <div class="panel-header">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-secondary-800">Daftar Pengguna</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-secondary-600 bg-secondary-100 px-3 py-1 rounded-lg">
                        Total User: <span class="font-semibold" x-text="filteredData().length"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <!-- Filter and Action Bar -->
            <div class="bg-gradient-to-r from-secondary-50 to-white p-4 rounded-2xl border border-secondary-200/50 shadow-soft mb-6">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <!-- Left Section: Search and Filters -->
                    <div class="flex flex-col xl:flex-row gap-3 w-full">
                        <!-- Search Input -->
                        <div class="relative flex-1 min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                x-model="searchInput"
                                @input="searchUserTable()"
                                class="pl-10 pr-4 py-3 w-full text-sm border border-secondary-300 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300"
                                placeholder="Cari nama, email, atau perusahaan...">
                        </div>

                        <!-- Filter Dropdowns -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Status Filter -->
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex items-center justify-between w-full sm:w-36 px-4 py-3 text-sm border border-secondary-300 rounded-xl bg-white hover:bg-secondary-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="truncate" x-text="filter === 'all' ? 'Semua Status' : filter === '1' ? 'Aktif' : 'Tidak Aktif'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-secondary-500 transition-transform duration-200 flex-shrink-0 ml-2" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-10 mt-2 w-full sm:w-36 bg-white rounded-xl shadow-lg border border-secondary-200 overflow-hidden" style="display: none;">
                                    <div class="py-1">
                                        <button type="button" @click="handleStatusChange('all'); open = false"
                                            :class="filter === 'all' ? 'bg-primary-50 text-primary-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="filter === 'all' ? 'bg-primary-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Semua Status</span>
                                        </button>
                                        <button type="button" @click="handleStatusChange('1'); open = false"
                                            :class="filter === '1' ? 'bg-success-50 text-success-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="filter === '1' ? 'bg-success-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Aktif</span>
                                        </button>
                                        <button type="button" @click="handleStatusChange('0'); open = false"
                                            :class="filter === '0' ? 'bg-danger-50 text-danger-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="filter === '0' ? 'bg-danger-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Tidak Aktif</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription Filter -->
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex items-center justify-between w-full sm:w-36 px-4 py-3 text-sm border border-secondary-300 rounded-xl bg-white hover:bg-secondary-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-info-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate" x-text="selectLangganan === 'all' ? 'Semua Paket' : selectLangganan.charAt(0).toUpperCase() + selectLangganan.slice(1)"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-secondary-500 transition-transform duration-200 flex-shrink-0 ml-2" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-10 mt-2 w-full sm:w-36 bg-white rounded-xl shadow-lg border border-secondary-200 overflow-hidden" style="display: none;">
                                    <div class="py-1">
                                        <button type="button" @click="handleLanggananChange('all'); open = false"
                                            :class="selectLangganan === 'all' ? 'bg-primary-50 text-primary-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="selectLangganan === 'all' ? 'bg-primary-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Semua Paket</span>
                                        </button>
                                        <button type="button" @click="handleLanggananChange('trial'); open = false"
                                            :class="selectLangganan === 'trial' ? 'bg-warning-50 text-warning-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="selectLangganan === 'trial' ? 'bg-warning-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Trial</span>
                                        </button>
                                        <button type="button" @click="handleLanggananChange('standard'); open = false"
                                            :class="selectLangganan === 'standard' ? 'bg-info-50 text-info-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="selectLangganan === 'standard' ? 'bg-info-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Standard</span>
                                        </button>
                                        <button type="button" @click="handleLanggananChange('pro'); open = false"
                                            :class="selectLangganan === 'pro' ? 'bg-primary-50 text-primary-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="selectLangganan === 'pro' ? 'bg-primary-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Pro</span>
                                        </button>
                                        <button type="button" @click="handleLanggananChange('enterprise'); open = false"
                                            :class="selectLangganan === 'enterprise' ? 'bg-success-50 text-success-700' : 'text-secondary-700 hover:bg-secondary-50'"
                                            class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full flex-shrink-0" :class="selectLangganan === 'enterprise' ? 'bg-success-500' : 'bg-transparent'"></div>
                                            <span class="truncate">Enterprise</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add User Button -->
                        <div class="flex-shrink-0">
                            <button type="button"
                                x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('users.store') }}', name: 'users.create', title: 'Tambah User', type: 'form' })"
                                class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-white bg-primary-600 border border-primary-600 rounded-xl hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="overflow-hidden rounded-2xl border border-secondary-200/50 shadow-soft">
                <div class="overflow-x-auto">
                    <table class="w-full bg-white" id="userTable" style="min-width: 800px;">
                        <thead>
                            <tr class="border-b border-secondary-200">
                                <th class="t-head text-left" style="width: 25%;">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="truncate">Nama User</span>
                                    </div>
                                </th>
                                <th class="t-head text-left hidden sm:table-cell" style="width: 25%;">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate">Email</span>
                                    </div>
                                </th>
                                <th class="t-head text-left hidden md:table-cell" style="width: 15%;">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="truncate">HP</span>
                                    </div>
                                </th>
                                <th class="t-head text-center" style="width: 12%;">
                                    <div class="flex items-center gap-1 justify-center">
                                        <svg class="w-4 h-4 text-info-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate">Paket</span>
                                    </div>
                                </th>
                                <th class="t-head text-center" style="width: 10%;">
                                    <div class="flex items-center gap-1 justify-center">
                                        <svg class="w-4 h-4 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="truncate">Status</span>
                                    </div>
                                </th>
                                <th class="t-head text-center" style="width: 13%;">
                                    <div class="flex items-center gap-1 justify-center">
                                        <svg class="w-4 h-4 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                        <span class="truncate">Aksi</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-secondary-100">
                            <template x-for="user in paginatedData" :key="user.id">
                                <tr class="hover:bg-secondary-50 transition-colors duration-200 group cursor-pointer"
                                    x-on:click.prevent="$dispatch('open-modal', { route: `{{ route('users.show', '') }}/${user.id}`, name: 'users.show', title: 'Detail User', data: user, type: 'form' })">
                                    <td class="px-3 py-4 text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                                <span x-text="user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-medium text-secondary-800 truncate" x-text="user.name"></div>
                                                <div class="text-xs text-secondary-500 truncate sm:hidden" x-text="user.email"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-secondary-700 hidden sm:table-cell">
                                        <div class="truncate" x-text="user.email"></div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-secondary-700 hidden md:table-cell">
                                        <div class="truncate" x-text="user.no_hp || '-'"></div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-center">
                                        <span x-text="user.profile.charAt(0).toUpperCase() + user.profile.slice(1)"
                                              :class="{
                                                'trial': 'inline-flex items-center rounded-lg bg-warning-100 px-2 py-1 text-xs font-semibold text-warning-700',
                                                'standard': 'inline-flex items-center rounded-lg bg-info-100 px-2 py-1 text-xs font-semibold text-info-700',
                                                'pro': 'inline-flex items-center rounded-lg bg-primary-100 px-2 py-1 text-xs font-semibold text-primary-700',
                                                'enterprise': 'inline-flex items-center rounded-lg bg-success-100 px-2 py-1 text-xs font-semibold text-success-700'
                                              }[user.profile || 'enterprise']">
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-center">
                                        <span x-text="user.is_active == 1 ? 'Aktif' : 'Nonaktif'"
                                              :class="user.is_active == 1
                                                ? 'inline-flex items-center rounded-lg bg-success-500 px-2 py-1 text-xs font-semibold text-white'
                                                : 'inline-flex items-center rounded-lg bg-danger-500 px-2 py-1 text-xs font-semibold text-white'">
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <div class="flex items-center gap-1 justify-center">
                                            <button type="button"
                                                x-on:click.prevent.stop="$dispatch('open-modal', { route: `{{ route('users.update', '') }}/${user.id}`, name: 'users.update', title: 'Edit User', data: user, method: 'PUT', type: 'form' })"
                                                class="p-1.5 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-600 transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                x-on:click.prevent.stop="$dispatch('open-modal', { route: `{{ route('users.destroy', '') }}/${user.id}`, name: 'users.destroy', title: 'Hapus User', data: user, method: 'DELETE', type: 'delete' })"
                                                class="p-1.5 rounded-lg bg-danger-50 hover:bg-danger-100 text-danger-600 transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 py-4 bg-secondary-50/50 border-t border-secondary-200">
                    <div class="text-sm text-secondary-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold" x-text="Math.min((currentPage - 1) * rowsPerPage + 1, filteredData().length)"></span>
                        - <span class="font-semibold" x-text="Math.min(currentPage * rowsPerPage, filteredData().length)"></span>
                        dari <span class="font-semibold" x-text="filteredData().length"></span> pengguna
                    </div>

                    <div class="flex items-center justify-center gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-secondary-100'"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-secondary-700 bg-white border border-secondary-300 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>

                        <div class="flex gap-1">
                            <template x-for="page in pagesToShow" :key="page">
                                <button @click="changePage(page)"
                                    :class="page === currentPage
                                        ? 'bg-primary-600 text-white border-primary-600'
                                        : 'bg-white text-secondary-700 border-secondary-300 hover:bg-secondary-50'"
                                    class="w-8 h-8 sm:w-10 sm:h-10 text-sm font-medium border rounded-lg transition-colors duration-200"
                                    x-text="page">
                                </button>
                            </template>
                        </div>

                        <button @click="nextPage" :disabled="currentPage === totalPage"
                            :class="currentPage === totalPage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-secondary-100'"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-secondary-700 bg-white border border-secondary-300 rounded-lg transition-colors duration-200">
                            <span class="hidden sm:inline">Selanjutnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    @endsection
    @push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userTable', () => ({
                currentPage: 1,
                rowsPerPage: 7,
                totalRows: 0,
                totalPage: 0,
                sortDirection: 'asc',
                filter: 'all',
                searchInput: '',
                allData: [],
                hover: false,
                selectedStatus: 'all',
                selectLangganan: 'all',
                get paginatedData() {
                    const filteredData = this.filteredData();
                    const start = (this.currentPage - 1) * this.rowsPerPage;
                    const end = start + this.rowsPerPage;
                    return filteredData.slice(start, end);
                },
                get pagesToShow() {
                    const startPage = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
                    const endPage = Math.min(startPage + 4, this.totalPage);
                    return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
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
                async fetchUserData() {
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/users');
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            this.allData = data;
                            this.totalRows = data.length;
                            this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                        } else {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format. Please check the data returned from the server.');
                        }
                    } catch (error) {
                        console.error('Error fetching User data:', error);
                        alert('Error fetching User data. Please try again later.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },
                renderUserTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchUserTable() {
                    this.renderUserTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderUserTable();
                },
                filteredData() {
                    return this.allData.filter(user => {
                        const matchesSearch = user.email.toLowerCase().includes(this.searchInput.toLowerCase()) || user.name.toLowerCase().includes(this.searchInput.toLowerCase()) || user.company_name.toLowerCase().includes(this.searchInput.toLowerCase());
                        const matchesStatus = this.filter === 'all' ? true : user.is_active === this.filter;
                        const matchesLangganan = this.selectLangganan === 'all' ? true : user.profile === this.selectLangganan;
                        return matchesSearch && matchesStatus && matchesLangganan;
                    });
                },
                handleStatusChange(status) {
                    console.log(status)
                    this.filter = status;
                    this.renderUserTable();
                },
                handleLanggananChange(langganan) {
                    console.log(langganan)
                    this.selectLangganan = langganan;
                    this.renderUserTable();
                },
                init() {
                    this.fetchUserData();
                }
            }));
        });
    </script>
    @endpush
