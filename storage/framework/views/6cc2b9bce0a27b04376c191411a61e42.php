<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    <title>Laba Rugi</title>
    
    <style>
        @import url('<?php echo e(asset('css/rpt.css')); ?>');
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
        <div class="header" style="text-align: center;">
            <h1><?php echo e(auth()->user()->company_name); ?></h1>
            <h2>Laporan Laba Rugi</h2>
            <h3>Periode <?php echo e(date('d/m/Y', strtotime($start))); ?> s/d <?php echo e(date('d/m/Y', strtotime($end))); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; padding-right: 10px;">
    <div class="report-container">
    <?php $__currentLoopData = $dataChunked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="page-break">
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <table class="main-data">
                <h3><?php echo e($category); ?></h3>
                <?php $__currentLoopData = $details['Detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="data-desc"><?php echo e($item); ?></td>
                        <td class="data-num"><?php echo e(number_format($amount, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="padding: 0px; margin: 0px; font-weight: bold; font-size: 14px;">Total <?php echo e($category); ?></td>
                    <td class="data-num" style="text-align: right; font-weight: bold; font-size: 12px;"><?php echo e(number_format($details['Jumlah'], 0, ',', '.')); ?></td>
                </tr>
                <div style="height: 5px;"></div>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div style="height: 5px;"></div>
            <table class="main-data">
                <tr>
                    <td style="padding: 0px; margin: 0px; font-weight: bold; font-size: 16px;">Saldo Laba (Rugi) Tahun Berjalan</td>
                    <td style="text-align: right; border-top: 2px solid black; width: 20%; font-weight: bold; font-size: 12px;">
                        <h3><?php echo e(number_format($labaRugiBersih, 0, ',', '.')); ?></h3>
                    </td>
                </tr>
            </table>
        <footer>
            <table style="width: 100%; margin-top: 70px;">
                <tr>
                    <td style="text-align: center; width: 100%;">
                        <div style="font-size: 12px;"><?php echo e($paged['alamat']); ?>, <?php echo e($paged['tanggal']); ?></div>
                        <div style="height: 80px;"></div>
                        <div style="font-size: 12px;"><?php echo e($paged['dibuat']); ?></div>
                        <div style="border-bottom: 2px solid black; width: 100px; margin-left: auto; margin-right: auto;"></div>
                        <div style="font-size: 12px;"><?php echo e($paged['jabatan']); ?></div>
                    </td>
                </tr>
            </table>
        </footer>
        <!-- Footer Nomor Halaman -->
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/labarugi.blade.php ENDPATH**/ ?>