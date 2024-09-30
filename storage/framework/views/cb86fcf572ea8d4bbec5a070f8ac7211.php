<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .report-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }


        table.main-data tr:nth-child(odd) {
            background-color: #f4f4f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-data tr td {
            padding: 4px 4px 4px 32px;
        }

        table.main-data tr:nth-last-child(2) td.data-num {
            border-bottom: solid 1px black;
        }

        .data-desc {
            width: 80%;
        }

        .data-num {
            text-align: right;
        }


        .total {
            font-size: 14px;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="report-container">
        <header style="border-bottom: 2px solid #ddd; padding: 10px 20px;">
            <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
            <div style="overflow: hidden;">
                <h1 style="text-align:center; font-size: 20px;"><?php echo e(auth()->user()->company_name); ?></h1>
                <p style="text-align:center;">Laporan Laba Rugi</p>
                <p style="text-align:center;">Periode <?php echo e($start); ?> - <?php echo e($end); ?></p>
            </div>
        </header>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table class="main-data">
            <h3 style="font-size: 20px; padding-bottom: 16px;"><?php echo e($category); ?></h3>
            <?php $__currentLoopData = $details['Detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="data-desc"><?php echo e($item); ?></td>
                    <td class="data-num"><?php echo e(number_format($amount, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total" style="border-bottom: 2px solid black;             background-color: #f4f4f5;">
                <td style="padding: 8px 4px 8px 12px;">Total <?php echo e($category); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($details['Jumlah'], 0, ',', '.')); ?></td>
            </tr>
        </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div style="padding: 24px 0 24px 0;border-bottom: 2px solid black">
            <table>
                <tr class="total" style=" font-size: 20px; ">
                    <td>Saldo Laba (Rugi) Tahun Berjalan</td>
                    <td style="text-align: right;"><?php echo e(number_format($labaRugiBersih, 0, ',', '.')); ?></td>
                </tr>
            </table>
        </div>
        <table style="width: 100%; margin-top: 70px;">
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
    </div>
</body>
</html><?php /**PATH /var/www/hisabuna/backend/resources/views/report/labarugiprint.blade.php ENDPATH**/ ?>