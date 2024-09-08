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
    <?php
        $fields = [
            [
                'name' => 'jenis',
                'label' => 'Jenis',
                'type' => 'text',
                'disabled' => true,
            ],
            [
                'name' => 'keterangan',
                'label' => 'Keterangan',
                'type' => 'text',
                'disabled' => true,
            ],
        ];
    ?>
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fields,'focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fields),'focusable' => true]); ?>
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
    <div class="container mx-auto px-4" x-data="jurnalTable">
        <div class="mb-4 mt-2">
            <p class="text-2xl text-emerald-500">Jurnal</p>
        </div>
        <div class="card bg-white rounded-xl border border-gray-200">
            <div class="flex flex-wrap items-center justify-between">
                <div id="filterAkun" class="flex-grow rounded p-3 flex flex-wrap gap-2 w-full md:w-auto justify-between">
                    <div class="flex flex-wrap gap-2 h-fit">
                        <button @click="filterCategory('all')" class="btn-akun bg-gray-200 rounded hover:bg-emerald-500 transition duration-300 text-sm h-fit px-3 py-2">Semua</button>
                        <button @click="filterCategory('rv')" class="btn-akun bg-gray-200 rounded hover:bg-emerald-500 transition duration-300 text-sm h-fit px-3 py-2">RV</button>
                        <button @click="filterCategory('pv')" class="btn-akun bg-gray-200 rounded hover:bg-emerald-500 transition duration-300 text-sm h-fit px-3 py-2">PV</button>
                        <button @click="filterCategory('jv')" class="btn-akun bg-gray-200 rounded hover:bg-emerald-500 transition duration-300 text-sm h-fit px-3 py-2">JV</button>
                        <div class="relative w-full md:w-auto flex-grow md:flex-grow-0">
                            <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:border-emerald-500 focus:ring focus:ring-emerald-500 focus:ring-opacity-50 w-full px-3 h-full" style="width: 600px" placeholder="Cari Jurnal . . ." @keydown.enter="searchJurnalTable">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="<?php echo e(route('report.daftarjurnal')); ?>" class="btn bg-gray-200 rounded px-3 py-2 hover:bg-emerald-500 transition duration-300 text-sm"><p style="line-height: 1.5;">Daftar Jurnal</p></a>
                    </div>
                    <a role="button" class="text-base py-2 px-4 inline-flex items-center justify-center bg-emerald-500 border border-transparent rounded-md text-white hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150" href="<?php echo e(route('jurnal.create')); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 28 28" class="mr-2">
                        <path fill="white" d="M14 5a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H6a1 1 0 1 1 0-2h7V6a1 1 0 0 1 1-1z"/>
                      </svg>Tambah Jurnal</a>
                </div>
                </div>
                <div class="card-body overflow-x-auto">
                    <table class="w-full min-w-full" id="jurnalTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer" style="font-weight: 400; line-height: 1; width: 132px;">
                                    <div class="flex items-center text-left">
                                        No. Transaksi
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer" style="font-weight: 400; line-height: 1; width: 132px;">
                                    <div class="flex items-center">
                                        Jenis Jurnal
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm text-gray-500 cursor-pointer" style="font-weight: 400; line-height: 1;">
                                    <div class="flex items-center">
                                        Keterangan
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-3 py-2 text-sm  text-gray-500  cursor-pointer text-center" style="font-weight: 400; width: 50px;">
                                    <div class="flex items-center justify-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="jurnalTableBody">
                            <template x-for="(jurnal, index) in paginatedData" :key="jurnal.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false" class="hover:bg-gray-100">
                                    <td class="text-left  px-3 py-2" x-text="jurnal.no_urut_transaksi"></td>
                                    <td class="text-center  " x-text="jurnal.jenis"></td>
                                    <td class="text-left px-3  break-words" x-text="jurnal.keterangan"></td>
                                    <td class="text-left  px-3 py-2">
                                        <div class="flex items-center flex-col md:flex-row gap-2">
                                            <a class="px-2 py-1 text-emerald-600 border border-emerald-600 rounded text-sm hover:bg-emerald-100 focus:bg-emerald-500 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150" :href="`<?php echo e(url('jurnal')); ?>/${jurnal.id}/edit`">
                                                Edit
                                            </a>
                                           
                                            <a class="btn border border-zinc-300 rounded items-center justify-center hover:bg-emerald-500 transition duration-300 h-fit text-sm px-2 py-1 cursor-pointer"  x-data="{data: jurnal}"
                                            x-on:click.prevent="$dispatch('open-modal', { route: `<?php echo e(route('jurnal.show', '')); ?>/${jurnal.id}`, name: 'jurnal.show', title: 'Lihat Jurnal', data: jurnal, type: 'form' })"
                                        ><?php echo e(__('View')); ?></a>
                                        <a :href="`<?php echo e(route('report.transaksi', '')); ?>/${jurnal.id}`" target="_blank" class="btn border border-zinc-300 rounded items-center justify-center hover:bg-emerald-500 transition duration-300 h-fit text-sm px-2 py-1">Print</a>
                                            
                                            
                                            
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="pagination flex justify-center p-4 space-x-2">
                        <button @click="prevPage" class="prev bg-emerald-600 text-white py-1 px-3 rounded">Previous</button>
                        <div id="pageNumbers" class="flex space-x-2">
                            <template x-for="page in pagesToShow" :key="page">
                                <button @click="changePage(page)" :class="{'bg-emerald-600 text-white': page === currentPage, 'bg-gray-200': page !== currentPage}" class="page-number py-1 px-3 rounded" x-text="page"></button>
                            </template>
                        </div>
                        <button @click="nextPage" class="next bg-emerald-600 text-white py-1 px-3 rounded">Next</button>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('script'); ?>
    <script>
        async function cekTrial()
        {
            await $.get('cekTrial').done((res,status,xhr)=> {
                var data = res.data;
            })
        }
        cekTrial();

        document.addEventListener('alpine:init', () => {
            Alpine.data('jurnalTable', () => ({
                currentPage: 1,
                rowsPerPage: 10,
                totalRows: 0,
                totalPage: 0,
                sortDirection: 'asc',
                filter: 'all',
                searchInput: '',
                allData: [],
                hover: false,
                get paginatedData() {
                    const filteredData = this.filteredData();
                    const start = (this.currentPage - 1) * this.rowsPerPage;
                    const end = start + this.rowsPerPage;
                    return filteredData.slice(start, end);
                },
                get pagesToShow() {
                    const startPage = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
                    const endPage = Math.min(startPage + 2, this.totalPage);
                    return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
                },
                changePage(page) {
                    this.currentPage = page;
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },
                nextPage() {
                    if (this.currentPage < this.totalPage) {
                        this.currentPage++;
                    }
                },
                async fetchJurnalData() {
                    try {
                        const response = await fetch('/api/jurnal');
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            this.allData = data;
                            this.totalRows = data.length;
                            this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                        } else {
                            console.error('Unexpected data format:', data);
                            alert('Error: Unexpected data format. Please check the data returned from the server.');
                        }
                    } catch (error) {
                        console.error('Error fetching Jurnal data:', error);
                        alert('Error fetching Jurnal data. Please try again later.');
                    }
                },
                renderJurnalTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchJurnalTable() {
                    this.renderJurnalTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderJurnalTable();
                },
                filteredData() {
                    return this.allData.filter(jurnal => {
                        const matchesCategory = (this.filter === 'all' || jurnal.jenis.toLowerCase() === this.filter.toLowerCase());
                        const matchesSearch = jurnal.keterangan.toLowerCase().includes(this.searchInput.toLowerCase());
                        return matchesCategory && matchesSearch;
                    });
                },

                init() {
                    this.fetchJurnalData();
                }
            }));
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
<?php /**PATH /var/www/hisabuna/resources/views/jurnal/index.blade.php ENDPATH**/ ?>