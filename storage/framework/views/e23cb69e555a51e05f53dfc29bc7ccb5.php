<?php $__env->startSection('title', 'Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition mb-6">
    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
        </svg>
        Daftar Invoice
    </h2>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="invoices-table" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No Invoice</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($invoice->order_id); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($invoice->created_at->format('d M Y')); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700">Rp <?php echo e(number_format($invoice->amount, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3">
                            <div class="space-x-2">
                                
                                <?php if(isset($invoice) && file_exists(storage_path('app/' . $invoice->file_path))): ?>
                                <a href="<?php echo e(route('invoice.download', $invoice)); ?>"
                                    class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 transition"
                                    target="_blank">
                                        Download Invoice
                                    </a>
                                <?php endif; ?>

                                
                                <a href="<?php echo e(route('invoice.preview', $invoice->order_id)); ?>"
                                target="_blank"
                                class="inline-flex items-center px-3 py-1 rounded-lg border border-green-500 text-green-500 text-xs font-semibold hover:bg-green-500 hover:text-white transition">
                                    Preview Invoice
                                </a>


                            </div>
                        </td>

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                            Belum ada invoice.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#invoices-table').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari total _MAX_ data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/hisabuna/backend/resources/views/invoices/index.blade.php ENDPATH**/ ?>