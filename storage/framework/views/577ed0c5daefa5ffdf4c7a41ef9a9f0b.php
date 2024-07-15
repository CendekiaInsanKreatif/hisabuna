<!DOCTYPE html>
<html>
<head>
    <title>Neraca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .category, .total {
            font-weight: bold;
        }
        .total {
            background-color: #f2f2f2;
        }
        h2, h4 {
            margin: 0;
        }
    </style>
</head>
<body>
<table>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $subcategories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <th colspan="2" class="category"><h2><?php echo e($category); ?></h2></th>
        </tr>
        <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td colspan="2" class="subcategory"><h4>&nbsp;&nbsp;&nbsp;<?php echo e($subcategory); ?></h4></td>
            </tr>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($item); ?></td>
                    <td style="text-align: right"><?php echo e(number_format($value, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="total">Total <?php echo e($subcategory); ?></td>
                <td class="total" style="text-align: right"><?php echo e(number_format($totals[$category][$subcategory], 0, ',', '.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="total">Total <?php echo e($category); ?></td>
            <td class="total" style="text-align: right"><?php echo e(number_format($totals[$category]['Total'], 0, ',', '.')); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

</body>
</html>
<?php /**PATH C:\dedeProject\Development\hisabuna\resources\views\report\neraca.blade.php ENDPATH**/ ?>