<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jurnal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 100%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .logo {
            width: 50px;
            height: 50px;
            background-color: #000;
            margin: 0;
            padding: 0;
            margin-left: 30px;
            align-self: flex-start;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            font-size: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        th:nth-child(1), th:nth-child(4), th:nth-child(8) {
            text-align: center;
        }
        td:nth-child(1), td:nth-child(8) {
            text-align: right;
        }
        .period {
            text-align: center;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container" style="overflow-x: auto; overflow-y: hidden; display: flex; justify-content: center;">
        <div class="header-content" style="text-align: center; width: 100%;">
            <div id="titleHeader" class="header-text" style="display: flex; align-items: center; justify-content: center; flex-direction: row;">
                <div>
                    <h2 style="padding: 0; margin: 0; margin-top: 20px;"><?php echo e(auth()->user()->company_name); ?></h2>
                </div>
            </div>
            <br>
            <div class="title">
                <h2>DAFTAR JURNAL</h2>
                <p class="period">Periode <?php echo e(\Carbon\Carbon::parse($tgl_awal)->format('d/m/Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($tgl_akhir)->format('d/m/Y')); ?></p>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nomor Transaksi</th>
                    <th style="text-align: center">Jenis Jurnal</th>
                    <th style="text-align: center">Keterangan Transaksi</th>
                    <th>Jumlah</th>
                </tr>
                <tr>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $jurnal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="<?php echo e($item->jenis == 'RV' ? 'background-color: #fee2e2;' : ($item->jenis == 'PV' ? 'background-color: #f4f4f5;' : ($item->jenis == 'JV' ? 'background-color: #fef9c3;' : ''))); ?>">
                        <td style="text-align: center"><?php echo e($loop->iteration); ?></td>
                        <td style="text-align: center"><?php echo e($item->jenis); ?></td>
                        <td><?php echo e($item->keterangan); ?></td>
                        <td style="text-align: right"><?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/daftarjurnal.blade.php ENDPATH**/ ?>