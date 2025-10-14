<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar (Ledger)</title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 100%;
            margin: 0 auto;
            counter-reset: table-counter;
        }

        .report-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 5px;
            background-color: #fff;
        }

        h1 {
            margin: 5px 0;
            padding: 0;
            font-size: 16px;
        }

        h2 {
            margin: 15px 0 5px 0;
            padding: 0;
            font-size: 14px;
            font-weight: bold;
        }

        h3 {
            margin: 3px 0;
            padding: 0;
            font-size: 12px;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        table caption {
            margin-bottom: 8px;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        th {
            text-transform: uppercase;
            font-weight: bold;
            background-color: #f5f5f5;
            font-size: 10px;
            border-bottom: 2px solid #333;
        }

        tbody tr:first-child {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        tbody tr:first-child td {
            border-top: 2px solid #333;
        }

        td {
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .new-header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            margin-bottom: 10px;
        }

        .company-logo {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .header-content {
            width: calc(100% - 100px);
            margin-left: auto;
            margin-right: auto;
        }

        .header-divider {
            border: none;
            border-top: 2px solid #333;
            margin: 10px 0 15px 0;
        }

        .saldo-normal-info {
            font-size: 9px;
            font-style: italic;
            font-weight: normal;
            margin-top: 2px;
        }

        @page {
            size: A4;
            margin: 20mm 15mm 25mm 15mm;
        }
    </style>
</head>

<body>
    <?php echo $__env->make('report.partials.header', [
        'reportTitle' => 'Buku Besar (Ledger)',
        'reportPeriod' =>
            'Periode ' .
            \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') .
            ' s/d ' .
            \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div>
            <table>
                <caption style="text-align: left;">
                    <h2><?php echo e(formatNomorAkun($coaAkun)); ?> - <?php echo e($transactions->first()->coa->nama_akun); ?></h2>
                </caption>
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%;">Tanggal</th>
                        <th scope="col" style="width: 10%;">Jurnal</th>
                        <th scope="col" style="width: 12%;">No. Transaksi</th>
                        <th scope="col" style="width: 35%;">Keterangan</th>
                        <th scope="col" class="text-right" style="width: 11%;">
                            Debit
                            <div class="saldo-normal-info">
                                <?php echo e($transactions->first()->coa->saldo_normal == 'debit' ? '(Bertambah)' : '(Berkurang)'); ?>

                            </div>
                        </th>
                        <th scope="col" class="text-right" style="width: 11%;">
                            Kredit
                            <div class="saldo-normal-info">
                                <?php echo e($transactions->first()->coa->saldo_normal == 'credit' ? '(Bertambah)' : '(Berkurang)'); ?>

                            </div>
                        </th>
                        <th scope="col" class="text-right" style="width: 11%;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/y')); ?></td>
                        <td>-</td>
                        <td>-</td>
                        <td><strong>Saldo Awal</strong></td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">
                            <strong><?php echo e(number_format($transactions->saldo_per_tanggal, 0, ',', '.')); ?></strong>
                        </td>
                    </tr>
                    <?php
                        $previousDate = null;
                    ?>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="vertical-align: top;">
                                <?php if($previousDate != \Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')): ?>
                                    <?php echo e(\Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')); ?>

                                    <?php
                                        $previousDate = \Carbon\Carbon::parse($transaction->tanggal_bukti)->format(
                                            'd/m/y',
                                        );
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: top;"><?php echo e($transaction->jenis); ?></td>
                            <td style="vertical-align: top;"><?php echo e($transaction->no_urut_transaksi); ?></td>
                            <td style="text-align: justify; vertical-align: top;"><?php echo e($transaction->keterangan); ?></td>
                            <td class="text-right" style="vertical-align: top;">
                                <?php echo e($transaction->debit ? number_format($transaction->debit, 0, ',', '.') : '-'); ?></td>
                            <td class="text-right" style="vertical-align: top;">
                                <?php echo e($transaction->credit ? number_format($transaction->credit, 0, ',', '.') : '-'); ?>

                            </td>
                            <td class="text-right" style="vertical-align: top;">
                                <?php echo e(number_format($transaction->saldo, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo $__env->make('report.partials.footer', [
        'location' => auth()->user()->company_address ?? 'Jakarta',
        'date' => \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
        'preparedBy' => auth()->user()->name ?? 'Dibuat Oleh',
        'position' => 'Direktur',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/bukubesar_download.blade.php ENDPATH**/ ?>