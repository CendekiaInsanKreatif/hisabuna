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
        $minYear = \Carbon\Carbon::parse(
            \Illuminate\Support\Facades\DB::table('coas')
                ->where('created_by', auth()->id())
                ->min('created_at') ?? now()
        )->year;

        $currentYear = \Carbon\Carbon::now()->year;
        $startYear = min($minYear, $currentYear - 10);
    ?>


    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900">Pengaturan Profil</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui data pengguna dan informasi perusahaan Anda.</p>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <!-- Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- Kolom Kiri: Profil Pengguna -->
                <section class="p-6 md:p-8 border-b lg:border-b-0 lg:border-r border-gray-100 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Profil Pengguna</h2>
                        <p class="mt-1 text-sm text-gray-500">Nama, email, dan kontak Anda.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo e(old('name', $user->name)); ?>"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo e(old('email', $user->email)); ?>"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- No HP -->
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">No HP</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" id="no_hp" name="no_hp" required
                                   placeholder="08xxxxxxxxxx"
                                   value="<?php echo e(old('no_hp', $user->no_hp)); ?>"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <p class="mt-1 text-xs text-gray-500">Hanya angka, tanpa spasi atau tanda baca.</p>
                            <?php $__errorArgs = ['no_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- No Telp -->
                        <div>
                            <label for="no_telp" class="block text-sm font-medium text-gray-700">No Telp</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" id="no_telp" name="no_telp" required
                                   placeholder="0274xxxxxxx"
                                   value="<?php echo e(old('no_telp', $user->no_telp)); ?>"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <?php $__errorArgs = ['no_telp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </section>

                <!-- Kolom Kanan: Profil Perusahaan -->
                <section class="p-6 md:p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Profil Perusahaan</h2>
                        <p class="mt-1 text-sm text-gray-500">Logo, nama, dan periode akuntansi.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Logo Perusahaan -->
                        <div x-data="{
                                preview: '<?php echo e($user->company_logo ? asset('storage/'.$user->company_logo) : ''); ?>',
                                fileChosen(e){ const f=e.target.files[0]; if(!f) return; const r=new FileReader(); r.onload = ev => this.preview = ev.target.result; r.readAsDataURL(f); }
                            }"
                            class="space-y-3">
                            <label for="company_logo" class="block text-sm font-medium text-gray-700">Logo Perusahaan</label>

                            <div class="flex items-start gap-4">
                                <div class="w-24 h-24 rounded-xl ring-1 ring-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                                    <template x-if="preview">
                                        <img :src="preview" alt="Preview Logo" class="w-full h-full object-contain" />
                                    </template>
                                    <template x-if="!preview">
                                        <span class="text-xs text-gray-400">No Logo</span>
                                    </template>
                                </div>

                                <div class="flex-1">
                                    <input
                                        id="company_logo"
                                        name="company_logo"
                                        type="file"
                                        accept=".jpg,.jpeg,.png"
                                        @change="fileChosen"
                                        class="block w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:ring-1 file:ring-gray-200 file:bg-white file:text-gray-700 hover:file:bg-gray-50 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    >
                                    <p class="mt-2 text-xs text-gray-500">PNG/JPG, disarankan rasio kotak, maksimal ~1MB.</p>
                                    <?php $__errorArgs = ['company_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Nama Perusahaan -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input type="text" id="company_name" name="company_name" required
                                   value="<?php echo e(old('company_name', auth()->user()->company_name)); ?>"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                            <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Periode Akuntansi -->
                        <div>
                            <label for="periode" class="block text-sm font-medium text-gray-700">Periode Akuntansi</label>
                            <select id="periode" name="periode" required
                                    class="mt-1 block w-full rounded-xl border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5">
                                <?php for($year = $startYear; $year <= $currentYear; $year++): ?>
                                    <option value="<?php echo e($year); ?>" <?php echo e((int)$year === (int)old('periode', auth()->user()->periode) ? 'selected' : ''); ?>>
                                        <?php echo e($year); ?>

                                    </option>
                                <?php endfor; ?>
                            </select>
                            <?php $__errorArgs = ['periode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <p class="mt-1 text-xs text-gray-500">Tahun berjalan untuk laporan & penomoran.</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500">Pastikan data sudah benar sebelum menyimpan.</p>
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(url()->previous()); ?>" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 bg-white ring-1 ring-gray-200 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('script'); ?>
    <script type="module">
        // Sanitasi angka untuk HP & Telp
        const onlyDigits = (el) => el.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        const hp = document.getElementById('no_hp');
        const telp = document.getElementById('no_telp');
        if (hp) onlyDigits(hp);
        if (telp) onlyDigits(telp);
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