<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COA Akun</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 3px;
            background-color: #f4f6f9;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 3px;
        }
        h4 {
            text-align: center;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div id="btnDownload" style="position: absolute; top: 10px; right: 10px; padding: 5px; background-color: #f8f9fa; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <?php if($bType == 'preview'): ?>
            <a href="<?php echo e(route('report.print-coa')); ?>" class="btn" style="background-color: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background-color 0.3s ease;">
                Download
            </a>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" style="width: 100px; display: block; margin: 0 auto;">
        <h1 style="margin: 10px 0;"><?php echo e(auth()->user()->company_name); ?></h1>
        <h4 style="margin: 0;">Chart of Account</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: center">Kode Akun</th>
                <th style="text-align: center">Nama Akun</th>
                <th style="text-align: center">Level</th>
                <th style="text-align: center">Golongan</th>
                <th style="text-align: center">Saldo Normal</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $formattedNomorAkun = preg_replace('/\D/', '', $item->nomor_akun);
                    if (strlen($formattedNomorAkun) > 6) {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3, 2) . '-' . substr($formattedNomorAkun, 5);
                    } elseif (strlen($formattedNomorAkun) > 4) {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3) . '-' . substr($formattedNomorAkun, 3);
                    } else {
                        $formattedNomorAkun = substr($formattedNomorAkun, 0, 3);
                    }
                ?>

                <tr>
                    <td><?php echo e($formattedNomorAkun); ?></td>
                    <td style="padding-left: <?php echo e($item->level * 15); ?>px;"><?php echo e($item->nama_akun); ?></td>
                    <td style="text-align: center"><?php echo e($item->level); ?></td>
                    <td><?php echo e($item->golongan); ?></td>
                    <td style="text-align: center"><?php echo e($item->saldo_normal); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/printcoa.blade.php ENDPATH**/ ?>