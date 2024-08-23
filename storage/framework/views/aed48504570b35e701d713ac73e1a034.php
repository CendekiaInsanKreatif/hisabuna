<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca Saldo</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
        }
        table { 
            width: 100%;
            margin-top: 20px; 
        }
        th, td {
            padding: 2px; 
            text-align: left; 
        }
        @media print {
            body {
                display: block;
            }
            .header {
                text-align: center;
                padding: 0;
            }
            .header h1, .header h2, .header h4 {
                margin: 0;
                font-size: inherit;
            }
            .header h1 {
                font-size: 2em;
            }
            .header h2 {
                font-size: 1.5em;
            }
            .header h4 {
                font-size: 1em;
            }
        }
        th { 
            background-color: #f2f2f2; 
        }
        h2, h1, h4 { 
            text-align: center; 
        }
        .indent { 
            padding-left: 20px; 
        }
        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: 0;
            font-size: 10px;
        }
        .footer .right::before {
            float: left;
            content: "Halaman " counter(page);
        }
        .footer .left {
            float: right;
        }
    </style>
</head>
<body>
    <header>
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" style="width: 160px; float: left; padding-right: 2rem">
        <div class="header">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Neraca Saldo</h2>
            <h4>Periode: <?php echo e($tanggal_mulai); ?> s/d <?php echo e($tanggal_selesai); ?></h4>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <thead>
            <tr>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun1 => $subcategories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td colspan="3"><strong><?php echo e($akun1); ?></strong></td>
                </tr>
                <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun2 => $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($akun2 !== 'Total'): ?>
                        <tr>
                            <td class="indent"><strong><?php echo e($akun2); ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $xCoa => $balances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($xCoa !== 'Total'): ?>
                                <tr>
                                    <td class="indent indent"><span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(formatNomorAkun($xCoa)); ?></span></td>
                                    <td style="text-align: right;"><?php echo e(number_format($balances['debit'], 0, ',', '.')); ?></td>
                                    <td style="text-align: right;"><?php echo e(number_format($balances['kredit'], 0, ',', '.')); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="indent"><strong>Total <?php echo e(substr($akun2, 4)); ?></strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;"><strong><?php echo e(number_format($accounts['Total']['debit'], 0, ',', '.')); ?></strong></td>
                            <td style="text-align: right; border-bottom: 1px solid black;"><strong><?php echo e(number_format($accounts['Total']['kredit'], 0, ',', '.')); ?></strong></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong>Total <?php echo e($akun1); ?></strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;"><strong><?php echo e(number_format($subcategories['Total']['debit'], 0, ',', '.')); ?></strong></td>
                    <td style="text-align: right; border-bottom: 2px solid black;"><strong><?php echo e(number_format($subcategories['Total']['kredit'], 0, ',', '.')); ?></strong></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="footer">
        <div class="left"><?php echo e(auth()->user()->company_name); ?></div>
        <div class="right"></div>
    </div>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/neraca_saldo.blade.php ENDPATH**/ ?>