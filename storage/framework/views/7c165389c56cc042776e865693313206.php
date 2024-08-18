<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>


    <link rel="stylesheet" href="<?php echo e(asset('css/datepicker.css')); ?>">
    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('style'); ?>
    <style>
        .sidebar {
            position: fixed;
            top: 64px;
            left: 0;
            bottom: 0;
            width: 100%;
            max-width: 300px;
            background: #f3f4f6;
            padding: 1rem;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 10;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .header {
            height: 64px;
        }

        /* Base styles for btnProfile and btnSidebar */
        #btnProfile {
            display: none;
        }

        .flex #btnSidebar {
            margin-left: 40px;
        }

        /* Small devices (320px and up) */
        @media (min-width: 320px) {
            .flex #btnSidebar {
                margin-left: 40px;
            }
        }

        /* Small devices (375px and up) */
        @media (min-width: 375px) {
            .flex #btnSidebar {
                margin-left: 100px;
            }
        }

        /* Small devices (425px and up) */
        @media (min-width: 425px) {
            .flex #btnSidebar {
                margin-left: 150px;
            }
        }

        /* Medium devices (640px and up) */
        @media (min-width: 640px) and (max-width: 1023px) {
            #btnProfile {
                display: flex;
            }

            .flex #btnSidebar {
                margin-left: 90px;
            }
        }

        /* Large devices (768px and up) */
        @media (min-width: 768px) {
            .content.shifted {
                margin-left: 300px;
            }

            #btnProfile {
                display: flex;
            }

            .flex #btnSidebar {
                margin-left: 90px;
            }
        }

        /* Extra large devices (1024px and up) */
        @media (min-width: 1024px) {
            .sidebar {
                max-width: 20%;
            }

            .content.shifted {
                margin-left: 20%;
            }

            #btnProfile {
                display: flex;
            }

            .flex #btnSidebar {
                margin-right: 0;
            }
        }


        .overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            display: none;
        }

        .alert-container {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 100%;
            z-index: 9999;
            display: flex;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">
    <main class="flex-grow">
        <?php if(session('message')): ?>
            <?php
                $color = session('color') == 'green' ? 'emerald' : 'red';
            ?>
            <div class="alert-container">
                <div id="s_alert"
                    class="alert bg-<?php echo e($color); ?>-500 text-white text-center p-4 rounded-lg shadow-md w-full max-w-2xl transition-opacity duration-500 ease-out">
                    <?php echo e(session('message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <div class="header bg-gray-100 text-gray-800 p-4 sticky top-0 flex items-center justify-between z-10">
            <div class="flex justify-between w-full">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <div class="flex-shrink-0">
                        <img src="<?php echo e(asset('images/brand/logo-hisabuna-color.svg')); ?>" alt="Logo Hisabuna"
                            class="h-8 w-auto">
                    </div>

                    <button id="btnSidebar" class="bg-transparent hover:bg-emerald-500 p-2 px-4 rounded">
                        <span id="menuIcon" class="menu-icon">=</span>
                    </button>
                </div>

                <div id="btnProfile" class="flex items-center space-x-2 md:space-x-4">
                    <div>
                        <p class="text-lg md:text-xl text-center font-bold"><?php echo e(Auth::user()->company_name); ?></p>
                        <p class="text-xs md:text-sm text-center"><?php echo e(Auth::user()->email); ?></p>
                    </div>
                    <a href="<?php echo e(route('profile.edit')); ?>"
                        class="w-12 h-12 md:w-14 md:h-14 rounded-full overflow-hidden hover:scale-110 transition-transform duration-300">
                        <img src="<?php echo e(asset('storage/'. Auth::user()->company_logo)); ?>" alt="User"
                            class="w-full h-full object-cover">
                    </a>
                </div>
            </div>
        </div>


        <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="content shifted p-2 w-full md:w-auto" id="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <div class="overlay fixed inset-0 bg-black bg-opacity-0" id="overlay"
            style="display: none; justify-content: center; align-items: center;">
            <img src="<?php echo e(asset('images/loading-spinner.gif')); ?>" height="150" width="150" alt="Loading Spinner">
        </div>
    </main>
    <footer class="w-full text-right px-6 py-2 text-sm text-zinc-600">
        HISABUNA v1.0 © Copyright 2024 developed by PT. Insan Kreatif Cendekia.
    </footer>
    <script type="module">
        $('#s_alert').fadeOut(3000, function() {
            $(this).remove();
        });

        $('#btnSidebar').on('click', function() {
            const sidebar = $('#sidebar');
            const content = $('#content');
            const menuIcon = $('#menuIcon');
            sidebar.toggleClass('open');
            content.toggleClass('shifted');
            if (sidebar.hasClass('open')) {
                menuIcon.text('<');
            } else {
                menuIcon.text('=');
            }
        });

        function showAlert() {
            $('#overlay').removeClass('hidden');
        }

        function closeAlert() {
            $('#overlay').addClass('hidden');
        }

        function checkScreenSize() {
            if ($(window).width() >= 769) {
                $('#sidebar').addClass('open');
                $('#content').addClass('shifted');
                $('#menuIcon').text('<');
            } else {
                $('#sidebar').removeClass('open');
                $('#content').removeClass('shifted');
                $('#menuIcon').text('=');
            }
        }

        $(window).on('resize', checkScreenSize);
        $(document).ready(checkScreenSize);
    </script>
    <?php echo $__env->yieldPushContent('script'); ?>
</body>

</html>
<?php /**PATH /var/www/hisabuna/resources/views/layouts/app.blade.php ENDPATH**/ ?>