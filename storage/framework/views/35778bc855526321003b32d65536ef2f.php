<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Laporan <?php echo e($label); ?></title>
        <script src="<?php echo e(asset('js/paged_old.js')); ?>"></script>
        
        <style type="text/css">
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                max-width: 800px;
                margin: 0 auto;
            }

            h3 {
                margin: 0;
                padding: 0;
            }

            h2 {
                margin: 0;
                padding: 0;
            }

            p {
                margin: 0;
                padding: 0;
            }

            .amount {
                float: right;
                text-align: right;
                margin-left: 20px;
            }

            table {
                width: 100%;
            }

            .text-left {
                text-align: left;
            }

            .new-header {
                position: relative;
            }

            .company-logo {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100px;
            }

            @page {
                size: A4;
                margin: 30px;
                padding: 0;

                @bottom-center {
                    content: counter(page);
                }
            }

            /* #page-number-container {
                font-size: 12px;
                color: #000;
            }

            @media print {
                .page-number {
                    position: fixed;
                    bottom: 10px;
                    right: 10px;
                }
            } */

            /* @page {
            size: A4;
            margin: 30px;
            padding: 0;
            counter-reset: count 3;
            

            @bottom-center {
                content: counter(count);
                counter-increment: count 1;
            }
        } */
        
        </style>
        <script>
            // document.addEventListener('DOMContentLoaded', function() {
            //     // Mengambil jumlah halaman awal dari Blade (Laravel)
            //     let x = <?php echo e($paged['jumlahLaman']); ?>; // Blade syntax untuk memasukkan variabel dari backend

            //     // Menggunakan event 'page' dari Paged.js ketika halaman telah diproses
            //     PagedPolyfill.on('page', function(event) {
            //         document.querySelectorAll('.pagedjs_page').forEach((page, index) => {
            //             let pageNumber = x + index; // Menghitung nomor halaman
            //             let pageGet = page.querySelector('.pagedjs_margin-bottom .pagedjs_margin-bottom-center .pagedjs_margin-content');
            //             if (!pageGet) {
            //                 pageGet = document.createElement('div');
            //                 pageGet.classList.add('pagedjs_margin-content', 'page-number');
            //                 page.querySelector('.pagedjs_margin-bottom-center').appendChild(pageGet);
            //             }
            //             pageGet.textContent = pageNumber;
            //         });
            //     });
            //     PagedPolyfill.preview();
            // });

            // let number = <?php echo \Illuminate\Support\Js::from($paged['jumlahLaman'])->toHtml() ?>;
            // class MyHandler extends Paged.Handler {
            //     constructor(chunker, polisher, caller, layout) {
            //         super(chunker, polisher, caller, layout);
            //     }

            //     beforeParsed(pages) {
            //         console.log(pages)
            // pages.forEach((page, index) => {
            // console.log(`Nomor halaman: ${index + 1}`);
            // console.log(page.element.dataset.pageNumber)
            // page.element.innerHTML = `<p>Nomor halaman: ${index + 4}</p>`;
            // console.log(page.element)
            // });
            //     }
            // }
            // Paged.registerHandlers(MyHandler);
            // let path = <?php echo \Illuminate\Support\Js::from(base_path())->toHtml() ?>;
            // import vfrag from `${path}/node_modules/vfrag/src/index.js`;
            // console.log(`${path}/node_modules/vfrag/src/index.js`);
            // console.log(`${path}/node_modules/vfrag/src/index.js`);

            // let paged = new Previewer();
            // console.log(paged);
            // let flow = paged.preview(DOMContent,null,document.body).then((flow) => {
            //     console.log(flow.totalPages)
            // })
            // $(document).ready(function() {
            //     let totalPageCount = <?php echo \Illuminate\Support\Js::from($paged['jumlahLaman'])->toHtml() ?>;
            //     let page = document.querySelector('.pagedjs_pages');
            //     page.innerHTML = `Jumlah halaman: ${totalPageCount}`;
            // });
            // document.addEventListener('DOMContentLoaded', function() {
            //     let req = <?php echo \Illuminate\Support\Js::from($paged['jumlahLaman'])->toHtml() ?>;
            //     let page = document.getElementById('pageNumber');
            // });
            // $(document).ready(async function() {
            //     let pageNumber = <?php echo \Illuminate\Support\Js::from($paged['jumlahLaman'])->toHtml() ?>;
            //     console.log('Page number starts from:', pageNumber);

            //     // Set up IntersectionObserver
            //     let observer = new IntersectionObserver((entries) => {
            //         entries.forEach(entry => {
            //             if (entry.isIntersecting) {
            //                 let index = [...document.querySelectorAll('.pagedjs_sheet')].indexOf(entry.target);
            //                 let currentPageNumber = pageNumber + index;

            //                 // Cari elemen berdasarkan kelas yang mungkin berbeda per halaman
            //                 let pagenumElement = entry.target.querySelector('.pagedjs_margin-bottom')
            //                     || entry.target.querySelector('.pagedjs_margin-content')
            //                     || entry.target.querySelector('.pagedjs_footer'); // Tambahkan kelas lain jika perlu

            //                 if (pagenumElement) {
            //                     console.log(`Page ${index + 1} element found:`, pagenumElement);
            //                     pagenumElement.innerHTML = `<p>Page ${currentPageNumber}</p>`;
            //                 } else {
            //                     console.warn(`Page ${index + 1} element not found`);
            //                 }
            //             }
            //         });
            //     }, {
            //         rootMargin: '200px'
            //     });

            //     // Observasi semua elemen yang sudah ada
            //     document.querySelectorAll('.pagedjs_sheet').forEach(sheet => observer.observe(sheet));

            //     // Menggunakan MutationObserver untuk menangani penambahan elemen baru
            //     let mutationObserver = new MutationObserver(mutations => {
            //         mutations.forEach(mutation => {
            //             mutation.addedNodes.forEach(node => {
            //                 if (node.classList && node.classList.contains('pagedjs_sheet')) {
            //                     observer.observe(node);  // Mulai mengobservasi elemen yang baru
            //                     console.log('New page detected and observed:', node);
            //                 }
            //             });
            //         });
            //     });

            //     // Mengawasi perubahan pada body atau elemen container utama
            //     mutationObserver.observe(document.body, {
            //         childList: true,
            //         subtree: true
            //     });

            //     $('#btn-print').click(function() {
            //         setTimeout(() => {
            //             window.print();
            //         }, 300);
            //     });
            // });
        </script>
    </head>

    <body onload="window.print()">
        <header class="new-header">
            <img src="<?php echo e(asset('storage/' . auth()->user()->company_logo)); ?>" alt="Logo" class="company-logo" />
            <div class="header" style="text-align: center;">
                <h1><?php echo e(auth()->user()->company_name); ?></h1>
                <h2>Laporan Posisi Keuangan</h2>
                <h3>Per <?php echo e($periode); ?></h3>
            </div>
        </header>
        <hr style="border: 2px solid black; width: 100%;" />
        <table style="border-spacing: 6px 2px;">
            <thead>
                <tr>
                    <th style="width: <?php echo e(60/(count($data)+1)); ?>%;">&nbsp;</th>
                    <?php $__currentLoopData = array_keys($data); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th style="text-align: right; font-size: 18px; width: <?php echo e(20/(count($data)+1)); ?>%;"><?php echo e($year); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data[array_key_first($data)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level1 => $level1Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(is_array($level1Data)): ?>
                <tr>
                    <td colspan="<?php echo e(count($data) + 1); ?>">
                        <h3><?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                    </td>
                </tr>
                <?php $totalLevel1 = array_fill_keys(array_keys($data), 0); ?> <?php $__currentLoopData = $level1Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level2 => $level2Data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(is_array($level2Data)): ?>
                <tr>
                    <td colspan="<?php echo e(count($data) + 1); ?>">
                        <h3>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level2); ?></h3>
                    </td>
                </tr>
                <?php $totalLevel2 = array_fill_keys(array_keys($data), 0); ?> <?php $__currentLoopData = $level2Data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level3 => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo e($level3); ?></td>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $yearData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php $amount = $yearData[$level1][$level2][$level3] ?? 0; $totalLevel2[$year] += $amount; $totalLevel1[$year] += $amount; ?>
                    <td style="text-align: right;">
                        <?php echo e(number_format($amount, 0, ',', '.')); ?>

                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <h3>&nbsp;&nbsp;&nbsp;&nbsp; Jumlah <?php echo e($level2); ?></h3>
                    </td>
                    <?php $__currentLoopData = $totalLevel2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="text-align: right; border-top: 1px solid black; border-bottom: 1px solid black;">
                        <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <h3>JUMLAH <?php echo e($level1 == '1' ? 'ASET' : strtoupper($level1)); ?></h3>
                    </td>
                    <?php $__currentLoopData = $totalLevel1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="text-align: right; border-top: 2px double black; border-bottom: 2px double black;">
                        <h3><?php echo e(number_format($total, 0, ',', '.')); ?></h3>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <br />
                <?php endif; ?>
                <!-- <footer style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center;">
                        <p id="page-number"><?php echo e($paged['jumlahLaman']); ?></p>
                    </footer> -->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 70px;">
            <tr>
                <td style="text-align: center; width: 100%;">
                    <div style="font-size: 12px;"><?php echo e($paged['alamat']); ?>, <?php echo e($paged['tanggal']); ?></div>
                    <div style="height: 80px;"></div>
                    <div style="font-size: 12px;"><?php echo e($paged['dibuat']); ?></div>
                    <div style="border-bottom: 2px solid black; width: 100px; margin-left: auto; margin-right: auto;"></div>
                    <div style="font-size: 12px;"><?php echo e($paged['jabatan']); ?></div>
                </td>
            </tr>
        </table>

        <!-- Tambahkan lebih banyak halaman sesuai kebutuhan -->
        
        <script>
            // $(document).ready(function() {
            //     var hal = $('#halaman').text();
            //     console.log(hal)
            // })
            // var jumlahLaman = <?php echo e($paged['jumlahLaman']); ?>; // Ambil dari backend
            // var pageHeight = $(window).height(); // Tinggi halaman tampilan

            // function updatePageNumber() {
            //     var documentHeight = $(document).height(); // Tinggi total dokumen
            //     var totalPages = Math.ceil(documentHeight / pageHeight); // Hitung total halaman
            //     var currentPage = Math.floor($(window).scrollTop() / pageHeight) + 1; // Halaman saat ini

            //     // Jika halaman saat ini melebihi total halaman, set ke total halaman
            //     currentPage = Math.min(currentPage, totalPages);

            //     var finalPageNumber = jumlahLaman + currentPage - 1; // Update nomor halaman
            //     $('#halaman').text(finalPageNumber); // Menampilkan
            // }

            // // Memperbarui nomor halaman saat scroll
            // $(window).on('scroll', function() {
            //     updatePageNumber();
            // });

            // // Memperbarui nomor halaman sebelum dialog cetak
            // window.onbeforeprint = function() {
            //     updatePageNumber();
            // };

            // // Panggil fungsi untuk pertama kali saat halaman dimuat
            // $(document).ready(function() {
            //     updatePageNumber();
            // });
        </script>
    </body>
</html>
<?php /**PATH /var/www/trial_hisabuna/backend/resources/views/report/neraca.blade.php ENDPATH**/ ?>