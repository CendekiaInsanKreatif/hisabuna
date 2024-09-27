<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr class="border-b">
        <td class="py-2 px-4 flex items-center">
            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: <?php echo e($index); ?> })">
                Pilih
            </button>
            <input type="text" name="no_akun[<?php echo e($index); ?>]" readonly required class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 mr-2" value="<?php echo e($row['no_akun']); ?>">
            <input type="text" name="nama_akun[<?php echo e($index); ?>]" readonly required class="w-full px-2 py-1 rounded-lg shadow-sm bg-gray-200 border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" value="<?php echo e($row['nama_akun']); ?>">
        </td>
        <td class="py-2 px-4">
                                                    x-text="<?php echo e($item['name'] == 'nomor_akun' ? 'formatNomorAkun(' . json_encode($item2[$item['name']]) . ')' : json_encode($item2[$item['name']])); ?>"></span>
            <input type="text" name="debit[<?php echo e($index); ?>]" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" value="<?php echo e($row['debit']); ?>">
        </td>
        <td class="py-2 px-4">
            <input type="text" name="kredit[<?php echo e($index); ?>]" class="w-full px-2 py-1 mb-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50" value="<?php echo e($row['kredit']); ?>">
        </td>
        <td class="py-2 px-4">
            <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong mr-2" x-on:click="rows.splice(<?php echo e($index); ?>, 1)">
                Hapus
            </button>
        </td>
    </tr>
    <tr>
        <td class="py-1 px-2" colspan="2">
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan<span class="text-red-500">*</span></label>
            <textarea name="keterangan[<?php echo e($index); ?>]" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50"><?php echo e($row['keterangan']); ?></textarea>
        </td>
        <td class="py-1 px-2">
            <label class="block text-sm font-medium text-gray-700">Tanggal Bukti<span class="text-red-500">*</span></label>
            <input type="text" name="tanggal_bukti[<?php echo e($index); ?>]" class="w-full px-2 py-1 rounded-lg shadow-sm border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 datepicker" value="<?php echo e($row['tanggal_bukti']); ?>" readonly>
        </td>
        <td class="py-1 px-1">
            <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
            <input style="width: 85px;" type="file" id="lampiran" name="lampiran[<?php echo e($index); ?>]" accept=".pdf,.jpg,.png,.jpeg" class="file:bg-emerald-500 file:border-none file:rounded-md file:px-2 file:py-1 file:text-sm file:font-semibold file:text-white file:tracking-widest hover:file:bg-emerald-700" multiple>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH /var/www/trial_hisabuna/backend/resources/views/jurnal/partials/jurnal_rows.blade.php ENDPATH**/ ?>