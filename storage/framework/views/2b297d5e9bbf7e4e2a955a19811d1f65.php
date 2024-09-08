<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3px;
            /* background-color: #fff; */
        }


        table.main-data tr:nth-child(odd) {
            /* background-color: #f4f4f5;? */
        }

        @media print {
            body {
                display: block;
            }
            footer {
                page-break-after: always;
            }
        }


        table {
            width: 100%;
            /* border-collapse: collapse; */
        }

        table.main-data tr td {
            padding: 2px 2px 2px 22px;
        }

        table.main-data tr:nth-last-child(2) td.data-num {
            border-bottom: solid 1px black;
        }

        .data-desc {
            width: 80%;
        }

        .data-num {
            text-align: right;
        }

        h3 {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        h2 {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }


        .total {
            font-size: 11px;
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
            <h2>Laporan Laba Rugi</h2>
            <h3>Periode : <?php echo e($start); ?> - <?php echo e($end); ?></h3>
        </div>
    </header>
    <hr style="border: 2px solid black; width: 100%;">
    
    <div class="report-container">
        
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table class="main-data">
            <h3><?php echo e($category); ?></h3>
            <?php $__currentLoopData = $details['Detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="data-desc"><h3><?php echo e($item); ?></h3></td>
                    <td class="data-num"><?php echo e(number_format($amount, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total">
                <td style="padding: 2px">Total <?php echo e($category); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($details['Jumlah'], 0, ',', '.')); ?></td>
            </tr>
        </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div style="padding: 2px;">
            <table>
                <tr class="total">
                    <td><h2>Saldo Laba (Rugi) Tahun Berjalan</h2></td>
                    <td style="text-align: right; border-top: 4px solid black; border-bottom: 4px solid black; width: 20%;"><?php echo e(number_format($labaRugiBersih, 0, ',', '.')); ?></td>
                </tr>
            </table>
        </div>
        <footer>
            <table style="width: 100%; margin-top: 70px;">
            <tr>
                <td style="text-align: center; width: 35%;">
                    <div>Dibuat oleh, <?php echo e($ttd1); ?></div>
                    <div style="height: 80px;"></div>
                    <div><strong>Staff Keuangan</strong></div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 10%;"></td>
                <td style="width: 10%;"></td>
                <td style="text-align: center; width: 35%;">
                    <div>Disetujui oleh, <?php echo e($ttd2); ?></div>
                    <div style="height: 80px;"></div>
                    <div><strong>Manajer Keuangan</strong></div>
                </td>
            </tr>
        </table>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            $('#pdf').click(function() {
                var start = "<?php echo e($start); ?>";
                var end = "<?php echo e($end); ?>";
                $.ajax({
                    url     :'<?php echo e(route("report.labarugiprint")); ?>',
                    type    :'post',
                    data:{
                        start:start,
                        end:end
                    },
                    success : function(res) {

                    }
                })
            })
        })
    </script>
</body>
</html>
<?php /**PATH /var/www/hisabuna/resources/views/report/labarugi.blade.php ENDPATH**/ ?>