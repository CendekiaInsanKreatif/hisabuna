<div id="ledgerContent">
    <div style="text-align: center;">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" style="width: 50px; height: 50px;">
        <h1><?php echo e(auth()->user()->company_name); ?></h1>
        <br>
        <h3 style="font-size: 1.5em; font-weight: bold; color: #101111; margin: 0; padding: 0;"><u>BUKU BESAR (LEDGER)</u></h3>
        <p>Periode <?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y')); ?></p>
        <br>
    </div>
    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coaAkun => $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2 style="font-size: 1.25em; font-weight: 600; color: #4a5568;"><?php echo e($coaAkun); ?></h2>
        <table style="width: 100%; border-collapse: collapse; margin: 2px;">
            <thead style="background-color: #f7fafc;">
                <tr>
                    <th scope="col" style="width: 20%; padding: 8px; text-align: left; font-size: 0.65rem; font-weight: 500; color: #718096; text-transform: uppercase;">Tanggal</th>
                    <th scope="col" style="min-width: 10cm; padding: 8px; text-align: left; font-size: 0.65rem; font-weight: 500; color: #ffffff; text-transform: uppercase;">Keterangan</th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.65rem; font-weight: 500; color: #718096; text-transform: uppercase;">Debit<div style="color: #e53e3e;"><?php echo e($transactions->first()->coa->saldo_normal == 'debit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.65rem; font-weight: 500; color: #718096; text-transform: uppercase;">Kredit<div style="color: #e53e3e;"><?php echo e($transactions->first()->coa->saldo_normal == 'credit' ? 'Bertambah' : 'Berkurang'); ?></div></th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.65rem; font-weight: 500; color: #718096; text-transform: uppercase;">Saldo</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;"><?php echo e(\Carbon\Carbon::parse($transaction->jurnal_tgl)->format('d/m/Y')); ?></td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">
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
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096; text-align-right;"><?php echo e(number_format($transaction->debit, 0, ',', '.')); ?></td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096; text-align-right;"><?php echo e(number_format($transaction->credit, 0, ',', '.')); ?></td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096; text-align-right;"><?php echo e(number_format($transaction->saldo, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /var/www/hisabuna/resources/views/report/bukubesar_download.blade.php ENDPATH**/ ?>