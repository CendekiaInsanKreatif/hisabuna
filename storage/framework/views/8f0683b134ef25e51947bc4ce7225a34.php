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
            'route' => '/report/neraca',
        ],
        [
            'label' => 'Neraca Perbandingan',
            'route' => '/report/neraca-perbandingan',
        ],
        [
            'label' => 'Laba / Rugi',
            'route' => '/report/labarugi',
        ],
        [
            'label' => 'Laporan Perubahan Modal',
            'route' => '/report/perubahanekuitas',
        ],
        [
            'label' => 'Laporan Arus Kas',
            'route' => '/report/aruskas',
        ],
        [
            'label' => 'Neraca Saldo',
            'route' => '/report/neraca-saldo',
        ],
        [
            'label' => 'Mutasi Saldo',
            'route' => '/report/mutasi-saldo',
        ],
        [
            'label' => 'Buku Besar',
            'route' => '/report/bukubesar',
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
    <div class="container mx-auto px-4 py-6 max-w-7xl" x-data="">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Semua Laporan</h1>
            <p class="text-gray-600">Pilih periode dan generate laporan keuangan</p>
        </div>

        <!-- Date Filter Card -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Filter Periode Laporan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <div class="relative">
                        <input type="text" id="start_date" name="start_date" placeholder="Pilih tanggal mulai"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 ease-in-out">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <div class="relative">
                        <input type="text" id="end_date" name="end_date" placeholder="Pilih tanggal selesai"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150 ease-in-out">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="space-y-3">
            <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="container-card-report">
                    <div
                        class="report-card bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-5 gap-4">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800"><?php echo e($i['label']); ?></h3>
                                    <p class="text-sm text-gray-500">Laporan periode terpilih</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <?php if($i['route'] == '/report/bukubesar'): ?>
                                    <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                                        <label for="akun"
                                            class="text-sm font-medium text-gray-700 whitespace-nowrap">Akun:</label>
                                        <input type="text" id="akun" name="akun" placeholder="Pilih akun..."
                                            readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
                                        <button type="button"
                                            class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-150"
                                            @click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('coas.index')); ?>', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                                            Pilih
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if(in_array($i['route'], [
                                        '/report/neraca',
                                        '/report/neraca-perbandingan',
                                        '/report/labarugi',
                                        '/report/aruskas',
                                        '/report/perubahanekuitas',
                                    ])): ?>
                                    <button @click="showReport('<?php echo e($loop->index); ?>', '<?php echo e($i['route']); ?>')"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Opsi
                                    </button>
                                <?php endif; ?>

                                <button @click="downloadReport('<?php echo e($i['route']); ?>', <?php echo e($loop->index); ?>, 0)"
                                    class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-150 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(in_array($i['route'], [
                        '/report/neraca',
                        '/report/neraca-perbandingan',
                        '/report/labarugi',
                        '/report/aruskas',
                        '/report/perubahanekuitas',
                    ])): ?>
                    <?php
                        $pecah = explode('/', $i['route']);
                        $pecah = end($pecah);
                    ?>
                    <div id="showReport<?php echo e($loop->index); ?>" class="hidden container-card-report">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 ml-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Opsi Tambahan untuk <?php echo e($i['label']); ?>

                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label for="alamat_<?php echo e($loop->index); ?>"
                                        class="block text-sm font-medium text-gray-700 mb-2">Tempat</label>
                                    <input type="text" id="alamat_<?php echo e($loop->index); ?>"
                                        oninput="funcState(this.value, 'alamat', <?php echo e($loop->index); ?>)"
                                        name="alamat_<?php echo e($loop->index); ?>" placeholder="Kota/Tempat"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                </div>
                                <div>
                                    <label for="tanggal_<?php echo e($loop->index); ?>"
                                        class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                    <div class="relative">
                                        <input type="text" id="tanggal_<?php echo e($loop->index); ?>"
                                            oninput="funcState(this.value, 'tanggal', <?php echo e($loop->index); ?>)"
                                            name="tanggal_<?php echo e($loop->index); ?>" placeholder="Pilih tanggal"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="dibuat_<?php echo e($loop->index); ?>"
                                        class="block text-sm font-medium text-gray-700 mb-2">Ditandatangani Oleh</label>
                                    <input type="text" id="dibuat_<?php echo e($loop->index); ?>"
                                        oninput="funcState(this.value, 'dibuat', <?php echo e($loop->index); ?>)"
                                        name="dibuat_<?php echo e($loop->index); ?>" placeholder="Nama penandatangan"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                </div>
                                <div>
                                    <label for="jabatan_<?php echo e($loop->index); ?>"
                                        class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                    <input type="text" id="jabatan_<?php echo e($loop->index); ?>"
                                        oninput="funcState(this.value, 'jabatan', <?php echo e($loop->index); ?>)"
                                        name="jabatan_<?php echo e($loop->index); ?>" placeholder="Jabatan penandatangan"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                </div>
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
            let defaultDateStart = new Date(new Date(periode, 0, 1).setHours(0, 0, 0, 0));
            let defaultDateEnd = new Date(new Date(periode, 11, 31).setHours(23, 59, 59, 999));

            // Function to sync input fields across reports
            funcState = (value, field, index) => {
                const containers = document.querySelectorAll('.container-card-report');

                // Sync values across similar fields
                containers.forEach((container) => {
                    const input = container.querySelector(`#${field}_${index}`);
                    if (input) {
                        const inputsToUpdate = document.querySelectorAll(
                            `input[id^="${field}_"]:not(#${field}_${index-1})`);
                        inputsToUpdate.forEach(inputToUpdate => {
                            inputToUpdate.value = value;
                        });
                    }
                });
            }

            // Initialize flatpickr for all tanggal fields in report options
            initializeTanggalPickers = () => {
                const tanggalInputs = document.querySelectorAll('input[id^="tanggal_"]');
                tanggalInputs.forEach((input) => {
                    if (!input._flatpickr) { // Check if flatpickr is not already initialized
                        flatpickr(input, {
                            dateFormat: 'd-m-Y',
                            allowInput: true,
                            minDate: `01-01-${periode}`,
                            maxDate: `31-12-${periode}`,
                            defaultDate: new Date(),
                            locale: {
                                firstDayOfWeek: 1,
                                weekdays: {
                                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum',
                                        'Sab'],
                                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis',
                                        'Jumat', 'Sabtu'
                                    ]
                                },
                                months: {
                                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul',
                                        'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                                    ],
                                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei',
                                        'Juni', 'Juli', 'Agustus', 'September', 'Oktober',
                                        'November', 'Desember'
                                    ]
                                }
                            },
                            onChange: function(selectedDates, dateStr, instance) {
                                // Sync value to all other tanggal fields
                                const inputId = instance.input.id;
                                const index = inputId.split('_')[1];
                                funcState(dateStr, 'tanggal', index);
                            }
                        });
                    }
                });
            }

            // Function to show report options
            showReport = (index, route) => {
                if (route === '/report/neraca-perbandingan' || route === '/report/neraca' || route ===
                    '/report/labarugi' || route === '/report/aruskas' || route === '/report/perubahanekuitas') {
                    $('#showReport' + index).slideToggle(300, function() {
                        // Initialize flatpickr for newly visible tanggal fields
                        if ($(this).is(':visible')) {
                            initializeTanggalPickers();
                        }
                    });
                }
            }

            // Initialize tanggal pickers on page load (for any visible fields)
            initializeTanggalPickers();

            // Initialize start date picker
            const startDatePicker = flatpickr('#start_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: `01-01-${periode}`,
                maxDate: `31-12-${periode}`,
                defaultDate: defaultDateStart,
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                            'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ]
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update end date min date
                    if (selectedDates.length > 0) {
                        endDatePicker.set('minDate', selectedDates[0]);
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                }
            });

            // Initialize end date picker
            const endDatePicker = flatpickr('#end_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: defaultDateStart,
                maxDate: `31-12-${periode}`,
                defaultDate: defaultDateEnd,
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                            'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ]
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                    const startDate = $('#start_date').val();

                    if (startDate && selectedDates.length > 0) {
                        const startDateObj = new Date(startDate.split('-').reverse().join('-'));
                        const endDateObj = selectedDates[0];

                        if (endDateObj < startDateObj) {
                            alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                            instance.clear();
                        }
                    }
                }
            });

            // Download report function
            downloadReport = function(route, index, jenis = 0) {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                // Validate dates
                if (!startDate || !endDate) {
                    alert('Silakan pilih tanggal mulai dan tanggal selesai');
                    return;
                }

                let akun = $('#akun').val();
                let alamat = $('#alamat_' + index).val();
                let tanggal = $('#tanggal_' + index).val();
                let dibuat = $('#dibuat_' + index).val();
                let jabatan = $('#jabatan_' + index).val();
                let jumlahLaman = $('#jumlahLaman').val();
                let token = $('meta[name="csrf-token"]').attr("content");
                let routenya = '';

                if (jenis == 0) {
                    routenya = `${route}?excel=0`
                } else {
                    routenya = `${route}?excel=1`
                }

                var form = $('<form>', {
                    'method': 'POST',
                    'action': routenya,
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

                form.appendTo('body').submit().fail(function(jqXHR) {
                    if (jqXHR.status === 419) {
                        alert('Session expired. Please refresh the page.');
                        location.reload();
                    }
                });
            };

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/report/template.blade.php ENDPATH**/ ?>