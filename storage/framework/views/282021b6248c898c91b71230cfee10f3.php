<!DOCTYPE html>
<html>
<head>
    <title>Arus Kas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
        }

        th, td {
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .section-title {
            font-weight: bold;
            margin-top: 5px;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Arus Kas</h1>
    <p>Periode: <?php echo e($start_date); ?> - <?php echo e($end_date); ?></p>

    <?php
        $totalKas = 0;
    ?>

    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="section-title">Arus Kas Dari <?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></div>
        <table>
            <tbody>
                <?php $__currentLoopData = $item['Detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nama_akun => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                        <?php if($nilai > 0): ?>
                            Kenaikan (Penurunan)
                        <?php else: ?>
                            Penurunan (Kenaikan)
                        <?php endif; ?>
                        <?php echo e($nama_akun); ?>

                        </td>
                        <td style="text-align: right;"><?php echo e(number_format($nilai, 0)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td>Total <?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($item['Jumlah'], 0)); ?></td>
                </tr>
            </tbody>
        </table>
        <?php
            $totalKas += $item['Jumlah'];
        ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="section-title">Kas dan Setara Kas Akhir</div>
    <table>
        <tbody>
            <tr class="total-row">
                <td>Total Kas dan Setara Kas Akhir</td>
                <td style="text-align: right;"><?php echo e(number_format($totalKas, 0)); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/aruskas.blade.php ENDPATH**/ ?>