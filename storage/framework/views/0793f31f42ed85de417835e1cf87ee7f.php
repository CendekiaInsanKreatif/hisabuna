<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COA List</title>
</head>
<body>
    <h1>COA</h1>
    <h4><?php echo e(auth()->user()->name); ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($item->nomor_akun); ?></td>
                    <td><?php echo e($item->nama_akun); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\dedeProject\Development\hisabuna\resources\views\report\printcoa.blade.php ENDPATH**/ ?>