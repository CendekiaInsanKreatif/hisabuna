<?php
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

$report = [
    [
        'label' => 'Neraca',
        'route' => '/report/neraca-perbandingan'
    ],
    [
        'label' => 'Neraca Perbandingan',
        'route' => '/report/neraca'
    ],
    [
        'label' => 'Laba / Rugi',
        'route' => '/report/labarugi'
    ],
    [
        'label' => 'Laporan Perubahan Modal',
        'route' => '/report/perubahanekuitas'
    ],
    [
        'label' => 'Laporan Arus Kas',
        'route' => '/report/aruskas'
    ],
    [
        'label' => 'Neraca Saldo',
        'route' => '/report/neraca-saldo'
    ],
    [
        'label' => 'Mutasi Saldo',
        'route' => '/report/mutasi-saldo'
    ],
    [
        'label' => 'Buku Besar',
        'route' => '/report/bukubesar'
    ]
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
<div class="container w-full" x-data="">
    <h1 class="text-2xl font-bold">Semua Laporan</h1>
    <br>
    <div class="flex flex-wrap -mx-2">
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="text" id="start_date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            
        </div>
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="text" id="end_date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            
        </div>
    </div>
    
    <div class="flex flex-wrap -mx-2">
        <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="container-card-report mt-1 flex flex-row gap-5 w-full p-2">
            <div class="report-card p-2 bg-slate-50 border hover:bg-slate-100 border-slate-200 rounded-md flex justify-between items-center w-full cursor-pointer">
                <p class="text-xl"><?php echo e($i['label']); ?></p>
                <div class="flex gap-3 items-center">
                    <?php if($i['route'] == '/report/bukubesar'): ?>
                            <label for="akun" class="text-sm font-medium">Akun:</label>
                                <input type="text" id="akun" name="akun" placeholder="Filter Buku Besar by CoA" readonly class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <button type="button" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200" @click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                                    Pilih
                                </button>
                    <?php endif; ?>
                    <?php if(in_array($i['route'], ['/report/neraca-perbandingan', '/report/neraca', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas'])): ?>
                        <button @click="showReport('<?php echo e($loop->index); ?>', '<?php echo e($i['route']); ?>')" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200">Show Options</button>
                    <?php endif; ?>
                    <button @click="downloadReport('<?php echo e($i['route']); ?>', <?php echo e($loop->index); ?>)" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200">Preview</button>
                    
                    

                </div>
            </div>
        </div>
        <?php if(in_array($i['route'], ['/report/neraca-perbandingan', '/report/neraca', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas'])): ?>
        <?php
            $pecah = explode('/', $i['route']);
            $pecah = end($pecah);
        ?>
            <div id="showReport<?php echo e($loop->index); ?>" class="hidden container-card-report w-full p-2">
                <div class="flex justify-evenly p-2">
                    <div class="px-1">
                        <label for="alamat_<?php echo e($loop->index); ?>" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <input type="text" id="alamat_<?php echo e($loop->index); ?>" oninput="funcState(this.value, 'alamat', <?php echo e($loop->index); ?>)" name="alamat_<?php echo e($loop->index); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="tanggal_<?php echo e($loop->index); ?>" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="text" id="tanggal_<?php echo e($loop->index); ?>" oninput="funcState(this.value, 'tanggal', <?php echo e($loop->index); ?>)" name="tanggal_<?php echo e($loop->index); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="dibuat_<?php echo e($loop->index); ?>" class="block text-sm font-medium text-gray-700">Dibuat</label>
                        <input type="text" id="dibuat_<?php echo e($loop->index); ?>" oninput="funcState(this.value, 'dibuat', <?php echo e($loop->index); ?>)" name="dibuat_<?php echo e($loop->index); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="jabatan_<?php echo e($loop->index); ?>" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" id="jabatan_<?php echo e($loop->index); ?>" oninput="funcState(this.value, 'jabatan', <?php echo e($loop->index); ?>)" name="jabatan_<?php echo e($loop->index); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>
            </div>            
        <?php endif; ?>
            
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            let periode = <?php echo \Illuminate\Support\Js::from(auth()->user()->periode)->toHtml() ?>;
            let val = 0;
            let defaultDateStart = new Date(new Date(new Date().getFullYear(), new Date().getMonth(), 1).setHours(0, 0, 0, 0))
            let defaultDateEnd = new Date(new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).setHours(23, 59, 59, 999))

            $('#signature1').toggleClass('hidden', !$('#signature').is(':checked'));
            $('#signature2').toggleClass('hidden', !$('#signature').is(':checked'));

            tambahLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                $('#jumlahLaman'+index).val(jumlah + 1);
            }

            kurangiLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                if (jumlah > 1) {
                    $('#jumlahLaman'+index).val(jumlah - 1);
                }
            }

            showReport = (index, route) => {
                if (route === '/report/neraca-perbandingan' || route === '/report/neraca' || route === '/report/labarugi' || route === '/report/aruskas' || route === '/report/perubahanekuitas') {
                    $('#showReport'+index).fadeToggle(300);
                }
            }

            funcState = (value, field, index) => {
                const containers = document.querySelectorAll('.container-card-report');
                const tanggal = flatpickr(`#tanggal_${index}`, {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: `01-01-${periode}`,
                    maxDate: `31-12-${periode}`,
                    defaultDate: defaultDateStart
                });
                containers.forEach((container) => {
                    const input = container.querySelector(`#${field}_${index}`);
                    if (input) {
                        const inputsToUpdate = document.querySelectorAll(`input[id^="${field}_"]:not(#${field}_${index-1})`);
                        inputsToUpdate.forEach(inputToUpdate => {
                            inputToUpdate.value = value;
                        });
                    }
                });
            }

            flatpickr('#start_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-' + periode,
                maxDate: '31-12-' + periode,
                defaultDate: defaultDateStart,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                }
            });

            flatpickr('#end_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-' + periode,
                maxDate: '31-12-' + periode,
                defaultDate: defaultDateEnd,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                    var startDate = $('#start_date').val();
                    if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                        alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                        instance.clear();
                    }
                }
            });

            window.$('#signature').on('change', function() {
                $('#signature1').toggleClass('hidden', !$(this).is(':checked'));
                $('#signature2').toggleClass('hidden', !$(this).is(':checked'));
            });


            downloadReport = function(route, index) {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let akun = $('#akun').val();
                let alamat = $('#alamat_'+index).val();
                let tanggal = $('#tanggal_'+index).val();
                let dibuat = $('#dibuat_'+index).val();
                let jabatan = $('#jabatan_'+index).val();
                let jumlahLaman = $('#jumlahLaman'+index).val();
                let token = $('meta[name="csrf-token"]').attr("content");

                var form = $('<form>', {
                    'method': 'POST',
                    'action': route,
                    'target': '_blank'
                }).append($('<input>', {
                    'name': 'start_date',
                    'value': startDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'end_date',
                    'value': endDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'akun',
                    'value': akun,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'alamat',
                    'value': alamat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'tanggal',
                    'value': tanggal,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'dibuat',
                    'value': dibuat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jabatan',
                    'value': jabatan,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jumlahLaman',
                    'value': jumlahLaman,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': '_token',
                    'value': token,
                    'type': 'hidden'
                }));

                form.appendTo('body').submit();
            };


            
        });
        
    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trial_hisabuna/backend/resources/views/report/template.blade.php ENDPATH**/ ?>