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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            margin: 0;
            padding: 0;
        }

        /* .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 50px;
            height: 50px;
        }

        .header h1 {
            font-size: 1.5em;
            margin: 0;
        }

        .header h3 {
            font-size: 1.25em;
            font-weight: bold;
            color: #101111;
            margin: 0;
            padding: 0;
        }

        .header p {
            margin: 0;
        } */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 3px;
            text-align: left;
            font-size: 0.875rem;
            /* color: #718096; */
        }

        th {
            /* background-color: #f7fafc; */
            text-transform: uppercase;
        }

        tbody tr:nth-child(odd) {
            /* background-color: #f9f9f9; */
        }

        tbody tr:nth-child(even) {
            /* background-color: #ffffff; */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .coa-title {
            font-size: 1.25em;
            font-weight: 600;
            /* color: #4a5568; */
            margin-top: 7px;
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
<body>
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Buku Besar</h2>
            <h3>Periode: <?php echo e($tanggalMulai); ?> s/d <?php echo e($tanggalSelesai); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <div class="report-container">
        <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h2 class="coa-title"><?php echo e(formatNomorAkun($coaAkun)); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="text-right">Debit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'debit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                        <th scope="col" class="text-right">Kredit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'credit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                        <th scope="col" class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/Y')); ?></td>
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
</html><?php /**PATH /var/www/hisabuna/resources/views/report/bukubesar_download.blade.php ENDPATH**/ ?>