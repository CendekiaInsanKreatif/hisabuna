<?php $__env->startSection('content'); ?>
    <section class="pricing flex bg-superlight gap-5 overflow-x-scroll px-5 md:px-16 snap-x">
        <?php
            $plans = [
                [
                    'name' => 'Standard',
                    'price' => '500.000',
                    'features' => ['Neraca', 'Laba Rugi', 'Arus Kas', 'Neraca Saldo', 'Buku Besar'],
                    'buttonText' => 'Pesan',
                    'buttonLink' => 'https://app.hisabuna.id/register'
                ],
                [
                    'name' => 'Pro',
                    'price' => '750.000',
                    'features' => ['Mencetak Voucher RV, PV, JV', 'Komparasi Laporan Keuangan', 'Upload Bukti Transaksi'],
                    'buttonText' => 'Pesan',
                    'buttonLink' => 'https://app.hisabuna.id/register'
                ],
                [
                    'name' => 'Enterprise (Coming Soon)',
                    'price' => '1.000.000',
                    'features' => ['Kelengkapan Audit', 'Analisa Laporan Keuangan', 'Cash Opname', 'Kuitansi', 'Anggaran', 'Laporan Dalam Dua Bahasa'],
                    'buttonText' => 'Pesan',
                    'buttonLink' => 'https://app.hisabuna.id/register'
                ]
            ];
        ?>

        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tier bg-white p-4 rounded-xl flex flex-col gap-5 min-w-[340px] w-full snap-center justify-between" data-tier="<?php echo e($plan['name']); ?>">
                <div>
                    <h3 class="text-2xl text-emerald-800"><?php echo e($plan['name']); ?></h3>
                    <p class="text-emerald-600 font-medium pb-3 border-b border-slate-300 text-xl"><?php echo e($plan['price']); ?> <span class="text-sm text-gray-500">/bulan</span></p>
                    <p class="my-2"><?php echo e($loop->first ? 'Fitur dasar:' : 'Semua fitur yang ada pada ' . $plans[$loop->index - 1]['name'] . ', ditambah:'); ?></p>
                    <ul class="flex flex-col gap-2">
                        <?php $__currentLoopData = $plan['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3">
                                <div class="w-4 h-4">
                                    <svg width="100%" height="100%" viewBox="0 0 24 24" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                        <g transform="matrix(1,0,0,1,-533.063,-97.2763)">
                                            <g transform="matrix(0.163703,0,0,0.163703,445.799,81.3519)">
                                                <g transform="matrix(1,0,0,1,318.046,0)">
                                                    <path d="M288.321,97.276C328.805,97.276 361.624,130.095 361.624,170.58L361.624,170.58C361.624,211.064 328.805,243.883 288.321,243.883C288.137,243.883 287.954,243.883 287.771,243.883C247.59,243.883 215.017,211.31 215.017,171.13L215.017,170.03C215.017,129.849 247.59,97.276 287.771,97.276C287.954,97.276 288.137,97.276 288.321,97.276Z" style="fill:rgb(18,185,129);"></path>
                                                </g>
                                                <g id="ic_check_24px" transform="matrix(4.52559,0,0,4.52559,551.132,114.938)">
                                                    <path d="M9,16.17L4.83,12L3.41,13.41L9,19L21,7L19.59,5.59L9,16.17Z" style="fill:rgb(236,254,245);fill-rule:nonzero;"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                                <p class="text-base"><?php echo e($feature); ?></p>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo e($plan['buttonLink']); ?>" class="w-full">
                        <button class="order-btn bg-emerald-600 w-full text-white rounded-full py-3">
                            Upgrade
                        </button>
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/upgrade/index.blade.php ENDPATH**/ ?>