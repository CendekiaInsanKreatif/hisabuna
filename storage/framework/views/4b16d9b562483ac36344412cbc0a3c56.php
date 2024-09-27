<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chart of Account</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            margin: 0;
            padding: 0;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .new-header {
            position: relative;
            margin-bottom: 20px;
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
            <h2>Chart of Account</h2>
        </div>
    </header>
    <br>
    <br>
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
                    <td style="text-align: center"><?php echo e($item->golongan); ?></td>
                    <td style="text-align: center"><?php echo e(ucwords($item->saldo_normal)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/trial_hisabuna/backend/resources/views/report/printcoa.blade.php ENDPATH**/ ?>