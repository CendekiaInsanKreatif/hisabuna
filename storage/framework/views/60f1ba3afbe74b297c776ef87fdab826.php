<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            background-color: #e8e8e8;
        }
        h2, h1 {
            text-align: center;
            margin-top: 20px;
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
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
    </div>
    <h1><?php echo e(auth()->user()->company_name); ?></h1>
    <h2><u>LAPORAN PERUBAHAN EKUITAS</u></h2>
    <table>
        <caption style="text-align: center; font-size: 14px;">Periode : <?php echo e($tanggal_mulai); ?> s/d <?php echo e($tanggal_selesai); ?></caption>
        <br>
        <thead style="border: 1px solid black;">
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right;"><?php echo e(date('Y')); ?></th>
                <th style="text-align: center;">Penambahan / (Pengurangan)</th>
                <th style="text-align: right;"><?php echo e(date('Y') - 1); ?></th>
            </tr>
        </thead>
        <tbody style="border: 1px solid black;">
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($year == date('Y')): ?>
                    <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-bottom: 1px solid black;">
                            <td><?php echo e($key); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($value - ($data[date('Y') - 1][$key] ?? 0))); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($data[date('Y') - 1][$key] ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/perubahanekuitas.blade.php ENDPATH**/ ?>