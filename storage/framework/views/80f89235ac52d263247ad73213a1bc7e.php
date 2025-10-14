<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    <style>
        @import url('<?php echo e(asset('css/rpt.css')); ?>');
    </style>
</head>
<body>
    <?php echo $__env->make('report.partials.header', [
        'reportTitle' => 'Laporan Perubahan Ekuitas',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 40%;">Keterangan</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;"><?php echo e($tahun); ?></th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;">Penambahan / <br> (Pengurangan)</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;"><?php echo e($tahun - 1); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalEkuitasTahunIni = 0;
                $totalEkuitasTahunLalu = 0;
            ?>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($year == $tahun): ?>
                    <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalEkuitasTahunIni += $value;
                            $totalEkuitasTahunLalu += $data[date('Y') - 1][$key] ?? 0;
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo e($key); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value - ($data[$tahun - 1][$key] ?? 0))); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($data[$tahun - 1][$key] ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total">
                <td style="text-align: left; font-weight: bold;">Total Ekuitas</td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;"><?php echo e(number_format($totalEkuitasTahunIni)); ?></td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;"><?php echo e(number_format($totalEkuitasTahunIni - $totalEkuitasTahunLalu)); ?></td>
                <td style="text-align: right; font-weight: bold; border-top: 1px solid #000;"><?php echo e(number_format($totalEkuitasTahunLalu)); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/perubahanekuitas.blade.php ENDPATH**/ ?>