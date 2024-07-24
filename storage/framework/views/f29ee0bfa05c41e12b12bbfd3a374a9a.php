<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neraca Perbandingan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e8f4ff;
        }
        .indent-1 {
            padding-left: 20px;
        }
        .indent-2 {
            padding-left: 40px;
        }
        .total {
            font-weight: bold;
            background-color: #e8e8e8;
        }
    </style>
</head>
<body>
    <table>
        <caption><strong><h1>Neraca Perbandingan</h1></strong></caption>
        <caption><?php echo e(auth()->user()->company_name); ?></caption>
        <thead>
            <tr>
                <th style="text-align: center">Keterangan</th>
                <th style="text-align: center"><?php echo e($tahunSekarang); ?></th>
                <th style="text-align: center"><?php echo e($tahunSebelumnya); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data[$tahunSekarang]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keterangan => $subData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="total" style="margin-top: 10px;">
                    <td><strong><?php echo e($keterangan); ?></strong></td>
                    <td colspan="2"></td>
                </tr>
                <?php $__currentLoopData = $subData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_array($nilai)): ?>
                        <tr class="indent-1">
                            <td>&nbsp;&nbsp;&nbsp;<strong><?php echo e($kategori); ?></strong></td>
                            <td colspan="2"></td>
                        </tr>
                        <?php $__currentLoopData = $nilai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subkategori => $jumlah): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($subkategori != 'Total'): ?>
                                <tr class="indent-2">
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($subkategori); ?></td>
                                    <td style="text-align: right;"><?php echo e(number_format($jumlah, 0, ',', '.')); ?></td>
                                    <td style="text-align: right;"><?php echo e(number_format($data[$tahunSebelumnya][$keterangan][$kategori][$subkategori], 0, ',', '.')); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="total indent-1">
                            <td>Jumlah <?php echo e($kategori); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($nilai['Total'], 0, ',', '.')); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($data[$tahunSebelumnya][$keterangan][$kategori]['Total'], 0, ',', '.')); ?></td>
                        </tr>
                    <?php else: ?>
                        
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total">
                    <td>Jumlah <?php echo e($keterangan); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($subData['Total'], 0, ',', '.')); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($data[$tahunSebelumnya][$keterangan]['Total'], 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/neraca_perbandingan.blade.php ENDPATH**/ ?>