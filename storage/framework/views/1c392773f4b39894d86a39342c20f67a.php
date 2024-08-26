<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
            /* onload="window.print()" */
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

        h2 {
            margin: 0;
            padding: 0;
        }


        h3 {
            margin: 0;
            padding: 0;
        }

        /* .header {
            text-align: center;
            padding: 20px;
        }

        .header h1,
        .header h2,
        .header h4 {
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
        } */

        /* @media print {
            body {
                display: block;
            }

            .header {
                text-align: center;
                padding: 0;
            }

            .header h1,
            .header h2,
            .header h4 {
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
        } */

        /* .footer {
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
        } */

        table {
            width: 100%;
        }

        th,
        td {
            padding: 2px;
            text-align: left;
        }
    </style>
</head>

<body onload="window.print()">
    <header class="new-header">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo">
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2><u><?php echo e($jurnal['jenis'] == 'RV' ? 'JURNAL KAS MASUK' : ($jurnal['jenis'] == 'PV' ? 'JURNAL KAS KELUAR' : ($jurnal['jenis'] == 'JV' ? 'JURNAL UMUM' : ''))); ?></u>
            </h2>
            <h3><?php echo e($jurnal['jenis'] == 'RV' ? 'RECEIVE VOUCHER' : ($jurnal['jenis'] == 'PV' ? 'PAYMENT VOUCHER' : ($jurnal['jenis'] == 'JV' ? 'JOURNAL VOUCHER' : ''))); ?>

            </h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    <table>
        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%;">Informasi Tambahan</td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top;">Nomor Transaksi</td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top;">Jenis Jurnal</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center; width: 60%; vertical-align: center;"><b><em><?php echo e($jurnal['keterangan']); ?></em></b></td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top; font-size: 35px; font-weight: bold;">
                    <?php echo e($jurnal['no_urut_transaksi']); ?>

                </td>
                <td style="width: 20%; border: 1px solid black; text-align: center; vertical-align: top; font-size: 35px; font-weight: bold;">
                    <?php echo e($jurnal['jenis']); ?>

                </td>
            </tr>
            
            
        </tbody>
    </table>
    <table>
        <thead>
            <tr style="border: 1px solid black;">
                <th style="height: 25px; text-align:center;">Nomor Akun</th>
                <th style="height: 25px; text-align:center;">Nama Akun</th>
                <th style="height: 25px; text-align:center;">Subtotal</th>
                <th style="height: 25px; text-align:center;">Debit</th>
                <th style="height: 25px; text-align:center;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalDebit = 0;
                $totalCredit = 0;
            ?>
            <?php $__currentLoopData = $jurnal['details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(
                    $index == 0 ||
                        $jurnal['details'][$index]['parent']['nomor_akun'] != $jurnal['details'][$index - 1]['parent']['nomor_akun']
                ): ?>
                    <tr style="border-top: 1px solid black">
                        <td style="font-weight: bold;"><?php echo e($detail['parent']['nomor_akun']); ?></td>
                        <td style="font-weight: bold;"><?php echo e($detail['parent']['nama_akun']); ?></td>
                        <td style="text-align: right; font-weight: bold;">
                            <?php echo e($detail['parent']['total'] ? number_format($detail['parent']['total'], 0, ',', '.') : 0); ?>

                        </td>
                        <td style="text-align: right;">
                            <?php echo e($jurnal['debit'] ? number_format($jurnal['debit'], 0, ',', '.') : 0); ?></td>
                        <td style="text-align: right;">
                            <?php echo e($jurnal['credit'] ? number_format($jurnal['credit'], 0, ',', '.') : 0); ?></td>
                    </tr>
                <?php endif; ?>
                <tr style="border-bottom: 1px solid black;">
                    <td>
                        <?php
                            $formattedNomorAkun = preg_replace('/\D/', '', $detail['coa_akun']);
                            if (strlen($formattedNomorAkun) > 6) {
                                $formattedNomorAkun =
                                    substr($formattedNomorAkun, 0, 3) .
                                    '-' .
                                    substr($formattedNomorAkun, 3, 2) .
                                    '-' .
                                    substr($formattedNomorAkun, 5);
                            } elseif (strlen($formattedNomorAkun) > 4) {
                                $formattedNomorAkun =
                                    substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3);
                            } else {
                                $formattedNomorAkun = substr($formattedNomorAkun, 0, 3);
                            }
                        ?>
                        &nbsp;&nbsp;&nbsp;<?php echo e($formattedNomorAkun); ?>

                    </td>
                    <td>&nbsp;&nbsp;&nbsp;<?php echo e($detail['nama_akun']); ?></td>
                    <td style="text-align: right;">
                        <?php echo e($detail['debit'] ? number_format($detail['debit'] + $detail['credit'], 0, ',', '.') : number_format($detail['debit'] + $detail['credit'], 0, ',', '.')); ?>

                    </td>
                    <td style="text-align: right;">
                        <?php echo e($detail['debit'] ? number_format($detail['debit'], 0, ',', '.') : 0); ?></td>
                    <td style="text-align: right;">
                        <?php echo e($detail['credit'] ? number_format($detail['credit'], 0, ',', '.') : 0); ?></td>
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
                <td><i>Terbilang : <?php echo e(terbilang($totalDebit) . ' Rupiah'); ?></i></td>
            </tr>
        </tbody>
    </table>
    
</body>

</html>
<?php /**PATH C:\laragon\www\hisabuna\resources\views/report/transaksi.blade.php ENDPATH**/ ?>