<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar (Ledger)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 5px;
            background-color: #fff;
        }

        h2, h3 {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        th, td {
            padding: 3px;
            text-align: left;
            font-size: 0.875rem;
            word-wrap: break-word;
        }

        th {
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .coa-title {
            font-size: 1.5em;
            font-weight: 600;
        }

        .new-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            position: relative;
            text-align: center;
        }

        .company-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 100px;
            height: auto;
        }

        .header-content {
            width: calc(100% - 120px);
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body onload="window.print()">
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header-content">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>LAPORAN BUKU BESAR</h2>
            <h3>Periode: <?php echo e($tanggalMulai); ?> s/d <?php echo e($tanggalSelesai); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <div class="report-container">
        <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <table>
                <caption style="text-align: left; background-color: #f0f0f0; padding: 3px;"><h2><?php echo e(formatNomorAkun($coaAkun) . ' - ' . $transactions->first()->coa->nama_akun); ?></h2></caption>
                <thead>
                    <tr>
                        <th scope="col" style="width: 9%;">Tanggal</th>
                        <th scope="col" style="width: 46%;">Keterangan</th>
                        <th scope="col" class="text-right" style="width: 15%;">Debit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'debit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                        <th scope="col" class="text-right" style="width: 15%;">Kredit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'credit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                        <th scope="col" class="text-right" style="width: 15%;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/y')); ?></td>
                        <td>Saldo per tanggal</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right"><?php echo e(number_format($transactions->saldo_per_tanggal, 0, ',', '.')); ?></td>
                    </tr>
                    <?php
                        $previousDate = null;
                    ?>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php if($previousDate != \Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')): ?>
                                    <?php echo e(\Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')); ?>

                                    <?php
                                        $previousDate = \Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y');
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $keterangan = $transaction->keterangan;
                                    $max_length = 50;
                                    $output = '';
                                    while (strlen($keterangan) > $max_length) {
                                        $output .= substr($keterangan, 0, $max_length) . '<br>';
                                        $keterangan = substr($keterangan, $max_length);
                                    }
                                    $output .= $keterangan;
                                    echo $output;
                                ?>
                            </td>
                            <td class="text-right"><?php echo e(number_format($transaction->debit, 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($transaction->credit, 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($transaction->saldo, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/bukubesar_download.blade.php ENDPATH**/ ?>