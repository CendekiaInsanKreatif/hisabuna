<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="user-id" content="<?php echo e(auth()->check() ? auth()->id() : ''); ?>">

    <title><?php echo e(config('app.name', 'Hisabuna')); ?></title>
    <link rel="icon" href="<?php echo e(asset('images/icons/hisabuna-favicon.png')); ?>" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Custom styles -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/datepicker.css')); ?>">

    <!-- Vite Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('style'); ?>
</head>

<body class="h-full bg-gradient-to-br from-secondary-50 via-primary-50 to-info-50 font-sans antialiased">
    <div class="flex h-full">
        <!-- Header -->
        <header class="fixed top-0 right-0 left-0 z-30 bg-white/95 backdrop-blur-md border-b border-secondary-200/30 shadow-soft">
            <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                <!-- Left Section: Logo & Menu Button -->
                <div class="flex items-center gap-4">
                    <button id="btnSidebar"
                        class="lg:hidden flex items-center justify-center w-10 h-10 bg-primary-50 hover:bg-primary-500 text-primary-600 hover:text-white rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 shadow-soft hover:shadow-medium">
                        <span id="menuIcon" class="text-xl font-light">≡</span>
                    </button>

                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center group">
                            <img src="<?php echo e(asset('images/brand/logo-hisabuna-color.svg')); ?>" alt="Logo Hisabuna"
                                class="h-8 w-auto transition-transform duration-300 group-hover:scale-105">
                        </a>
                    </div>

                    <!-- Mobile Time Display - Hidden on lg+ screens -->
                    <div class="lg:hidden flex items-center gap-2 bg-primary-50 rounded-lg px-3 py-1.5 border border-primary-200/50">
                        <svg class="w-3 h-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-medium text-primary-800" id="mobile-jakarta-time"><?php echo e(now()->setTimezone('Asia/Jakarta')->format('H:i')); ?></span>
                    </div>
                </div>

                <!-- Right Section: Profile Info & Actions -->
                <div class="flex items-center gap-3 lg:gap-4">
                    <!-- Jakarta Time Clock -->
                    <div class="hidden lg:block text-center bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl px-4 py-2.5 border border-primary-200/50 shadow-soft hover:shadow-medium transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary-500 p-1.5 rounded-lg shadow-soft">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-primary-800 tracking-wider" id="jakarta-time"><?php echo e(now()->setTimezone('Asia/Jakarta')->format('H:i:s')); ?></p>
                                <p class="text-xs text-primary-600 font-medium"><?php echo e(now()->setTimezone('Asia/Jakarta')->format('d M Y')); ?> • WIB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Company Info - Hidden on small screens -->
                    <div class="hidden md:block text-right bg-white rounded-xl px-4 py-2.5 border border-secondary-200/50 shadow-soft">
                        <p class="text-sm font-semibold text-secondary-800 truncate max-w-48">
                            <?php echo e(Auth::user()->company_name); ?>

                        </p>
                        <p class="text-xs text-secondary-600 truncate max-w-48 flex items-center justify-end mt-0.5">
                            <svg class="w-3 h-3 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="m18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <?php echo e(Auth::user()->email); ?>

                        </p>
                    </div>

                    <?php
                        $profile = auth()->user()->profile;
                        $subscriptionEnd = auth()->user()->subscribed_until;
                        $isExpiringSoon = $subscriptionEnd && \Carbon\Carbon::parse($subscriptionEnd)->diffInDays(now()) <= 30;
                    ?>

                    <!-- Subscription Actions -->
                    <?php if($profile === 'trial'): ?>
                        <a href="<?php echo e(route('subscription.upgrade')); ?>"
                            class="flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all duration-300 shadow-emerald hover:shadow-emerald-lg transform hover:scale-105">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="hidden sm:inline">Upgrade Pro</span>
                            <span class="sm:hidden">Upgrade</span>
                        </a>
                    <?php elseif(in_array($profile, ['standard', 'pro'])): ?>
                        <?php if($isExpiringSoon): ?>
                            <a href="<?php echo e(route('subscription.renew')); ?>"
                                class="flex items-center gap-2 bg-gradient-to-r from-warning-500 to-warning-600 hover:from-warning-600 hover:to-warning-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="hidden sm:inline">Renew Now</span>
                                <span class="sm:hidden">Renew</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('subscription.upgrade')); ?>"
                                class="flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all duration-300 shadow-emerald hover:shadow-emerald-lg transform hover:scale-105">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <span class="hidden sm:inline">Upgrade Pro</span>
                                <span class="sm:hidden">Upgrade</span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Profile Avatar -->
                    <a href="<?php echo e(route('profile.edit')); ?>"
                        class="flex-shrink-0 group relative">
                        <div class="relative">
                            <img src="<?php echo e(asset('storage/' . Auth::user()->company_logo)); ?>" alt="User Avatar"
                                class="w-10 h-10 rounded-xl object-contain border-2 border-white shadow-medium group-hover:shadow-strong transition-all duration-300 bg-white group-hover:scale-105">
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-success-500 border-2 border-white rounded-full"></div>
                        </div>
                    </a>
                </div>
            </div>
        </header>


        <!-- Navigation Sidebar -->
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 bg-secondary-900/20 backdrop-blur-sm z-40 lg:hidden opacity-0 invisible transition-all duration-300" id="sidebarOverlay"></div>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64 pt-16 min-h-screen">
            <!-- Success Alert -->
            <?php if(session('success')): ?>
                <div class="alert alert-success bg-gradient-to-r from-success-50 to-success-100 border border-success-200 text-success-800 px-5 py-4 rounded-2xl m-4 mb-3 shadow-medium animate-slide-in">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-success-500 rounded-full flex items-center justify-center shadow-soft">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold"><?php echo e(session('success')); ?></p>
                            <?php if(session('invoice_download')): ?>
                                <div class="mt-3">
                                    <a href="<?php echo e(route('invoice.download', basename(session('invoice_download')))); ?>"
                                        class="inline-flex items-center gap-2 bg-success-500 hover:bg-success-600 text-white font-medium px-4 py-2 rounded-xl text-sm transition-all duration-300 shadow-soft hover:shadow-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download Invoice
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="p-4 lg:p-6">
                <div class="max-w-full">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </main>

    </div>

    <!-- Footer -->
    

    <!-- Loading Overlay -->
    <div class="overlay fixed inset-0 bg-secondary-900/80 backdrop-blur-sm z-50 flex items-center justify-center" id="overlay" style="display: none;">
        <?php echo $__env->make('sweetalert::alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="bg-white rounded-3xl p-8 shadow-2xl border border-secondary-200/50 max-w-sm mx-4 animate-scale-in">
            <div class="text-center">
                <!-- Custom Loading Animation -->
                <div class="relative mb-6">
                    <div class="w-20 h-20 mx-auto">
                        <!-- Multiple rotating rings -->
                        <div class="absolute inset-0 border-4 border-secondary-200 rounded-full opacity-30"></div>
                        <div class="absolute inset-0 border-4 border-primary-500 rounded-full border-t-transparent animate-spin"></div>
                        <div class="absolute inset-1 border-3 border-primary-300 rounded-full border-r-transparent animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>

                        <!-- Inner pulsing circle -->
                        <div class="absolute inset-3 bg-gradient-to-br from-primary-400 via-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-medium">
                            <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>

                        <!-- Outer glow effect -->
                        <div class="absolute inset-0 rounded-full bg-primary-500 opacity-20 animate-ping"></div>
                    </div>
                </div>

                <!-- Loading Text with Animation -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-secondary-800">Memuat Data</h3>
                    <div class="flex items-center justify-center gap-1">
                        <div class="w-2 h-2 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                    <p class="text-sm text-secondary-600">Harap tunggu sebentar...</p>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        // Success alert auto-hide
        if ($('.alert-success').length) {
            setTimeout(() => {
                $('.alert-success').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Sidebar toggle functionality
        $('#btnSidebar').on('click', function() {
            const sidebar = $('#sidebar');
            const menuIcon = $('#menuIcon');
            const overlay = $('#sidebarOverlay');

            // Toggle sidebar
            sidebar.toggleClass('-translate-x-full');

            // Handle mobile overlay
            if (sidebar.hasClass('-translate-x-full')) {
                overlay.removeClass('opacity-100 visible').addClass('opacity-0 invisible');
                menuIcon.text('≡');
            } else {
                overlay.removeClass('opacity-0 invisible').addClass('opacity-100 visible');
                menuIcon.text('×');
            }
        });

        // Close sidebar when clicking overlay
        $('#sidebarOverlay').on('click', function() {
            const sidebar = $('#sidebar');
            const menuIcon = $('#menuIcon');

            sidebar.addClass('-translate-x-full');
            menuIcon.text('≡');
            $(this).removeClass('opacity-100 visible').addClass('opacity-0 invisible');
        });

        // Close sidebar on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = $('#sidebar');
                if (!sidebar.hasClass('-translate-x-full')) {
                    $('#sidebarOverlay').trigger('click');
                }
            }
        });

        // Loading overlay functions
        window.showAlert = function() {
            const overlay = $('#overlay');
            overlay.show();
            $('body').addClass('overflow-hidden');
        }

        window.closeAlert = function() {
            const overlay = $('#overlay');
            overlay.hide();
            $('body').removeClass('overflow-hidden');
        }

        // Show loading on page transitions
        $(document).on('click', 'a:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not([data-no-loading])', function(e) {
            const href = $(this).attr('href');
            if (href && (href.includes('download') || href.startsWith('http') || href.startsWith('mailto'))) {
                return;
            }

            setTimeout(() => {
                if (!window.location.href.includes(href)) {
                    showAlert();
                }
            }, 100);
        });

        // Hide loading when page loads
        $(window).on('load', function() {
            closeAlert();
        });

        // Initialize responsive behavior
        function initResponsive() {
            const screenWidth = $(window).width();
            const sidebar = $('#sidebar');
            const overlay = $('#sidebarOverlay');

            if (screenWidth >= 1024) {
                // Desktop: Always show sidebar
                sidebar.removeClass('-translate-x-full');
                overlay.removeClass('opacity-100 visible').addClass('opacity-0 invisible');
            } else {
                // Mobile: Hide sidebar by default
                sidebar.addClass('-translate-x-full');
                overlay.removeClass('opacity-100 visible').addClass('opacity-0 invisible');
            }
        }

        // Initialize and handle window resize
        $(window).on('resize', initResponsive);
        $(document).ready(initResponsive);

        // Jakarta Time Clock Update
        function updateJakartaTime() {
            const now = new Date();

            // Convert to Jakarta time (UTC+7)
            const jakartaTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));

            // Format full time (HH:MM:SS)
            const timeString = jakartaTime.toLocaleTimeString('id-ID', {
                hour12: false,
                timeZone: 'UTC'
            });

            // Format short time for mobile (HH:MM)
            const shortTimeString = jakartaTime.toLocaleTimeString('id-ID', {
                hour12: false,
                timeZone: 'UTC',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Format server time with timezone
            const serverTimeString = jakartaTime.toLocaleTimeString('id-ID', {
                hour12: false,
                timeZone: 'UTC'
            }) + ' WIB';

            // Update elements
            $('#jakarta-time').text(timeString);
            $('#mobile-jakarta-time').text(shortTimeString);
            $('#server-time').text(serverTimeString);
        }

        // Update time immediately and then every second
        updateJakartaTime();
        setInterval(updateJakartaTime, 1000);
    </script>
    <?php echo $__env->yieldPushContent('script'); ?>
</body>

</html>
<?php /**PATH /var/www/hisabuna/backend/resources/views/layouts/app.blade.php ENDPATH**/ ?>