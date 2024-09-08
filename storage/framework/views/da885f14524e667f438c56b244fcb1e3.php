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
                    'name' => 'name',
                    'type' => 'text',
                    'label' => 'Nama User',
                    'required' => true,
                ],
                [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email',
                    'required' => true,
                ],
                [
                    'name' => 'no_hp',
                    'type' => 'number',
                    'label' => 'Nomor HP',
                    'required' => true,
                ],
                [
                    'name' => 'no_telp',
                    'type' => 'number',
                    'label' => 'Nomor Telepon',
                    'required' => false,
                ],
                [
                    'name' => 'company_name',
                    'type' => 'text',
                    'label' => 'Nama Perusahaan',
                    'required' => true,
                ],
                [
                    'name' => 'company_logo',
                    'type' => 'file',
                    'label' => 'Logo Perusahaan',
                    'required' => true,
                ],
                [
                    'name' => 'periode',
                    'type' => 'number',
                    'label' => 'Periode',
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'type' => 'password',
                    'label' => 'Password',
                    'required' => true,
                ],
            ];
        ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['field' => $fields,'maxWidth' => '2xl','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fields),'maxWidth' => '2xl','focusable' => true]); ?>
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
        <div class="container mx-auto px-4" x-data="userTable">
            <div class="mb-6">
                <p class="text-2xl font-semibold text-emerald-500">Akun Pengguna</p>
            </div>
            <div class="card bg-white shadow-lg rounded-xl border border-gray-200 p-2 w-full md:w-auto">
                <div class="container mx-auto p-4">
                    <div class="flex flex-wrap gap-4 md:gap-6 items-center">
                        <div class="cari flex items-center space-x-2 mt-4 md:mt-0">
                            <label for="cari" class="text-sm font-medium text-gray-900 dark:text-white">Cari:</label>
                            <div class="relative">
                                <input type="text" id="cari" x-model="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-200 pl-3 pr-10 py-1 w-full md:w-64 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-200" placeholder="Cari User..." @keydown.enter="searchUserTable">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <button class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4" x-on:click.prevent="$dispatch('open-modal', { route: '<?php echo e(route('users.store')); ?>', name: 'users.create', title: 'Tambah User', type: 'form' })">
                                Tambah User
                            </button>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <label for="status" class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                            <select id="status" x-model="selectedStatus" @change="handleStatusChange(selectedStatus)" class="ml-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-200">
                                <option value="all">Semua</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="flex items-center mt-4 md:mt-0">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total User: </span>
                            <span class="ml-2 text-lg font-semibold text-emerald-500" x-text="filteredData().length"></span>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-x-auto mt-1">
                    <table class="w-full min-w-full" id="userTable">
                        <thead>
                            <tr>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nama
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Email
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nomor HP
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Nama Perusahaan
                                        <span class="ml-2">
                                            <img src="<?php echo e(asset('images/icons/ic-sort.svg')); ?>" class="w-4 h-4 sort-icon" data-sort="none">
                                        </span>
                                    </div>
                                </th>
                                <th class="bg-gray-100 px-4 py-2 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer text-center">
                                    <div class="flex items-center justify-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <template x-for="user in paginatedData" :key="user.id">
                                <tr @mouseover="hover = true" @mouseout="hover = false" class="cursor-pointer hover:bg-gray-100" x-on:click.prevent="$dispatch('open-modal', { route: `<?php echo e(route('users.show', '')); ?>/${user.id}`, name: 'users.show', title: 'Lihat User', data: user, type: 'form' })">
                                    <td class="text-left px-4 py-1" x-text="user.name"></td>
                                    <td class="text-left px-4 py-1" x-text="user.email"></td>
                                    <td class="text-left px-4 py-1" x-text="user.no_hp"></td>
                                    <td class="text-left px-4 py-1" x-text="user.company_name"></td>
                                    <td class="text-left px-4 py-1 items-center text-center mt-1">
                                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'w-full md:w-auto lg:w-auto md:mt-0 mt-1','xOn:click.prevent.stop' => '$dispatch(\'open-modal\', { route: `'.e(route('users.update', '')).'/${user.id}`, name: \'users.update\', title: \'Edit User\', data: user, method: \'PUT\', type: \'form\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full md:w-auto lg:w-auto md:mt-0 mt-1','x-on:click.prevent.stop' => '$dispatch(\'open-modal\', { route: `'.e(route('users.update', '')).'/${user.id}`, name: \'users.update\', title: \'Edit User\', data: user, method: \'PUT\', type: \'form\' })']); ?><?php echo e(__('Edit')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'w-full md:w-auto lg:w-auto md:mt-0 mt-1','xOn:click.prevent.stop' => '$dispatch(\'open-modal\', { route: `'.e(route('users.destroy', '')).'/${user.id}`, name: \'users.destroy\', title: \'Hapus User\', data: user, method: \'DELETE\', type: \'delete\' })']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full md:w-auto lg:w-auto md:mt-0 mt-1','x-on:click.prevent.stop' => '$dispatch(\'open-modal\', { route: `'.e(route('users.destroy', '')).'/${user.id}`, name: \'users.destroy\', title: \'Hapus User\', data: user, method: \'DELETE\', type: \'delete\' })']); ?><?php echo e(__('Delete')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('userTable', () => ({
                currentPage: 1,
                rowsPerPage: 7,
                totalRows: 0,
                totalPage: 0,
                sortDirection: 'asc',
                filter: 'all',
                searchInput: '',
                allData: [],
                hover: false,
                selectedStatus: 'all',
                get paginatedData() {
                    const filteredData = this.filteredData();
                    const start = (this.currentPage - 1) * this.rowsPerPage;
                    const end = start + this.rowsPerPage;
                    return filteredData.slice(start, end);
                },
                get pagesToShow() {
                    const startPage = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
                    const endPage = Math.min(startPage + 4, this.totalPage);
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
                async fetchUserData() {
                    const overlay = document.getElementById('overlay');
                    overlay.style.display = 'flex';
                    try {
                        const response = await fetch('/api/users');
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
                        console.error('Error fetching User data:', error);
                        alert('Error fetching User data. Please try again later.');
                    } finally {
                        overlay.style.display = 'none';
                    }
                },
                renderUserTable() {
                    const filteredData = this.filteredData();
                    this.totalRows = filteredData.length;
                    this.totalPage = Math.ceil(this.totalRows / this.rowsPerPage);
                    this.changePage(1);
                },
                searchUserTable() {
                    this.renderUserTable();
                },
                filterCategory(category) {
                    this.filter = category;
                    this.renderUserTable();
                },
                filteredData() {
                    return this.allData.filter(user => {
                        const matchesSearch = user.email.toLowerCase().includes(this.searchInput.toLowerCase()) || user.name.toLowerCase().includes(this.searchInput.toLowerCase()) || user.company_name.toLowerCase().includes(this.searchInput.toLowerCase());
                        const matchesStatus = this.filter === 'all' ? true : user.is_active === this.filter;
                        return matchesSearch && matchesStatus;
                    });
                },
                handleStatusChange(status) {
                    this.filter = status; // Update filter based on selected status
                    this.renderUserTable(); // Re-render the user table with the new filter
                },
                init() {
                    this.fetchUserData();
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
<?php /**PATH /var/www/hisabuna/resources/views/users/index.blade.php ENDPATH**/ ?>