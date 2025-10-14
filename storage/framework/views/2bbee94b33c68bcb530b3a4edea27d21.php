<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan <?php echo e($label); ?></title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    <script src="<?php echo e(asset('js/paged_old.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url('<?php echo e(asset('css/rpt.css')); ?>');
    </style>
    <script>
        setTimeout(() => {
            window.print()
        }, 5000);
    </script>
</head>

<body>
    <?php echo $__env->make('report.partials.header', [
        'reportTitle' => 'Laporan Posisi Keuangan',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table style="border-spacing: 6px 2px;">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <?php $__currentLoopData = array_keys($data); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th style="text-align: right; font-size: 18px; width: 115px;"><?php echo e($year); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data[array_key_first($data)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level1 => $level1Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($level1Data)): ?>
                    <tr>
                        <td colspan="<?php echo e(count($data) + 1); ?>">
                            <h3><?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                        </td>
                    </tr>
                    <?php $totalLevel1 = array_fill_keys(array_keys($data), 0); ?> <?php $__currentLoopData = $level1Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2 => $level2Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_array($level2Data)): ?>
                            <tr>
                                <td colspan="<?php echo e(count($data) + 1); ?>">
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level2); ?></h3>
                                </td>
                            </tr>
                            <?php $totalLevel2 = array_fill_keys(array_keys($data), 0); ?> <?php $__currentLoopData = $level2Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level3 => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    
                                    <?php if($level3 == 'Saldo Tahun Berjalan'): ?>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level3); ?>

                                        </td>
                                    <?php else: ?>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level3); ?>

                                        </td>
                                    <?php endif; ?>
                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $yearData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $amount = $yearData[$level1][$level2][$level3] ?? 0;
                                            $totalLevel2[$year] += $amount;
                                            $totalLevel1[$year] += $amount;
                                        ?>
                                        <td style="text-align: right;">
                                            <?php echo e(number_format($amount, 0, ',', '.')); ?>

                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah <?php echo e(ucwords(strtolower($level2))); ?></h3>
                                </td>
                                <?php $__currentLoopData = $totalLevel2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td
                                        style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                                        <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <h3>JUMLAH <?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                        </td>
                        <?php $__currentLoopData = $totalLevel1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td
                                style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                                <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <br />
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php echo $__env->make('report.partials.footer', [
        'location' => $paged['alamat'],
        'date' => $paged['tanggal'],
        'preparedBy' => $paged['dibuat'],
        'position' => $paged['jabatan'],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>





<?php /**PATH /var/www/hisabuna/backend/resources/views/report/neraca.blade.php ENDPATH**/ ?>