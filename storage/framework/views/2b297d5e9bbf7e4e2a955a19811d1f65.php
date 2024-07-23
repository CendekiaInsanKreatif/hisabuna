<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
            font-size: 12px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 20px;
            background-color: #f9f9f9;
        }
        th, td {
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
<h2><u>LAPORAN LABA RUGI</u></h2>
<h4><?php echo e(auth()->user()->company_name); ?></h4>
<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<table>
        <h3 style="padding: 0; margin: 0;"><?php echo e($category); ?></h3>
        <?php $__currentLoopData = $details['Detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;<?php echo e($item); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($amount, 0, ',', '.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr class="total">
            <td>Total <?php echo e($category); ?></td>
            <td style="text-align: right;"><?php echo e(number_format($details['Jumlah'], 0, ',', '.')); ?></td>
        </tr>
    </table>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<table>
    <tr class="total">
        <td>Saldo Laba (Rugi) Tahun Berjalan</td>
        <td style="text-align: right;"><?php echo e(number_format($labaRugiBersih, 0, ',', '.')); ?></td>
    </tr>
</table>
<table style="width: 100%; margin-top: 70px; border-top: 1px solid black; padding-top: 20px;">
    <tr>
        <td style="text-align: center; width: 35%;">
            <div>Dibuat oleh, <?php echo e($ttd1); ?></div>
            <div style="height: 80px;"></div>
            <div><strong>Staff Keuangan</strong></div>
        </td>
        <td style="width: 10%;"></td>
        <td style="width: 10%;"></td>
        <td style="width: 10%;"></td>
        <td style="text-align: center; width: 35%;">
            <div>Disetujui oleh, <?php echo e($ttd2); ?></div>
            <div style="height: 80px;"></div>
            <div><strong>Manager Keuangan</strong></div>
        </td>
    </tr>
</table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/labarugi.blade.php ENDPATH**/ ?>