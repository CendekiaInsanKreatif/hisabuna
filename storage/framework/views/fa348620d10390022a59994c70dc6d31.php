<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arus Kas</title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    
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
        'reportTitle' => 'Laporan Arus Kas',
        'reportPeriod' => 'Periode ' . $start_date . ' s/d ' . $end_date,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $totalKas = 0;
    ?>
    <?php $__currentLoopData = $dataChunked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($kategori != 'Total'): ?>
                <div class="section-title" style="font-size: 12px; font-weight: bold;">Arus Kas Dari
                    <?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></div>
                <table>
                    <tbody>
                        <?php
                            $kasbersih = 0;
                        ?>
                        <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nama_akun => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($nama_akun != 'Total'): ?>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;
                                        <?php if($nilai > 0): ?>
                                            Kenaikan (Penurunan)
                                        <?php else: ?>
                                            Penurunan (Kenaikan)
                                        <?php endif; ?>
                                        <?php echo e($nama_akun); ?>

                                    </td>
                                    <td style="text-align: right; font-size: 12px; width: 20%;">
                                        <?php if($nilai < 0): ?>
                                            (<?php echo e(number_format($nilai, 0, ',', '.')); ?>)
                                        <?php else: ?>
                                            <?php echo e(number_format($nilai, 0, ',', '.')); ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                    $kasbersih += $nilai;
                                ?>
                            <?php endif; ?>
                            <?php if($nama_akun == 'Total'): ?>
                                <tr>
                                    <td style="font-weight: bold; font-size: 12px;">Kas Bersih dari
                                        <?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></td>
                                    <td
                                        style="text-align: right; border-top: 1.5px solid black; font-weight: bold; font-size: 12px; width: 20%; margin-left: 20px;">
                                        <?php echo e(number_format($kasbersih, 0, ',', '.')); ?>

                                    </td>
                                </tr>
                                <div style="height: 10px;"></div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php
                    $totalKas += $item['Total'];
                ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <table>
            <tbody>
                <tr class="total-row">
                    <td style="font-weight: bold; font-size: 12px;">Kenaikan (Penurunan) Kas dan Setara Kas</td>
                    <td style="text-align: right; font-weight: bold; font-size: 12px; width: 20%;">
                        <?php echo e(number_format($data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'], 0, ',', '.')); ?></td>
                </tr>
                <tr class="total-row">
                    <td>Kas dan Setara Kas Awal</td>
                    <td style="text-align: right; border-bottom: 1.5px solid black; width: 20%;">
                        <?php echo e(number_format($data['Total']['Kas dan Setara Kas Awal'], 0, ',', '.')); ?></td>
                </tr>
                <tr class="total-row">
                    <td style="font-weight: bold; font-size: 12px;">Kas dan Setara Kas Akhir</td>
                    <td style="text-align: right; font-weight: bold; font-size: 12px width: 20%;">
                        <?php echo e(number_format($data['Total']['Kas dan Setara Kas Akhir'], 0, ',', '.')); ?></td>
                </tr>
            </tbody>
        </table>

        <?php echo $__env->make('report.partials.footer', [
            'location' => $paged['alamat'],
            'date' => $paged['tanggal'],
            'preparedBy' => $paged['dibuat'],
            'position' => $paged['jabatan'],
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>

</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/aruskas.blade.php ENDPATH**/ ?>