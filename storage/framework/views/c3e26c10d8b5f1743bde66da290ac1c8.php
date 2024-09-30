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
    <?php
        $route = Route::currentRouteName();
        $route = explode('.', $route);
        $title = ucfirst($route[0]).' '.ucwords($route[2]);

        switch($route[2]) {
            case 'neraca':
                $title = 'Neraca';
                break;
            case 'labarugi':
                $title = 'Laba Rugi';
                break;
            case 'perubahanekuitas':
                $title = 'Perubahan Ekuitas';
                break;
            case 'aruskas':
                $title = 'Arus Kas';
                break;
            case 'neracasaldo':
                $title = 'Neraca Saldo';
                break;
            case 'bukubesar':
                $title = 'Buku Besar';
                break;
            case 'mutasisaldo':
                $title = 'Mutasi Saldo';
                break;
        }

        $fieldSelect = [
                [
                    'name' => 'nomor_akun',
                    'type' => 'text',
                    'label' => 'Nomor Akun',
                    'required' => true,
                ],
                [
                    'name' => 'nama_akun',
                    'type' => 'text',
                    'label' => 'Nama Akun',
                    'required' => true,
                ],
            ];
    ?>
    <?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fieldSelect,'data' => @$coa,'maxWidth' => '2xl','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldSelect),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(@$coa),'maxWidth' => '2xl','focusable' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
        <div class="container mx-auto p-4 max-w-2xl" x-data="">
            <h1 class="text-2xl font-bold mb-4"><?php echo e('Report '.$title); ?></h1>
            <form action="<?php echo e(route($route[0].'.'.$route[2])); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
                <?php if($route[2] == 'neraca'): ?>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Neraca Per Tanggal:</label>
                        <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                    </div>
                <?php else: ?>
                <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Mulai:</label>
                    <input type="text" id="start_date" name="start_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                </div><div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Selesai:</label>
                    <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                </div>
                <?php endif; ?>
                <?php if($route[2] == 'neraca' || $route[2] == 'labarugi'): ?>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input1" class="block text-sm font-medium text-gray-700 md:w-1/4">Dibuat Oleh:</label>
                        <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input2" class="block text-sm font-medium text-gray-700 md:w-1/4">Disetujui Oleh:</label>
                        <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                <?php endif; ?>
                <?php if($route[2] == 'bukubesar'): ?>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="akun" class="block text-sm font-medium text-gray-700 md:w-1/4">Akun:</label>
                        <input type="text" id="akun" name="akun" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                            Pilih
                        </button>
                    </div>
                <?php endif; ?>
                <div class="flex justify-center md:justify-start gap-1">
                    <button type="button" id="popup" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4">View</button>
                </div>
            </form>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('script'); ?>
        <script type="module">
            
            let route = <?php echo \Illuminate\Support\Js::from($route[2])->toHtml() ?>;
            let periode = <?php echo \Illuminate\Support\Js::from(auth()->user()->periode)->toHtml() ?>;

            $(function() {
                $('#popup').click(function() {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': '<?php echo e(route('report.'.$route[2])); ?>',
                        'target': '_blank'
                    }).append($('<input>', {
                        'name': '_token',
                        'value': '<?php echo e(csrf_token()); ?>',
                        'type': 'hidden'
                    })).append($('<input>', {
                        'name': 'start_date',
                        'value': $('#start_date').val(),
                        'type': 'hidden'
                    })).append($('<input>', {
                        'name': 'end_date',
                        'value': $('#end_date').val(),
                        'type': 'hidden'
                    }));

                    if (route == 'neraca' || route == 'labarugi') {
                        form.append($('<input>', {
                            'name': 'text_input1',
                            'value': $('#text_input1').val(),
                            'type': 'hidden'
                        })).append($('<input>', {
                            'name': 'text_input2',
                            'value': $('#text_input2').val(),
                            'type': 'hidden'
                        }));
                    }

                    if (route == 'bukubesar') {
                        form.append($('<input>', {
                            'name': 'akun',
                            'value': $('#akun').val(),
                            'type': 'hidden'
                        }));
                    }

                    form.appendTo('body').submit();
                });

                
                flatpickr('#start_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: '01-01-' + periode,
                    maxDate: '31-12-' + periode,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                    }
                });

                flatpickr('#end_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: '01-01-' + periode,
                    maxDate: '31-12-' + periode,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                        var startDate = $('#start_date').val();
                        if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                            alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                            instance.clear();
                        }
                    }
                });

                $('#view').click(function() {
                    alert('Lukman');
                })
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
<?php /**PATH /var/www/hisabuna/backend/resources/views/report/views/template.blade.php ENDPATH**/ ?>