<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('style')
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
        @if(session('message'))
        @php
            $color = session('color') == 'green' ? 'emerald' : 'red';
        @endphp
        <div class="alert-container">
            <div id="s_alert" class="alert bg-{{ $color }}-500 text-white text-center p-4 rounded-lg shadow-md w-full max-w-2xl transition-opacity duration-500 ease-out">
                {{ session('message') }}
            </div>
        </div>
    @endif
    <div class="header bg-gray-100 text-gray-800 p-4 sticky top-0 flex items-center justify-between z-10">
        <div class="flex justify-between w-full">
            <div class="flex items-center space-x-4 sm:space-x-6">
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Logo Hisabuna" class="h-9 w-auto">
                </div>

                <button id="btnSidebar" class="bg-transparent hover:bg-emerald-500 p-2 px-4 rounded">
                    <span id="menuIcon" class="menu-icon">=</span>
                </button>
            </div>

            <div id="btnProfile" class="flex items-center space-x-2">
                <div>
                    <p class="text-xs text-center">PROJECT</p>
                    <p class="text-lg text-center font-bold">AL</p>
                </div>
                <div class="w-12 h-12 rounded-full overflow-hidden">
                    <img src="{{ asset('images/dummy-company.png') }}" alt="User" class="w-full h-full">
                </div>
            </div>
        </div>
    </div>


    @include('layouts.navigation')
    <div class="content shifted p-2 w-full md:w-auto" id="content">
        @yield('content')
    </div>
    <div class="overlay fixed inset-0 bg-black bg-opacity-0" id="overlay" style="display: none; justify-content: center; align-items: center;">
        <img src="{{ asset('images/loading-spinner.gif') }}" height="150" width="150" alt="Loading Spinner">
    </div>
    </main>
    <footer class="w-full mt-auto">
        <div class="footer bg-emerald-500 text-white text-center p-1 w-full">
            <div class="w-full flex justify-center items-center">
                © 2024 Hisabuna. All rights reserved.
            </div>
        </div>
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
    @stack('script')
</body>
</html>
