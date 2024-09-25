<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startSection('content'); ?>
        <div class="container mx-auto px-4" x-data="coaTable()">
            <div class="mb-6">
                <p class="text-2xl font-semibold text-emerald-500">Saldo Awal</p>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2 w-full md:w-auto">
                <form action="<?php echo e(route('saldo-awal.update')); ?>" method="post" @submit.prevent="validateAndSubmit">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                <div class="container mx-auto p-1">
                    <div class="flex flex-wrap gap-1 md:gap-3 items-center">
                        <div class="mr-auto">
                            <button type="button" @click="filterCategory('all')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Semua</button>
                            <button type="button" @click="filterCategory('neraca')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Neraca</button>
                            <button type="button" @click="filterCategory('labarugi')" class="btn-akun py-1 bg-gray-200 rounded px-3 hover:bg-emerald-500 transition duration-300">Laba Rugi</button>
                        </div>
                        <div class="ml-auto">
                            <button @click="reset" type="button" class="btn-secondary bg-gray-500 text-white py-1 px-3 rounded">Reset</button>
                            <button type="submit" class="btn-primary bg-emerald-500 text-white py-1 px-3 rounded ml-2">Simpan</button>
                        </div>
                        <div class="w-full mt-1 md:mt-0">
                            <div class="card-body overflow-x-auto mb-1">
                                <div class="flex gap-4 mt-4 w-full">
                                    <div class="bg-gray-100 p-1 rounded-lg shadow-md text-center w-full">
                                        <p class="text-sm font-medium text-gray-700">Saldo Awal Debit</p>
                                        <p class="text-lg font-semibold text-emerald-500" x-text="totalSaldoAwalDebit"></p>
                                    </div>
                                    <div class="bg-gray-100 p-1 rounded-lg shadow-md text-center w-full">
                                        <p class="text-sm font-medium text-gray-700">Saldo Awal Kredit</p>
                                        <p class="text-lg font-semibold text-emerald-500" x-text="totalSaldoAwalKredit"></p>
                                    </div>
                                    <div class="bg-gray-100 p-1 rounded-lg shadow-md text-center w-full">
                                        <p class="text-sm font-medium text-gray-700">Selisih</p>
                                        <p class="text-lg font-semibold text-emerald-500" x-text="selisihSaldoAwal"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <table class="w-full min-w-full" id="coaTable">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center">
                                    Nomor Akun
                                    <span class="ml-2">
                                        <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center">
                                    Nama Akun
                                    <span class="ml-2">
                                        <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                            <th class="bg-gray-100 px-4 py-2 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center justify-end">
                                    Debit
                                    <span class="ml-2">
                                        <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                            <th class="bg-gray-100 px-4 py-2 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center justify-end">
                                    Kredit
                                    <span class="ml-2">
                                        <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="coaTableBody">
                        <template x-for="coa in filteredData" :key="coa.id">
                            <tr @mouseover="hover = true" @mouseout="hover = false">
                                <input type="hidden" name="id[]" x-model="coa.id">
                                <input type="hidden" name="nomor_akun[]" x-model="coa.nomor_akun">
                                <input type="hidden" name="nama_akun[]" x-model="coa.nama_akun">
                                <td class="text-left px-4 py-1" x-text="formatNomorAkun(coa.nomor_akun)"></td>
                                <td class="text-left px-4 py-1" x-text="coa.nama_akun"></td>
                                <td class="text-right px-4 py-1">
                                    <input type="text" name="saldo_awal_debit[]" class="w-full text-right focus:ring-emerald-500" x-model="coa.formatted_saldo_awal_debit" x-on:input="formatCurrencyInput($event, 'debit', coa)" :readonly="coa.saldo_normal == 'credit' || coa.saldo_normal == 'kredit'" :style="(coa.saldo_normal == 'credit' || coa.saldo_normal == 'kredit') ? 'background-color: #d1d5db; text-align:right; padding-right: 10px; border: 1px solid #ccc; border-radius: 4px;' : 'text-align: right; padding-right: 10px; border: 1px solid #ccc; border-radius: 4px;'">
                                </td>
                                <td class="text-right px-4 py-1">
                                    <input type="text" name="saldo_awal_credit[]" class="w-full text-right focus:ring-emerald-500" x-model="coa.formatted_saldo_awal_credit" x-on:input="formatCurrencyInput($event, 'credit', coa)" :readonly="coa.saldo_normal == 'debit' || coa.saldo_normal == 'db'" :style="(coa.saldo_normal == 'debit' || coa.saldo_normal == 'db') ? 'background-color: #d1d5db; text-align: right; padding-right: 10px; border: 1px solid #ccc; border-radius: 4px;' : 'text-align: right; padding-right: 10px; border: 1px solid #ccc; border-radius: 4px;'">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </form>
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
                filterCategory(category) {
                    this.filter = category;
                    this.updateTotals();
                },
                filteredData() {
                    if (this.filter === 'all') {
                        return this.allData;
                    } else if (this.filter === 'neraca') {
                        return this.allData.filter(coa => ['1', '2', '3'].includes(coa.nomor_akun.charAt(0)));
                    } else if (this.filter === 'labarugi') {
                        return this.allData.filter(coa => ['4', '5', '6'].includes(coa.nomor_akun.charAt(0)));
                    }
                },
                updateTotals() {
                    const filteredData = this.filteredData();
                    const totalDebit = filteredData.reduce((acc, coa) => acc + parseFloat(coa.saldo_awal_debit || 0), 0);
                    const totalKredit = filteredData.reduce((acc, coa) => acc + parseFloat(coa.saldo_awal_credit || 0), 0);
                    this.totalSaldoAwalDebit = this.formatCurrency(totalDebit);
                    this.totalSaldoAwalKredit = this.formatCurrency(totalKredit);
                    this.selisihSaldoAwal = this.formatCurrency(totalDebit - totalKredit);
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
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/saldo-awal');
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            this.allData = data.map(coa => ({
                                ...coa,
                                formatted_saldo_awal_debit: this.formatCurrency(coa.saldo_awal_debit),
                                formatted_saldo_awal_credit: this.formatCurrency(coa.saldo_awal_credit)
                            }));
                            this.updateTotals();
                        } else {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format. Please check the data returned from the server.');
                        }
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
                },
                formatCurrency(value) {
                    let parsedValue = parseFloat(value.toString().replace(/\./g, '').replace(/,/g, '.'));
                    if (isNaN(parsedValue)) {
                        return '';
                    }
                    return parsedValue.toLocaleString('id-ID');
                },
                formatCurrencyInput(event, type, coa) {
                    let value = event.target.value.replace(/[^0-9,-]/g, '').replace(/\./g, '').replace(/,/g, '.');
                    value = parseFloat(value);
                    if (isNaN(value)) {
                        value = 0;
                    }
                    value = value.toLocaleString('id-ID');
                    event.target.value = value;
                    if (type === 'debit') {
                        coa.saldo_awal_debit = parseFloat(value.replace(/[^0-9,-]/g, '').replace(/\./g, '').replace(/,/g, '.'));
                        coa.formatted_saldo_awal_debit = value;
                    } else {
                        coa.saldo_awal_credit = parseFloat(value.replace(/[^0-9,-]/g, '').replace(/\./g, '').replace(/,/g, '.'));
                        coa.formatted_saldo_awal_credit = value;
                    }
                    this.changedData[coa.id] = coa;
                    this.updateTotals();
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/trial_hisabuna/backend/resources/views/saldo-awal/index.blade.php ENDPATH**/ ?>