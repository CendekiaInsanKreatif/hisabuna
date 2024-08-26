<!DOCTYPE html>
<html>
<head>
    <title>Arus Kas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 2px;
            text-align: left;
        }
        h3 {
            margin: 0;
            padding: 0;
        }

        h2 {
            margin: 0;
            padding: 0;
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

        .new-header {
            position: relative;
        }

        .company-logo {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100px;

        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .report-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .report-period {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body onload="window.print()">
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Arus Kas</h2>
            <h3>Periode: <?php echo e($start_date); ?> s/d <?php echo e($end_date); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">

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
                                <td>&nbsp;&nbsp;&nbsp;
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
<?php /**PATH C:\laragon\www\hisabuna\resources\views/report/aruskas.blade.php ENDPATH**/ ?>