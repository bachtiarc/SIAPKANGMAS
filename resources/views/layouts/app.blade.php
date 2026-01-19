<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAPKANGMAS') - Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Montserrat & Lato -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- reCAPTCHA -->
    {!! app('captcha')->renderJs() !!}
    
    <style>
        /* Font Configuration */
        body {
            font-family: 'Lato', sans-serif; 
        }
        
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Montserrat', sans-serif; 
        }
        
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
        
        .font-lato {
            font-family: 'Lato', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS Logo" class="h-10">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="font-montserrat text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
                        Beranda
                    </a>
                    <a href="{{ route('about') }}" class="font-montserrat text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
                        Tentang Kami
                    </a>
                    <a href="{{ route('contact') }}" class="font-montserrat text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
                        Kontak
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="font-montserrat bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="font-montserrat bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">
                            Daftar
                        </a>
                    @else
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->user_type == 'pegawai' ? route('user.dashboard') : route('masyarakat.dashboard')) }}" class="font-montserrat text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="font-montserrat text-gray-700 hover:text-red-600 px-3 py-2 text-sm font-medium">
                                Keluar
                            </button>
                        </form>
                    @endguest
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu (hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="font-montserrat block text-gray-700 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium">
                    Beranda
                </a>
                <a href="{{ route('about') }}" class="font-montserrat block text-gray-700 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium">
                    Tentang Kami
                </a>
                <a href="{{ route('contact') }}" class="font-montserrat block text-gray-700 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium">
                    Kontak
                </a>
                
                @guest
                    <a href="{{ route('login') }}" class="font-montserrat block text-orange-600 hover:bg-orange-50 px-3 py-2 rounded-md text-base font-medium">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="font-montserrat block text-blue-600 hover:bg-blue-50 px-3 py-2 rounded-md text-base font-medium">
                        Daftar
                    </a>
                @else
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->user_type == 'pegawai' ? route('user.dashboard') : route('masyarakat.dashboard')) }}" class="font-montserrat block text-gray-700 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium">
                        Dashboard
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="font-montserrat block w-full text-left text-red-600 hover:bg-red-50 px-3 py-2 rounded-md text-base font-medium">
                            Keluar
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600 text-sm mb-4 font-lato">
                2026 Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah
            </div>
            <div class="flex justify-center space-x-6 text-sm">
                <a href="#" class="font-montserrat text-blue-600 hover:text-blue-700">Kebijakan Privasi</a>
                <a href="#" class="font-montserrat text-blue-600 hover:text-blue-700">Syarat & Ketentuan</a>
                <a href="#" class="font-montserrat text-blue-600 hover:text-blue-700">Bantuan</a>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        // Toggle mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>