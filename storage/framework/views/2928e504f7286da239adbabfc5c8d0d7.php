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
            font-size: 12px;
            max-width: 800px;
            margin: 0 auto;
            counter-reset: table-counter;
        }

        .report-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 5px;
            background-color: #fff;
        }

        h2 {
            margin: 0;
            padding: 0;
        }

        h3 {
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
            /* font-size: 0.875rem; */
            word-wrap: break-word;
            overflow-wrap: break-word;
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
            position: relative;
            text-align: center;
        }

        .company-logo {
            position: absolute;
            top: 5%;
            left: 0;
            transform: translateY(-50%);
            width: 100px;
        }

        .header-content {
            width: calc(100% - 120px);
            margin-left: auto;
            margin-right: auto;
        }

        @page {
            size: A4;
            margin: 30px;
            padding: 0;
            margin-bottom: 40px;
        }
    </style>
    <script>
        setTimeout(() => {
                window.print()
            }, 5000);
    </script>
</head>
<body>
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header-content">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Buku Besar (Ledger)</h2>
            <h3>Periode <?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y')); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    
        <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <table style="width: 100%">
                    <caption style="text-align: left;"><h2><?php echo e(formatNomorAkun($coaAkun) . ' - ' . $transactions->first()->coa->nama_akun); ?></h2></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="width: 13%;">Tanggal</th>
                            <th scope="col" style="width: 13%;">Jurnal</th>
                            <th scope="col" style="width: 13%;">No.Urut Transaksi</th>
                            <th scope="col" style="width: 37%;">Keterangan</th>
                            <th scope="col" class="text-right" style="width: 15%;">Debit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'debit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                            <th scope="col" class="text-right" style="width: 15%;">Kredit<div style="color: #e53e3e; font-size: 0.75em;"><?php echo e($transactions->first()->coa->saldo_normal == 'credit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                            <th scope="col" class="text-right" style="width: 15%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/y')); ?></td>
                            <td class="text-right" style="text-align: left;">-</td>
                            <td class="text-right" style="text-align: left;">-</td>
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
                                <td style="vertical-align: top;">
                                    <?php if($previousDate != \Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')): ?>
                                        <?php echo e(\Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y')); ?>

                                        <?php
                                            $previousDate = \Carbon\Carbon::parse($transaction->tanggal_bukti)->format('d/m/y');
                                        ?>
                                    <?php endif; ?>
                                </td>
                                <td style="text-wrap: break-word; text-align: justify; overflow-wrap: break-word; vertical-align: top;">
                                    <?php
                                        echo $transaction->jenis;
                                    ?>
                                </td>
                                <td style="text-wrap: break-word; text-align: justify; overflow-wrap: break-word; vertical-align: top;">
                                    <?php
                                        echo $transaction->no_urut_transaksi;
                                    ?>
                                </td>
                                <td style="text-wrap: break-word; text-align: justify; overflow-wrap: break-word; vertical-align: top;">
                                    <?php
                                        echo $transaction->keterangan;
                                    ?>
                                </td>
                                <td class="text-right" style="vertical-align: top;"><?php echo e(number_format($transaction->debit, 0, ',', '.')); ?></td>
                                <td class="text-right" style="vertical-align: top;"><?php echo e(number_format($transaction->credit, 0, ',', '.')); ?></td>
                                <td class="text-right" style="vertical-align: top;"><?php echo e(number_format($transaction->saldo, 0, ',', '.')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <div class="count"></div>
                </table>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
</body>
</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/bukubesar_download.blade.php ENDPATH**/ ?>