<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startSection('content'); ?>
    <form method="GET" action="<?php echo e(route('report.bukubesar')); ?>" class="mb-6">
        <div class="flex space-x-2 items-end mb-4">
            <div class="flex flex-col">
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai:</label>
                <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo e($tanggalMulai); ?>" class="shadow-sm focus:ring-emerald-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai:</label>
                <input type="text" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo e($tanggalSelesai); ?>" class="shadow-sm focus:ring-emerald-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex flex-col">
                <label for="akun" class="block text-sm font-medium text-gray-700">Search Akun:</label>
                <input type="text" id="akun" name="akun" value="<?php echo e($akun); ?>" class="shadow-sm focus:ring-emerald-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="flex items-end">
                
                <a href="#" id="download" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">View</a>
            </div>
        </div>
    </form>

    
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('script'); ?>
    <script type="module">
        $(document).ready(function() {
            flatpickr("#tanggal_mulai", {
                dateFormat: "d-m-Y",
                onChange: function(selectedDates, dateStr, instance) {
                    var tanggalMulai = selectedDates[0];
                    flatpickr("#tanggal_selesai", {
                        dateFormat: "d-m-Y",
                        minDate: tanggalMulai,
                        defaultDate: tanggalMulai
                    });
                }
            });

            flatpickr("#tanggal_selesai", {
                dateFormat: "d-m-Y",
                onOpen: function(selectedDates, dateStr, instance) {
                    var tanggalMulai = flatpickr("#tanggal_mulai").selectedDates[0];
                    if (tanggalMulai) {
                        instance.set("minDate", tanggalMulai);
                    }
                }
            });

            $('#download').click(function() {
                $('<form>', {
                    'method': 'POST',
                    'action': '<?php echo e(route('report.bukubesar.download')); ?>',
                    'target': '_blank'
                }).append($('<input>', {
                    'name': '_token',
                    'value': '<?php echo e(csrf_token()); ?>',
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'tanggal_mulai',
                    'value': $('#tanggal_mulai').val(),
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'tanggal_selesai',
                    'value': $('#tanggal_selesai').val(),
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'akun',
                    'value': $('#akun').val(),
                    'type': 'hidden'
                })).appendTo('body').submit();
            });
        });
        
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>

<?php /**PATH /var/www/hisabuna/resources/views/report/bukubesar.blade.php ENDPATH**/ ?>