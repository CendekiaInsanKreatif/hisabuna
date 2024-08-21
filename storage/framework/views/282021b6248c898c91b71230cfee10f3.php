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
            border-collapse: collapse;
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

        .footer.content {
            display: flex;
            align-items: center;
        }

        .company-logo {
            width: 5rem;
            height: 5rem;
            margin-right: 8rem;
        }

        .company-name {
            font-size: 1.25rem; /* Ukuran font yang sesuai */
            position: relative;
            top: -1.5rem; /* Sesuaikan nilai ini sesuai kebutuhan */
        }
    </style>
</head>
<body>
    <div class="footer content">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Company Logo" class="company-logo">
        <span class="company-name"><?php echo e(auth()->user()->company_name); ?></span>
    </div>
    <h2><u>Arus Kas</u></h2>
    <p>Periode: <?php echo e($start_date); ?> - <?php echo e($end_date); ?></p>

    <?php
        $totalKas = 0;
    ?>

    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($kategori != 'Total'): ?>
            <div class="section-title">Arus Kas Dari <?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></div>
            <table>
                <tbody>
                    <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nama_akun => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($nama_akun != 'Total'): ?>
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
                <td>Kenaikan (Penurunan) Kas dan Setara Kas</td>
                <td style="text-align: right;"><?php echo e(number_format($data['Total']['Kenaikan (Penurunan) Kas dan Setara Kas'], 0)); ?></td>
            </tr>
            <tr class="total-row">
                <td>Kas dan Setara Kas Awal</td>
                <td style="text-align: right;"><?php echo e(number_format($data['Total']['Kas dan Setara Kas Awal'], 0)); ?></td>
            </tr>
            <tr class="total-row">
                <td>Kas dan Setara Kas Akhir</td>
                <td style="text-align: right;"><?php echo e(number_format($data['Total']['Kas dan Setara Kas Akhir'], 0)); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/aruskas.blade.php ENDPATH**/ ?>