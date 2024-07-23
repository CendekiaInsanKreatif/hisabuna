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
    <form method="GET" action="<?php echo e(route('report.bukubesar')); ?>" class="mb-6">
        <div class="flex space-x-2 items-end mb-4">
            <div class="flex flex-col">
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai:</label>
                <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo e($tanggalMulai); ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai:</label>
                <input type="text" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo e($tanggalSelesai); ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="akun" class="block text-sm font-medium text-gray-700">Akun:</label>
                <input type="text" id="akun" name="akun" value="<?php echo e($akun); ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex items-end">
                <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'h-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10']); ?>Tampilkan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                <a href="#" id="download" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Download</a>
            </div>
        </div>
    </form>

    <div id="ledgerContent" class="w-full">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">BUKU BESAR (LEDGER)</h1>
            <h3 class="text-gray-700 mb-4">Periode <?php echo e($tanggalMulai); ?> s/d <?php echo e($tanggalSelesai); ?></h3>
        </div>
        <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h2 class="text-xl font-semibold text-gray-700"><?php echo e($coaAkun); ?></h2>
            <table class="w-full divide-y divide-gray-200 mt-4 mb-6 table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-1/5 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="w-2/5 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Debit<div class="text-red-500">Bertambah</div></th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Kredit<div class="text-red-500">Berkurang</div></th>
                        <th scope="col" class="w-1/6 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($transaction->jurnal_tgl)->format('d/m/Y')); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                    $keterangan = $transaction->keterangan;
                                    $max_length = 50;
                                    $output = '';
                                    while (strlen($keterangan) > $max_length) {
                                        $output .= substr($keterangan, 0, $max_length) . '<br>';
                                        $keterangan = substr($keterangan, $max_length);
                                    }
                                    $output .= $keterangan;
                                    echo $output;
                                ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right"><?php echo e(number_format($transaction->debit, 0, ',', '.')); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right"><?php echo e(number_format($transaction->credit, 0, ',', '.')); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right"><?php echo e(number_format($transaction->saldo, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('script'); ?>
    <script type="module">
        $(document).ready(function() {
            $('#tanggal_mulai').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) { 
                    var tanggalMulai = new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
                    $(this).datepicker('setDate', tanggalMulai);
                    $('#tanggal_selesai').datepicker('option', 'minDate', tanggalMulai);
                    $('#tanggal_selesai').datepicker('setDate', tanggalMulai);
                }
            });

            $('#tanggal_selesai').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                beforeShow: function(input, inst) {
                    var tanggalMulai = $('#tanggal_mulai').datepicker('getDate');
                    if (tanggalMulai) {
                        $(this).datepicker('option', 'minDate', tanggalMulai);
                    }
                }
            });

            document.getElementById('download').addEventListener('click', function(event) {
                event.preventDefault();
                var tanggalMulai = document.getElementById('tanggal_mulai').value;
                var tanggalSelesai = document.getElementById('tanggal_selesai').value;
                if (tanggalMulai) {
                    var url = "<?php echo e(route('report.bukubesar.download')); ?>" + "?tanggal_mulai=" + tanggalMulai + "&tanggal_selesai=" + tanggalSelesai;
                    window.location.href = url;
                } else {
                    alert("Harap pilih tanggal mulai.");
                }
            });
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

<?php /**PATH /var/www/hisabuna/resources/views/report/bukubesar.blade.php ENDPATH**/ ?>