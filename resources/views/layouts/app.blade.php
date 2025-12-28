<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Tagihan Internet'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


    <style>
        :root {
            --primary-color: #2A4156;
            --secondary-color: #F5EEEB;
            --accent-color: #F28C00;
        }

        body {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        /* Card hover */
        .menu-card {
            transition: all 0.3s ease;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.15);
        }

        /* Bottom menu hover animation */
        .bottom-menu a {
            transition: transform 0.2s, background-color 0.2s, color 0.2s;
            border-radius: 0.5rem;
        }
        .bottom-menu a:hover {
            transform: translateY(-4px);
            background-color: rgba(242,140,0,0.15);
        }
        .bottom-menu a.active {
            color: var(--accent-color);
            font-weight: 600;
            transform: translateY(-2px);
        }

        /* Navbar shadow */
        nav {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        footer {
            background-color: var(--primary-color);
            color: white;
        }
    </style>

    @stack('head')
</head>
<body class="flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-[var(--primary-color)] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold">@yield('app-name', 'Tagihan Internet')</div>



        <!-- Logout Desktop -->
        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
            @csrf
            <button type="submit"
                class="bg-[var(--accent-color)] px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                Logout
            </button>
        </form>
    </nav>

    <div class="flex flex-1 flex-col lg:flex-row">

 

        {{-- Konten utama --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 mt-auto">
        &copy; {{ date('Y') }} Tagihan Internet. All rights reserved.
    </footer>

    <!-- Bottom Menu Mobile -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-inner z-50 bottom-menu">
        @yield('bottom-menu')
    </div>

    <!-- Toggle Mobile Menu -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if(menuToggle && mobileMenu){
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Highlight active bottom menu
        const bottomLinks = document.querySelectorAll('.bottom-menu a');
        bottomLinks.forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
