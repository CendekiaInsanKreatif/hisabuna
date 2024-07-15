<button <?php echo e($attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong'])); ?>>
    <?php echo e($slot); ?>

</button>
<?php /**PATH /var/www/hisabuna/resources/views/components/primary-button.blade.php ENDPATH**/ ?>