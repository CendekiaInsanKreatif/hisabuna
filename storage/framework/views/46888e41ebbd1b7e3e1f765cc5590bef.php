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
    <?php
        $field = ['no_transaksi', 'jenis', 'keterangan'];
        $fieldSelect = [
                [
                    'name' => 'nomor_akun',
                    'type' => 'text',
                    'label' => 'Nomer Akun',
                    'required' => true,
                ],
                [
                    'name' => 'nama_akun',
                    'type' => 'text',
                    'label' => 'Nama Akun',
                    'required' => true,
                ],
            ];

            $currentRoute = request()->route()->getName();
    ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fieldSelect,'data' => $coa,'maxWidth' => 'lg','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldSelect),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($coa),'maxWidth' => 'lg','focusable' => true]); ?>
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
    <?php if($currentRoute == 'jurnal.edit'): ?>
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center w-full mt-6">
                <form id="importForm" class="w-full" @submit.prevent="importJurnal">
                    <?php echo csrf_field(); ?>
                    <input type="file" id="importFile" name="file" accept=".xlsx" required
                        class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                    <button type="submit"
                        class="inline-flex items-center bg-emerald-500 justify-center px-2 py-1 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Import Jurnal
                    </button>
                </form>
                <form action="<?php echo e(route('jurnal.sample.export')); ?>" method="post" class="w-full text-right">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-2 py-1 bg-emerald-300 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Download Sample
                    </button>
                </form>
            </div>
            <form action="<?php echo e(route('jurnal.update', $jurnal->id)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
            <div class="mb-6 flex justify-between items-center">
                <p class="text-2xl font-semibold text-emerald-500">Edit Jurnal</p>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Simpan Jurnal
                    </button>

            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    <?php $__currentLoopData = $field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-span-1">
                        <label for="<?php echo e($item); ?>" class="block text-sm font-medium text-gray-700"><?php echo e(ucwords(str_replace('_', ' ', $item))); ?></label>
                        <?php if($item == 'keterangan'): ?>
                            <textarea name="<?php echo e($item); ?>_header" id="<?php echo e($item); ?>" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"><?php echo e($jurnal->$item); ?></textarea>
                        <?php elseif($item == 'jenis'): ?>
                            <select name="<?php echo e($item); ?>" id="<?php echo e($item); ?>" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <option value="">Pilih Jenis</option>
                                <option value="rv" <?php echo e($jurnal->$item == 'RV' ? 'selected' : ''); ?>>Voucher Penerimaan | RV</option>
                                <option value="pv" <?php echo e($jurnal->$item == 'PV' ? 'selected' : ''); ?>>Voucher Pembayaran | PV</option>
                                <option value="jv" <?php echo e($jurnal->$item == 'JV' ? 'selected' : ''); ?>>Voucher Jurnal | JV</option>
                            </select>
                        <?php elseif($item == 'no_transaksi'): ?>
                            <input type="text" name="<?php echo e($item); ?>" id="<?php echo e($item); ?>" readonly value="<?php echo e($jurnal->$item); ?>" placeholder="Generate By System" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="card-body overflow-x-auto mt-1 bg-white shadow-md rounded-lg p-1">
                    <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                        <thead class="text-xs text-gray-700 uppercase text-center bg-gray-200">
                            <tr>
                                <th class="py-2 px-4">Akun</th>
                                <th class="py-2 px-4">Debit</th>
                                <th class="py-2 px-4">Kredit</th>
                                <th class="py-2 px-4">Keterangan</th>
                                <th class="py-2 px-4 text-right">
                                    <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="rows.push({ no_akun: '', nama_akun: '', debit: '', kredit: '', keterangan: '' })">
                                        Tambah
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100 text-center" id="tBody">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="border-b">
                                    <td class="py-2 px-4 flex items-center">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                            Pilih
                                        </button>
                                        <input type="text" :name="'no_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" x-model="row.coa_akun">
                                        <input type="text" :name="'nama_akun[' + index + ']'" readonly class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.coa.nama_akun">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.credit">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'keterangan[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.keterangan">
                                    </td>
                                    <td class="py-2 px-4">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(index, 1)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr class="border-b bg-gray-300" id="tRowX">
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div x-data="jurnalApp()">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center w-full mt-6">
                <form id="importForm" class="w-full" @submit.prevent="importJurnal">
                    <?php echo csrf_field(); ?>
                    <input type="file" id="importFile" name="file" accept=".xlsx" required
                        class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700">
                    <button type="submit"
                        class="inline-flex items-center bg-emerald-500 justify-center px-2 py-1 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Import Jurnal
                    </button>
                </form>
                <form action="<?php echo e(route('jurnal.sample.export')); ?>" method="post" class="w-full text-right">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-2 py-1 bg-emerald-300 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Download Sample
                    </button>
                </form>
            </div>
            <form action="<?php echo e(route('jurnal.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
            <div class="mb-6 flex justify-between items-center">
                <p class="text-2xl font-semibold text-emerald-500">Buat Jurnal</p>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong">
                        Simpan Jurnal
                    </button>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    <?php $__currentLoopData = $field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-span-1">
                        <label for="<?php echo e($item); ?>" class="block text-sm font-medium text-gray-700"><?php echo e(ucwords(str_replace('_', ' ', $item))); ?></label>
                        <?php if($item == 'keterangan'): ?>
                            <textarea name="<?php echo e($item); ?>_header" id="<?php echo e($item); ?>" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"></textarea>
                        <?php elseif($item == 'jenis'): ?>
                            <select name="<?php echo e($item); ?>" id="<?php echo e($item); ?>" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                                <option value="">Pilih Jenis</option>
                                <option value="rv">Voucher Penerimaan | RV</option>
                                <option value="pv">Voucher Pembayaran | PV</option>
                                <option value="jv">Voucher Jurnal | JV</option>
                            </select>
                        <?php elseif($item == 'no_transaksi'): ?>
                            <input type="text" name="<?php echo e($item); ?>" id="<?php echo e($item); ?>" readonly placeholder="Generate By System" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="card-body overflow-x-auto mt-1 bg-white shadow-md rounded-lg p-1">
                    <table class="w-full min-w-full text-sm text-left text-gray-700" id="jurnalDetail">
                        <thead class="text-xs text-gray-700 uppercase text-center bg-gray-200">
                            <tr>
                                <th class="py-2 px-4">Akun</th>
                                <th class="py-2 px-4">Debit</th>
                                <th class="py-2 px-4">Kredit</th>
                                <th class="py-2 px-4">Keterangan</th>
                                <th class="py-2 px-4 text-right">
                                    <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click="addRow">
                                        Tambah
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100 text-center" id="tBody">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="border-b">
                                    <td class="py-2 px-4 flex items-center">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: index })">
                                            Pilih
                                        </button>
                                        <input type="text" :name="'no_akun[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" x-model="row.no_akun">
                                        <input type="text" :name="'nama_akun[' + index + ']'" class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.nama_akun">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'debit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.debit" x-on:input="formatCurrency($event, 'debit', index)">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'kredit[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.kredit" x-on:input="formatCurrency($event, 'kredit', index)">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" :name="'keterangan[' + index + ']'" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" x-model="row.keterangan">
                                    </td>
                                    <td class="py-2 px-4">
                                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(index, 1)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr class="border-b bg-gray-300" id="tRowX"></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script>
    function jurnalApp() {
        let jurnal = <?php echo json_encode($jurnal->details ?? [], 15, 512) ?>;
        // jurnal = jurnal.map(detail => ({
        //     no_akun: detail.coa_akun,
        //     nama_akun: detail.coa.nama_akun,
        //     debit: detail.debit,
        //     kredit: detail.credit,
        //     keterangan: detail.keterangan
        // }));

        console.log(jurnal)

        return {
            rows: jurnal ?? [],
            addRow() {
                this.rows.push({ no_akun: '', nama_akun: '', debit: '', kredit: '', keterangan: '' });
            },
            formatCurrency(event, field, index) {
                const value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
                const formattedValue = parseFloat(value).toLocaleString('id-ID');
                this.rows[index][field] = formattedValue;
            },
            importJurnal(event) {
                let formData = new FormData(document.getElementById('importForm'));
                fetch('<?php echo e(route('jurnal.import')); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.rows.forEach(row => {
                            this.rows.push(row);
                        });
                    } else {
                        console.error('Failed to import:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        };
    }
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
<?php /**PATH C:\dedeProject\Development\hisabuna\resources\views/jurnal/form.blade.php ENDPATH**/ ?>