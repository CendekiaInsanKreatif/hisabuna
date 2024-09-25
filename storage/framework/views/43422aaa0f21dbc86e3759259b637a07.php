<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            padding: 2px;
        }
        h2, h3 {
            margin: 0;
            padding: 0;
        }

        .footer.content {
            display: flex;
            align-items: center;
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

        @page {
            size: A4;
            margin: 30px;
            padding: 0;
            @bottom-center {
                content: counter(page);
            }
        }
    </style>
    <script src="<?php echo e(asset('js/paged_old.js')); ?>"></script>
</head>
<body onload="window.print()">
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Perubahan Ekuitas</h2>
            <h3>Per <?php echo e(\Carbon\Carbon::createFromFormat('d/m/Y', $tanggal_selesai)->format('d F Y')); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 40%;">Keterangan</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;"><?php echo e(date('Y')); ?></th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;">Penambahan / <br> (Pengurangan)</th>
                <th style="text-align: right; border-bottom: 1px solid #000; width: 20%;"><?php echo e(date('Y') - 1); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalEkuitasTahunIni = 0;
                $totalEkuitasTahunLalu = 0;
            ?>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($year == date('Y')): ?>
                    <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalEkuitasTahunIni += $value;
                            $totalEkuitasTahunLalu += $data[date('Y') - 1][$key] ?? 0;
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo e($key); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value - ($data[date('Y') - 1][$key] ?? 0))); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($data[date('Y') - 1][$key] ?? 0)); ?></td>
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
<?php /**PATH /var/www/trial_hisabuna/backend/resources/views/report/perubahanekuitas.blade.php ENDPATH**/ ?>