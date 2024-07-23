<?php
    $menus = getMenu();
?>

<div class="sidebar bg-gray-100 overflow-auto h-screen" id="sidebar">
    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-2 py-2 flex flex-col bg-gray-100 rounded">
            <p class="text-sm font-medium text-zinc-400 tracking-widest"><?php echo e($menu->name); ?></p>
            <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($child->route === '#' ? '#' : route($child->route)); ?>"
                class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600 <?php echo e($child->is_active == 0 ?'opacity-50 cursor-not-allowed' : ''); ?>"
                <?php echo e($child->is_active == 0 ? 'aria-disabled=true' : ''); ?>>
                    <div class="w-7 h-7 grid place-items-center relative">
                        <img src="<?php echo e(asset($child->icon)); ?>" alt="icon" class="w-full h-full" />
                    </div>
                    <p class="text-base tracking-normal leading-normal"><?php echo e($child->name); ?></p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="px-2 py-2 flex flex-col bg-gray-100 rounded mb-12 items-center">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="bg-transparent hover:bg-red-500 text-red-700 hover:text-white p-2 rounded w-full sm:w-auto">
                Logout
            </button>
        </form>
    </div>
</div>
<?php /**PATH /var/www/hisabuna/resources/views/layouts/navigation.blade.php ENDPATH**/ ?>