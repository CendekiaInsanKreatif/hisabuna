<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h3 {
            margin: 0;
            padding: 0;
        }
        h2 {
            margin: 0;
            padding: 0;
        }
        p {
            margin: 0;
            padding: 0;
        }
        .amount {
            float: right;
            text-align: right;
            margin-left: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Neraca</h2>
            <h4>Neraca Per: <?php echo e($periode); ?></h4>
        </div>
    </header>
    <table>
        <thead>
            <tr>
                <th>&nbsp;</th>
                <?php $__currentLoopData = array_keys($data); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th style="text-align: right; font-size: 18px;"><?php echo e($year); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data[array_key_first($data)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level1 => $level1Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($level1Data)): ?>
                    <tr>
                        <td colspan="<?php echo e(count($data) + 1); ?>"><h2><?php echo e($level1 == '1' ? 'Aset' : $level1); ?></h2></td>
                    </tr>
                    <?php
                        $totalLevel1 = array_fill_keys(array_keys($data), 0);
                    ?>
                    <?php $__currentLoopData = $level1Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2 => $level2Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_array($level2Data)): ?>
                            <tr>
                                <td colspan="<?php echo e(count($data) + 1); ?>"><h3>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level2); ?></h3></td>
                            </tr>
                            <?php
                                $totalLevel2 = array_fill_keys(array_keys($data), 0);
                            ?>
                            <?php $__currentLoopData = $level2Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level3 => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level3); ?></td>
                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $yearData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $amount = $yearData[$level1][$level2][$level3] ?? 0;
                                            $totalLevel2[$year] += $amount;
                                            $totalLevel1[$year] += $amount;
                                        ?>
                                        <td style="text-align: right;"><?php echo e(number_format($amount, 0, ',', '.')); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h3>&nbsp;&nbsp;&nbsp;&nbsp; Total <?php echo e($level2); ?></h3></td>
                                <?php $__currentLoopData = $totalLevel2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td style="text-align: right;"><h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><h2>Total <?php echo e($level1 == '1' ? 'Aset' : $level1); ?></h2></td>
                        <?php $__currentLoopData = $totalLevel1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td style="text-align: right;"><h2><?php echo e(number_format($total, 0, ',', '.')); ?></h2></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
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
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/neraca.blade.php ENDPATH**/ ?>