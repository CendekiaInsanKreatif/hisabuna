<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Transaksi</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            padding: 20px;
        }
        .header h1, .header h2, .header h4 {
            margin: 0;
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 2px;
            text-align: left;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>PT. DIGITAL TRANSFORMASI INDONESIA</h1>
        <h4>Pusat Inovasi dan Transformasi Digital</h4>
        <br>
        <br>
        <h2><u>JURNAL TRANSAKSI</u></h2>
        <h4>(TRANSACTION JOURNAL)</h4>
    </header>
    <br>
    <table>
        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%;">Informasi Tambahan</td>
                <td style="width: 40%;">&nbsp;&nbsp;Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo e(\Carbon\Carbon::parse($jurnal['jurnal_tgl'])->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; vertical-align: top; width: 60%; height: 5px; border: 1px solid black;"><b><em><?php echo e($jurnal['keterangan']); ?></em></b></td>
                <td style="width: 40%; height: 5px;">&nbsp;&nbsp;Nomor Jurnal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo e($jurnal['jenis'].' - '.$jurnal['no_transaksi']); ?></td>
            </tr>
            <tr>
                <td style="width: 40%; height: 5px">&nbsp;&nbsp;Nomor Transaksi&nbsp;&nbsp;&nbsp;: <?php echo e($jurnal['id']); ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <thead style="border: 1px solid black;">
            <tr>
                <th style="height: 25px; text-align:center">Nomor Akun</th>
                <th style="height: 25px; text-align:center">Nama Akun</th>
                <th style="height: 25px; text-align:center">Subtotal</th>
                <th style="height: 25px; text-align:center">Debit</th>
                <th style="height: 25px; text-align:center">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalDebit = 0;
                $totalCredit = 0;
            ?>
            <?php $__currentLoopData = $jurnal['details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($index == 0 || $jurnal['details'][$index]['parent']['nomor_akun'] != $jurnal['details'][$index - 1]['parent']['nomor_akun']): ?>
                <tr style="border-top: 1px solid black">
                    <td style="font-weight: bold;"><?php echo e($detail['parent']['nomor_akun']); ?></td>
                    <td style="font-weight: bold;"><?php echo e($detail['parent']['nama_akun']); ?></td>
                    <td style="text-align: right;"><?php echo e($jurnal['subtotal'] ? number_format($jurnal['subtotal'], 0, ',', '.') : 0); ?></td>
                    <td style="text-align: right;"><?php echo e($jurnal['debit'] ? number_format($jurnal['debit'], 0, ',', '.') : 0); ?></td>
                    <td style="text-align: right;"><?php echo e($jurnal['credit'] ? number_format($jurnal['credit'], 0, ',', '.') : 0); ?></td>
                </tr>
            <?php endif; ?>
            <tr style="border-bottom: 1px solid black;">
                <td>&nbsp;&nbsp;&nbsp;<?php echo e($detail['coa_akun']); ?></td>
                <td>&nbsp;&nbsp;&nbsp;<?php echo e($detail['nama_akun']); ?></td>
                <td style="text-align: right;"><?php echo e($detail['subtotal'] ? number_format($detail['subtotal'], 0, ',', '.') : 0); ?></td>
                <td style="text-align: right;"><?php echo e($detail['debit'] ? number_format($detail['debit'], 0, ',', '.') : 0); ?></td>
                <td style="text-align: right;"><?php echo e($detail['credit'] ? number_format($detail['credit'], 0, ',', '.') : 0); ?></td>
            </tr>
            <?php
                $totalDebit += $detail['debit'];
                $totalCredit += $detail['credit'];
            ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr style="border-top: 2px solid black;">
                <td colspan="3" style="text-align: right; font-weight: bold; margin-top: 10px;">Total:</td>
                <td style="text-align: right; margin-top: 10px;"><?php echo e(number_format($totalDebit, 0, ',', '.')); ?></td>
                <td style="text-align: right; margin-top: 10px;"><?php echo e(number_format($totalCredit, 0, ',', '.')); ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <tbody>
            <tr>
                <td><i>Lampiran: <?php echo e($jurnal['keterangan']); ?></i></td>
            </tr>
            <tr>
                <td><i>Terbilang : <?php echo e(terbilang($totalDebit).' Rupiah'); ?></i></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\dedeProject\Development\hisabuna\resources\views\report\transaksi.blade.php ENDPATH**/ ?>