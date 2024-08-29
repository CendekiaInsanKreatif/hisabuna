<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
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
        }

        .text-left {
            text-align: left;
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
    </style>
</head>

<body onload="window.print()">
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Neraca</h2>
            <h3>Neraca Per: <?php echo e($periode); ?></h3>
        </div>
    </header>
    
    <hr style="border: 2px solid black; width: 100%;">
    <table style="border-spacing: 16px 4px;">
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
                        <td colspan="<?php echo e(count($data) + 1); ?>">
                            <h3><?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                        </td>
                    </tr>
                    <?php
                        $totalLevel1 = array_fill_keys(array_keys($data), 0);
                    ?>
                    <?php $__currentLoopData = $level1Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2 => $level2Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_array($level2Data)): ?>
                            <tr>
                                <td colspan="<?php echo e(count($data) + 1); ?>">
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level2); ?></h3>
                                </td>
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
                                        <td style="text-align: right; width: 1%; padding: 2px; ">
                                            <?php echo e(number_format($amount, 0, ',', '.')); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah <?php echo e($level2); ?></h3>
                                </td>
                                <?php $__currentLoopData = $totalLevel2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td
                                        style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                                        <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <h3>JUMLAH <?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                        </td>
                        <?php $__currentLoopData = $totalLevel1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td
                                style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                                <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <br>
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