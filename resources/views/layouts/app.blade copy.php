<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/icons/hisabuna-favicon.png') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- custom style --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>


    <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}">
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


        @keyframes shakeOnLoad {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }


        @keyframes shakeOnHover {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }


        .animate-shake-on-load {
            animation: shakeOnLoad 0.5s ease-in-out 1;
        }


        .animate-shake-on-hover:hover {
            animation: shakeOnHover 0.5s ease-in-out infinite;
        }


    </style>
</head>

<body class="flex flex-col min-h-screen">
    <main class="flex-grow">
        {{-- @if (session('message'))
            @php
                $color = session('color') == 'green' ? 'emerald' : 'red';
            @endphp
            <div class="alert-container">
                <div id="s_alert"
                    class="alert bg-{{ $color }}-500 text-white text-center p-4 rounded-lg shadow-md w-full max-w-2xl transition-opacity duration-500 ease-out">
                    {{ session('message') }}
                </div>
            </div>
        @endif --}}
        <div class="header bg-gray-100 text-gray-800 p-4 sticky top-0 flex items-center justify-between z-10">
            <div class="flex justify-between w-full">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <img src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Logo Hisabuna"
                                class="h-8 w-auto">
                        </a>
                    </div>

                    <button id="btnSidebar" class="bg-transparent hover:bg-emerald-500 p-2 px-4 rounded">
                        <span id="menuIcon" class="menu-icon">=</span>
                    </button>
                </div>

                <div id="btnProfile" class="flex items-center space-x-2 md:space-x-4">
                    <div>
                        <p class="text-lg md:text-xl text-center font-bold">{{ Auth::user()->company_name }}</p>
                        <p class="text-xs md:text-sm text-center">{{ Auth::user()->email }}</p>
                    </div>


                    @php
                        $profile = auth()->user()->profile;
                        $subscriptionEnd = auth()->user()->subscribed_until;
                        $isExpiringSoon = $subscriptionEnd && \Carbon\Carbon::parse($subscriptionEnd)->diffInDays(now()) <= 30;
                    @endphp

                    @if($profile === 'trial')

                        <a href="{{ route('subscription.upgrade') }}"
                        class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white font-semibold px-4 py-2 rounded-full text-sm transition duration-300 ml-4 shadow-md animate-shake-on-load animate-shake-on-hover">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 6l5 5 5-9 5 9 5-5v13H2V6zm2 11h16v2H4v-2z"/>
                            </svg>
                            <span>Upgrade</span>
                        </a>

                    @elseif(in_array($profile, ['standard', 'pro']))

                        @if($isExpiringSoon)
                            <a href="{{ route('subscription.renew') }}"
                            class="flex items-center gap-2 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:to-yellow-600 text-white font-semibold px-4 py-2 rounded-full text-sm transition duration-300 ml-4 shadow-md animate-shake-on-load animate-shake-on-hover">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 6l5 5 5-9 5 9 5-5v13H2V6zm2 11h16v2H4v-2z"/>
                                </svg>
                                <span>Renewal</span>
                            </a>
                        @else

                            <a href="{{ route('subscription.upgrade') }}"
                            class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white font-semibold px-4 py-2 rounded-full text-sm transition duration-300 ml-4 shadow-md animate-shake-on-load animate-shake-on-hover">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 6l5 5 5-9 5 9 5-5v13H2V6zm2 11h16v2H4v-2z"/>
                                </svg>
                                <span>Upgrade</span>
                            </a>
                        @endif
                    @endif

                    <a href="{{ route('profile.edit') }}"
                        class="overflow-hidden hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('storage/' . Auth::user()->company_logo) }}" alt="User"
                            class="w-15 h-10 rounded-md object-contain">
                    </a>
                </div>
            </div>
        </div>


        @include('layouts.navigation')
        <div class="content shifted p-2 w-full md:w-auto" id="content">
            {{-- @if(auth()->user()->profile == 'trial')
                <div style="background-color: #fde047" class="trial-alert flex flex-col md:flex-row justify-center items-center p-1 rounded-sm shadow">
                    <p class="text-center text-gray-800 font-medium">Sisa waktu trial Anda adalah {{ $trial_ends_in_days }} hari !&nbsp;</p>
                    <a href="{{ route('upgrade.index') }}" class="p-1 px-2 bg-emerald-500 text-white rounded-md font-medium">
                        Upgrade
                    </a>
                    <p class="text-center text-gray-800 font-medium">&nbsp;untuk menghindari gangguan layanan, segera lakukan upgrade.</p>
                </div>
            @endif --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}

                    @if(session('invoice_download'))
                        <div class="mt-2">
                            <a href="{{ route('invoice.download', basename(session('invoice_download'))) }}"
                            class="btn btn-sm btn-outline-primary">
                                📄 Download Invoice
                            </a>
                        </div>
                    @endif
                </div>
            @endif
            @yield('content')
        </div>
        <div class="overlay fixed inset-0 bg-black bg-opacity-0" id="overlay"
            style="display: none; justify-content: center; align-items: center;">
            @include('sweetalert::alert')
            <img src="{{ asset('images/loading-spinner.gif') }}" height="150" width="150" alt="Loading Spinner">
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
    @stack('script')
</body>

</html>
