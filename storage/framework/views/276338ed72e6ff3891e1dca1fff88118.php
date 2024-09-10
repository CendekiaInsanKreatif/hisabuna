<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Mutasi Saldo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 3px;
            font-size: 11px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #ccc;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .col-md-6 {
            flex: 0 0 50%;
        }
        .col-md-12 {
            flex: 0 0 100%;
        }
        h2 {
            margin: 0;
            padding: 0;
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
            <h2>Laporan Mutasi Saldo</h2>
            <h3>Mutasi Saldo: <?php echo e($tanggal_mulai); ?> s/d <?php echo e($tanggal_selesai); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">

    <div class="row">
        <div class="col-md-12">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Nomor Akun</th>
                        <th rowspan="2">Nama Akun</th>
                        <th colspan="2" class="text-center">Saldo Awal</th>
                        <th colspan="2" class="text-center">Mutasi</th>
                        <th colspan="2" class="text-center">Saldo Akhir</th>
                    </tr>
                    <tr>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $total_saldo_awal_debit = 0;
                        $total_saldo_awal_credit = 0;
                        $total_mutasi_debit = 0;
                        $total_mutasi_credit = 0;
                        $total_saldo_akhir_debit = 0;
                        $total_saldo_akhir_credit = 0;
                    ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $golongan => $golonganData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td colspan="8"><h2 style="text-align: left;"><?php echo e($golongan); ?></h2></td>
                    </tr>
                        <?php
                            $subtotal_saldo_awal_debit = 0;
                            $subtotal_saldo_awal_credit = 0;
                            $subtotal_mutasi_debit = 0;
                            $subtotal_mutasi_credit = 0;
                            $subtotal_saldo_akhir_debit = 0;
                            $subtotal_saldo_akhir_credit = 0;
                        ?>
                        <?php $__currentLoopData = $golonganData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nomor_akun => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(formatNomorAkun($nomor_akun)); ?></td>
                            <td><?php echo e($detail['nama_akun'] ?? '-'); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['saldo_awal']['debit'], 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['saldo_awal']['credit'], 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['mutasi']['debit'], 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['mutasi']['credit'], 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['saldo_akhir']['debit'], 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format($detail['saldo_akhir']['credit'], 0, ',', '.')); ?></td>
                        </tr>
                        <?php
                            $subtotal_saldo_awal_debit += $detail['saldo_awal']['debit'];
                            $subtotal_saldo_awal_credit += $detail['saldo_awal']['credit'];
                            $subtotal_mutasi_debit += $detail['mutasi']['debit'];
                            $subtotal_mutasi_credit += $detail['mutasi']['credit'];
                            $subtotal_saldo_akhir_debit += $detail['saldo_akhir']['debit'];
                            $subtotal_saldo_akhir_credit += $detail['saldo_akhir']['credit'];

                            $total_saldo_awal_debit += $detail['saldo_awal']['debit'];
                            $total_saldo_awal_credit += $detail['saldo_awal']['credit'];
                            $total_mutasi_debit += $detail['mutasi']['debit'];
                            $total_mutasi_credit += $detail['mutasi']['credit'];
                            $total_saldo_akhir_debit += $detail['saldo_akhir']['debit'];
                            $total_saldo_akhir_credit += $detail['saldo_akhir']['credit'];
                        ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-top: 2px solid black;">
                            <td colspan="2" class="text-right"><strong>Jumlah <?php echo e($golongan); ?> :</strong></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_saldo_awal_debit, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_saldo_awal_credit, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_mutasi_debit, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_mutasi_credit, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_saldo_akhir_debit, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($subtotal_saldo_akhir_credit, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-top: 2px solid black;">
                        <td colspan="2" class="text-right"><strong>Jumlah : </strong></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_saldo_awal_debit, 0, ',', '.')); ?></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_saldo_awal_credit, 0, ',', '.')); ?></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_mutasi_debit, 0, ',', '.')); ?></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_mutasi_credit, 0, ',', '.')); ?></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_saldo_akhir_debit, 0, ',', '.')); ?></td>
                        <td class="text-right" style="font-weight: bold;"><?php echo e(number_format($total_saldo_akhir_credit, 0, ',', '.')); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/mutasi_saldo.blade.php ENDPATH**/ ?>