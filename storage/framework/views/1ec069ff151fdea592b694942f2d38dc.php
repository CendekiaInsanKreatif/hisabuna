<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    <title>Laporan Neraca Saldo</title>
    <script src="<?php echo e(asset('js/paged_old.js')); ?>"></script>
    <style>
        @import url('<?php echo e(asset('css/rpt.css')); ?>');

        table {
            width: 100%;
        }

        tr:last-child td {
            margin: 0;
            padding: 0;
        }
    </style>
    <script>
        setTimeout(() => {
            window.print()
        }, 5000);
    </script>
</head>

<body>
    <?php echo $__env->make('report.partials.header', [
        'reportTitle' => 'Neraca Saldo',
        'reportPeriod' =>
            'Per ' .
            \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->locale('id')->translatedFormat('d F Y'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalDebit = 0;
                $totalKredit = 0;
            ?>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun1 => $subcategories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td colspan="3"><strong><?php echo e($akun1); ?></strong></td>
                </tr>
                <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun2 => $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($akun2 !== 'Total'): ?>
                        <tr>
                            <td class="indent"><strong><?php echo e($akun2); ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $xCoa => $balances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($xCoa !== 'Total'): ?>
                                <tr>
                                    <td class="indent indent">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(formatNomorAkun($xCoa)); ?></span></td>
                                    <td style="text-align: right;"><?php echo e(number_format($balances['debit'], 0, ',', '.')); ?>

                                    </td>
                                    <td style="text-align: right;"><?php echo e(number_format($balances['kredit'], 0, ',', '.')); ?>

                                    </td>
                                </tr>
                                <?php
                                    $totalDebit += $balances['debit'];
                                    $totalKredit += $balances['kredit'];
                                ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="indent"><strong>Total <?php echo e(pisah($akun2)); ?></strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;">
                                <strong><?php echo e(number_format($accounts['Total']['debit'], 0, ',', '.')); ?></strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;">
                                <strong><?php echo e(number_format($accounts['Total']['kredit'], 0, ',', '.')); ?></strong></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong>Total <?php echo e(pisah($akun1)); ?></strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;">
                        <strong><?php echo e(number_format($subcategories['Total']['debit'], 0, ',', '.')); ?></strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;">
                        <strong><?php echo e(number_format($subcategories['Total']['kredit'], 0, ',', '.')); ?></strong></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><strong>Total Keseluruhan</strong></td>
                <td style="text-align: right; border-top: 2px solid black;">
                    <strong><?php echo e(number_format($totalDebit, 0, ',', '.')); ?></strong></td>
                <td style="text-align: right; border-top: 2px solid black;">
                    <strong><?php echo e(number_format($totalKredit, 0, ',', '.')); ?></strong></td>
            </tr>
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
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/neraca_saldo.blade.php ENDPATH**/ ?>