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
        @media (min-width: 769px) {
            .content.shifted {
                margin-left: 300px;
            }
        }
        @media (min-width: 1025px) {
            .sidebar {
                max-width: 20%;
            }
            .content.shifted {
                margin-left: 20%;
            }
        }

        body {
            overflow: hidden;
        }

        .overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="header bg-gray-100 text-gray-800 p-4 sticky top-0 flex items-center z-10">
        <div class="flex-grow-0">
            <img src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Logo Hisabuna" style="height: 50px; width: 150px;">
        </div>
        <div class="flex-grow-0" style="margin-left: 63px;">
            <button onclick="toggleSidebar()" class="bg-transparent hover:bg-emerald-500 p-2 rounded w-10">
                <span id="menuIcon" class="menu-icon">=</span>
            </button>
        </div>
        <div class="flex-grow ml-5 flex items-center gap-2">
            <div class="w-12 h-12 relative rounded-lg overflow-hidden">
                <img src="{{ asset('images/dummy-company.png') }}" alt="User" class="w-full h-full">
            </div>
            <div>
                <p class="text-xs">PROJECT</p>
                <p class="text-lg"><b>AL</b></p>
            </div>
        </div>
        <div class="profile flex-grow-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-transparent hover:bg-emerald-500 p-2 rounded w-10">
                    Logout
                </button>
            </form>
        </div>
    </div>
    @include('layouts.navigation')
    <div class="content p-4 mx-auto" id="content" style="padding-bottom: 64px;">
        @yield('content')
    </div>
    <div class="footer bg-emerald-500 text-white text-center p-1 fixed bottom-0 w-full">
        © 2024 Hisabuna. All rights reserved.
    </div>
    <div class="overlay fixed inset-0 bg-black bg-opacity-0" id="overlay" style="display: none; justify-content: center; align-items: center;">
        <img src="{{ asset('images/loading-spinner.gif') }}" height="150" width="150" alt="Loading Spinner">
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const menuIcon = document.getElementById('menuIcon');
            sidebar.classList.toggle('open');
            content.classList.toggle('shifted');
            if (sidebar.classList.contains('open')) {
                menuIcon.textContent = '<';
            } else {
                menuIcon.textContent = '=';
            }
        }

        function showAlert() {
            document.getElementById('overlay').classList.remove('hidden');
        }

        function closeAlert() {
            document.getElementById('overlay').classList.add('hidden');
        }

        function checkScreenSize() {
            if (window.innerWidth >= 769) {
                document.getElementById('sidebar').classList.add('open');
                document.getElementById('content').classList.add('shifted');
                document.getElementById('menuIcon').textContent = '<';
            } else {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('content').classList.remove('shifted');
                document.getElementById('menuIcon').textContent = '=';
            }
        }

        window.addEventListener('resize', checkScreenSize);
        document.addEventListener('DOMContentLoaded', checkScreenSize);
    </script>
    @stack('script')
</body>
</html>
