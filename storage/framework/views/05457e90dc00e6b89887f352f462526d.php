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
        $minYear = Carbon\Carbon::parse(DB::table('coas')->where('created_by', auth()->user()->id)->min('created_at'))->year;
        $currentYear = Carbon\Carbon::now()->year;
    ?>
        <div class="container mx-auto px-2 py-2 rounded-lg bg-gray-100">
            <form action="<?php echo e(route('profile.update')); ?>" method="POST" class="bg-white rounded-lg shadow-lg w-full p-6 space-y-6" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div class="flex flex-wrap md:flex-nowrap">
                    <div class="md:w-1/2 p-6 space-y-6">
                        <h1 class="text-4xl font-bold text-gray-900">Profil Pengguna</h1>
                        <div class="space-y-4">
                            <label for="name" class="block text-sm font-medium text-gray-800">Nama</label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="email" class="block text-sm font-medium text-gray-800">Email</label>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="no_hp" class="block text-sm font-medium text-gray-800">No HP</label>
                            <input type="text" name="no_hp" id="no_hp" value="<?php echo e(old('no_hp', $user->no_hp)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="no_telp" class="block text-sm font-medium text-gray-800">No Telp</label>
                            <input type="text" name="no_telp" id="no_telp" value="<?php echo e(old('no_telp', $user->no_telp)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="md:w-1/2 p-6 space-y-6">
                        <h1 class="text-4xl font-bold text-gray-900">Profil Perusahaan</h1>
                        <div class="space-y-4">
                            <label for="profile_image" class="block text-sm font-medium text-gray-800">Logo Perusahaan</label>
                            <div class="flex items-center">
                                <input type="file" name="company_logo" id="profile_image" accept=".jpg,.png,.jpeg" class="block w-full px-4 py-3 file:border file:border-gray-400 file:rounded-lg file:text-sm file:font-medium file:bg-white file:shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <img id="profile_image_preview" src="<?php echo e(asset('storage/' . $user->company_logo)); ?>" alt="Preview Image" class="w-15 h-10 rounded-md object-contain">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label for="company_name" class="block text-sm font-medium text-gray-800">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="company_name" value="<?php echo e(auth()->user()->company_name); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="periode" class="block mb-2 text-sm font-medium text-gray-800 dark:text-white">Periode Akuntansi</label>
                            <select name="periode" id="periode" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500">
                                <?php for($year = date('Y')-1; $year <= $currentYear; $year++): ?>
                                    <option value="<?php echo e($year); ?>" <?php echo e($year == auth()->user()->periode ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 transition ease-in-out duration-150">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('script'); ?>
    <script type="module">
        $(document).ready(function() {
            $('#no_hp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            $('#no_telp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });


        $('#profile_image').change(function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $('#profile_image_preview');
                    preview.attr('src', e.target.result);
                    preview.removeClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        $('#company_logo').change(function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $('#company_logo_preview');
                    preview.attr('src', e.target.result);
                    preview.removeClass('hidden');
                };
                reader.readAsDataURL(file);
            }
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
<?php /**PATH /var/www/hisabuna/backend/resources/views/profile/edit.blade.php ENDPATH**/ ?>