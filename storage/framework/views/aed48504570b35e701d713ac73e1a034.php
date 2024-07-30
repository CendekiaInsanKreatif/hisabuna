<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            background-color: #fff;
        }
        th, td {
            padding: 2px;
            text-align: left;
        }
        th {
            text-align: center;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 24px;
            margin: 5px 0;
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
    <table>
        
        <div class="footer content">
            <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Company Logo" class="company-logo">
            <span class="company-name"><?php echo e(auth()->user()->company_name); ?></span>
        </div>
        <h2><u>Neraca Saldo</u></h2>
        <tr>
            <th>Keterangan</th>
            <th>Saldo</th>
        </tr>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainCategory => $subcategories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td colspan="2"><strong><?php echo e($mainCategory); ?></strong></td>
            </tr>
            <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory => $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($subcategory !== 'Total'): ?>
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account => $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($account !== 'Total'): ?>
                            <tr>
                                <td style="padding-left: 30px;"><?php echo e($account); ?></td>
                                <td style="text-align: right"><?php echo e(number_format($balance, 0)); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="padding-left: 30px;"><strong>Total <?php echo e($subcategory); ?></strong></td>
                        <td style="text-align: right"><strong><?php echo e(number_format($accounts['Total'], 0)); ?></strong></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><strong>Total <?php echo e($mainCategory); ?></strong></td>
                <td style="text-align: right"><strong><?php echo e(number_format($subcategories['Total'], 0)); ?></strong></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/neraca_saldo.blade.php ENDPATH**/ ?>