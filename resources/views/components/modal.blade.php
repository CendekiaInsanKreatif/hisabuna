@props(['name', 'field' => [], 'show' => false, 'maxWidth' => '2xl', 'data' => []])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];

// da($data);
@endphp

<div x-data="{
    show: @js($show),
    title: '',
    data: {},
    route: '',
    method: '',
    name: '',
    type: '',
    isDetail: '',
    detailCount: 0,
    focusables() {
        let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
        return [...$el.querySelectorAll(selector)]
            .filter(el => !el.hasAttribute('disabled'));
    },
    firstFocusable() { return this.focusables()[0]; },
    lastFocusable() { return this.focusables().slice(-1)[0]; },
    nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
    prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },
    nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
    prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1; },
    formatNomorAkun(nomor_akun) {
        let formatted = nomor_akun.replace(/\D/g, '');
        if (formatted.length > 6) {
            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3, 5) + '-' + formatted.slice(5);
        } else if (formatted.length > 4) {
            formatted = formatted.slice(0, 3) + '-' + formatted.slice(3);
        } else {
            formatted = formatted.slice(0, 3);
        }
        return formatted;
    },
    formatInputAkun(event) {
        let input = event.target;
        let value = input.value.replace(/[^0-9]/g, ''); // Menghapus semua karakter non-numeric
        if (value.length > 6) {
            value = value.slice(0, 3) + '-' + value.slice(3, 5) + '-' + value.slice(5);
        } else if (value.length > 4) {
            value = value.slice(0, 3) + '-' + value.slice(3);
        }
        input.value = value;
    },
    formatCurrency(event) {
            let input = event.target;
            let value = input.value.replace(/[^0-9]/g, ''); // Menghapus semua karakter non-numeric
            let parsedValue = parseFloat(value);
            if (isNaN(parsedValue)) {
                parsedValue = 0;
            }
            const formattedValue = parsedValue.toLocaleString('id-ID');
            input.value = formattedValue;
            // Menyimpan nilai mentah jika diperlukan
            this.data[input.name] = parsedValue;
        },
    formatCurrencyValue(value) {
            if (typeof value === 'number') {
                return value.toLocaleString('id-ID');
            }
            return '';
    }
}" x-init="$watch('show', value => {
    if (value) {
        document.body.classList.add('overflow-y-hidden');
        {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
    } else {
        document.body.classList.remove('overflow-y-hidden');
    }
})"
    x-on:open-modal.window="console.log($event.detail.data); method = $event.detail.method; route = $event.detail.route; data = $event.detail.data; title = $event.detail.title; show = true; name = $event.detail.name; type = $event.detail.type; isDetail = $event.detail.isDetail; detailCount = $event.detail.count; from = $event.detail.from;"
    x-on:close-modal.window="show = false" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show"
    class="fixed flex items-center inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div x-show="show"
        class="mb-6 bg-white rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto border border-secondary-200/50"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <template x-if="type == 'select'">
            <div x-data="{ search: '' }" class="panel">
                <div class="panel-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-emerald">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-secondary-800">Pilih Akun</h3>
                    </div>
                </div>
                <div class="panel-body" style="max-height: 400px; position: relative;">
                    <div class="sticky top-0 bg-white z-10 mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" placeholder="Cari nomor atau nama akun..." x-model="search" id="searchBarAkun"
                                class="form-input pl-12 w-full">
                        </div>
                    </div>
                    <div class="overflow-y-auto rounded-xl border border-secondary-200/50 shadow-soft" style="max-height: 300px;">
                        <table class="w-full min-w-full text-sm" id="jurnalDetail">
                            <thead class="sticky top-0">
                                <tr>
                                    @php
                                        foreach ($field as $item) {
                                            echo '<th class="t-head text-center">' . $item['label'] . '</th>';
                                        }
                                    @endphp
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-secondary-100">
                                @foreach ($data as $item2)
                                    <tr class="hover:bg-secondary-50 cursor-pointer transition-colors duration-200"
                                        x-show="Object.values({{ json_encode($item2) }}).join(' ').toLowerCase().includes(search.toLowerCase())"
                                        x-on:click="
                                        let obj = { isDetail: isDetail, data: {{ json_encode($item2) }} };
                                        if (isDetail !== false) {
                                            document.getElementById('searchBarAkun').value = '';
                                            
                                            // Update DOM elements for visual feedback
                                            let namaAkunEl = document.getElementsByName('nama_akun[' + isDetail + ']')[0];
                                            let noAkunEl = document.getElementsByName('no_akun[' + isDetail + ']')[0];
                                            
                                            if (namaAkunEl) namaAkunEl.value = obj.data.nama_akun;
                                            if (noAkunEl) noAkunEl.value = formatNomorAkun(obj.data.nomor_akun);
                                            
                                            let firstChar = obj.data.nomor_akun.charAt(0);
                                            let teksDebit, teksKredit, styleDebit, styleKredit;
                                            if (['1'].includes(firstChar)) {
                                                teksDebit = 'Bertambah';
                                                teksKredit = 'Berkurang';
                                                styleDebit = 'color: green;';
                                                styleKredit = 'color: red;';
                                            } else if (['2', '3', '4'].includes(firstChar)) {
                                                teksDebit = 'Berkurang';
                                                teksKredit = 'Bertambah';
                                                styleDebit = 'color: red;';
                                                styleKredit = 'color: green;';
                                            } else if (['5', '6'].includes(firstChar)) {
                                                teksDebit = 'Bertambah';
                                                teksKredit = 'Berkurang';
                                                styleDebit = 'color: green;';
                                                styleKredit = 'color: red;';
                                            }
                                            
                                            let debitEl = document.getElementsByName('debit[' + isDetail + ']')[0];
                                            let kreditEl = document.getElementsByName('kredit[' + isDetail + ']')[0];
                                            if (debitEl) debitEl.placeholder = teksDebit;
                                            if (kreditEl) kreditEl.placeholder = teksKredit;
                                            
                                            // Dispatch custom event so Alpine.js in form.blade.php can update its data
                                            $dispatch('coa-selected', { 
                                                index: isDetail, 
                                                coa_akun: obj.data.nomor_akun,
                                                nama_akun: obj.data.nama_akun,
                                                coa: obj.data
                                            });
                                        } else {
                                            document.getElementsByName('akun')[0].value = formatNomorAkun(obj.data.nomor_akun);
                                        }
                                        $dispatch('close-modal');
                                        ">
                                        @foreach ($field as $item)
                                            <td class="px-4 py-3 text-center text-sm">
                                                <span class="font-medium text-secondary-800"
                                                    x-text="{{ $item['name'] == 'nomor_akun' ? 'formatNomorAkun(' . json_encode($item2[$item['name']]) . ')' : json_encode($item2[$item['name']]) }}"></span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </template>

        <template x-if="type == 'custom'">
            <div class="panel">
                <div class="panel-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-success-500 to-success-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-secondary-800">Import Excel</h2>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" x-bind:action="route" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label">Pilih File Excel</label>
                            <input type="file" name="file" id="file-input-button"
                                class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="submit" class="btn-success">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Import
                            </button>
                            <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <template x-if="type == 'form'">
            <div class="panel">
                <div class="panel-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-emerald">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-secondary-800" x-text="title"></h2>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" x-bind:action="route">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="method">
                        @if (is_array($field) && count($field) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" x-data="data">
                                <div class="space-y-4">
                                    @foreach ($field as $index => $item)
                                        @if ($index % 2 == 0)
                                            <div>
                                                <label for="{{ $item['name'] }}" class="form-label">{{ __($item['label']) }}</label>
                                                @if ($item['type'] == 'select')
                                                    <select id="{{ $item['name'] }}" name="{{ $item['name'] }}"
                                                        class="form-select"
                                                        x-bind:readonly="name.includes('show')"
                                                        x-on:input="name.includes('saldo-awal') ? formatCurrency($event) : ''"
                                                        x-bind:disabled="name.includes('show') ? true : (name.includes('saldo-awal') ? data.saldo_normal == 'credit' : false)">
                                                        @foreach ($item['options'] as $key => $value)
                                                            <option value="{{ $key }}" :selected="data.{{ $item['name'] }} == '{{ $key }}'">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input id="{{ $item['name'] }}" name="{{ $item['name'] }}"
                                                        type="{{ $item['type'] }}" class="form-input"
                                                        x-bind:readonly="name.includes('show')"
                                                        x-on:input="name.includes('saldo-awal') ? formatCurrency($event) : formatInputAkun($event)"
                                                        maxlength="10"
                                                        x-bind:disabled="name.includes('show') ? true : (name.includes('saldo-awal') ? data.saldo_normal == 'credit' : false)"
                                                        :placeholder="name === 'no_transaksi' ? 'Generate By System' : '{{ __($item['label']) }}'"
                                                        x-bind:value="name.includes('create') ? '' : (name.includes('saldo-awal') ? formatCurrencyValue(data.{{ $item['name'] }}) : data.{{ $item['name'] }})" />
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="space-y-4">
                                    @foreach ($field as $index => $item)
                                        @if ($index % 2 != 0)
                                            <div>
                                                <label for="{{ $item['name'] }}" class="form-label">{{ __($item['label']) }}</label>
                                                @if ($item['type'] == 'select')
                                                    <select id="{{ $item['name'] }}" name="{{ $item['name'] }}"
                                                        class="form-select"
                                                        x-bind:readonly="name.includes('show')"
                                                        x-on:input="name.includes('saldo-awal') ? formatCurrency($event) : ''"
                                                        x-bind:disabled="name.includes('show') ? true : (name.includes('saldo-awal') ? data.saldo_normal == 'credit' : false)">
                                                        @foreach ($item['options'] as $key => $value)
                                                            <option value="{{ $key }}" :selected="data.{{ $item['name'] }} == '{{ $key }}'">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input id="{{ $item['name'] }}" name="{{ $item['name'] }}"
                                                        type="{{ $item['type'] }}" class="form-input"
                                                        x-bind:readonly="name.includes('show')"
                                                        x-on:input="name.includes('saldo-awal') ? formatCurrency($event) : ''"
                                                        x-bind:disabled="name.includes('show') ? true : (name.includes('saldo-awal') ? data.saldo_normal == 'debit' : false)"
                                                        placeholder="{{ __($item['label']) }}"
                                                        x-bind:value="name.includes('create') ? '' : (name.includes('saldo-awal') ? formatCurrencyValue(data.{{ $item['name'] }}) : data.{{ $item['name'] }})" />
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        <template x-if="name.includes('jurnal')">
                            <div class="overflow-y-auto rounded-xl border border-secondary-200/50 shadow-soft mb-6" style="max-height: 300px;">
                                <table class="min-w-full">
                                    <thead class="sticky top-0">
                                        @php
                                            $header = ['Nomor Akun', 'Nama Akun', 'Debit', 'Kredit', 'Tanggal Bukti', 'Lampiran'];
                                        @endphp
                                        <tr>
                                            @foreach ($header as $item)
                                                <th class="t-head text-center">{{ $item }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-secondary-100">
                                        <template x-for="(detail, index) in data.details" :key="index">
                                            <tr class="hover:bg-secondary-50 transition-colors duration-200">
                                                <td class="px-4 py-3 text-center text-sm font-mono font-semibold text-secondary-800"
                                                    x-text="formatNomorAkun(detail.coa_akun)"></td>
                                                <td class="px-4 py-3 text-center text-sm font-medium text-secondary-800"
                                                    x-text="detail.coa.nama_akun"></td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-success-700"
                                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detail.debit)"></td>
                                                <td class="px-4 py-3 text-center text-sm font-semibold text-info-700"
                                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detail.credit)"></td>
                                                <td class="px-4 py-3 text-center text-sm text-secondary-600"
                                                    x-text="new Date(detail.tanggal_bukti).toLocaleDateString('id-ID')"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <a :href="`{{ asset('storage') }}/${detail.lampiran}`" target="_blank"
                                                        class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white text-xs font-semibold rounded-lg shadow-emerald hover:shadow-emerald-lg transition-all duration-300 transform hover:scale-105">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        @endif
                        <div class="flex justify-end gap-3">
                            <button type="submit" x-show="!name.includes('show')" x-bind:class="name.includes('destroy') ? 'btn-danger' : 'btn-primary'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!name.includes('destroy')">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="name.includes('destroy')">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span x-text="name.includes('destroy') ? 'Hapus' : 'Simpan'"></span>
                            </button>
                            <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
        <template x-if="type == 'delete'">
            <div class="panel">
                <div class="panel-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-danger-500 to-danger-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-secondary-800">Konfirmasi Hapus</h2>
                    </div>
                </div>
                <div class="panel-body text-center">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-danger-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-secondary-800 mb-2" x-text="title + ' ?'"></h3>
                        <p class="text-secondary-600">Data yang sudah dihapus tidak dapat dikembalikan.</p>
                    </div>
                    <form method="POST" x-bind:action="route">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="method">
                        <div class="flex justify-center gap-3">
                            <button type="submit" class="btn-danger">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ya, Hapus
                            </button>
                            <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>
