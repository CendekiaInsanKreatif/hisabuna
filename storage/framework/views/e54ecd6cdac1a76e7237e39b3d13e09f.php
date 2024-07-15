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
        // da(old('name'));
    ?>
    <?php $__env->startSection('content'); ?>
        <div class="container mx-auto px-2 py-2 rounded-lg bg-gray-100">
            <form action="<?php echo e(route('profile.update')); ?>" method="POST" class="bg-white rounded-lg shadow-lg w-full p-6 space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <div class="flex flex-wrap md:flex-nowrap">
                    <div class="md:w-1/2 p-6 space-y-6">
                        <h1 class="text-4xl font-bold text-gray-900">Profil Pengguna</h1>
                        <div class="space-y-4">
                            <label for="profile_image" class="block text-sm font-medium text-gray-800">Gambar Profil</label>
                            <div class="flex items-center">
                                <input type="file" name="profile" id="profile_image" class="block w-full px-4 py-3 file:border file:border-gray-400 file:rounded-lg file:text-sm file:font-medium file:bg-white file:shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <img id="profile_image_preview" src="<?php echo e(asset('storage/' . $user->profile)); ?>" alt="Preview Image" class="hidden w-20 h-20 rounded-md object-cover ml-4">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label for="name" class="block text-sm font-medium text-gray-800">Nama</label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div class="space-y-4">
                            <label for="email" class="block text-sm font-medium text-gray-800">Email</label>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>" required class="mt-1 block w-full px-4 py-3 border border-gray-400 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
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
<?php /**PATH /var/www/hisabuna/resources/views/profile/edit.blade.php ENDPATH**/ ?>