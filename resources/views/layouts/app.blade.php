<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MASSIVE') — MASSIVE</title>
    <meta name="description" content="MASSIVE - Platform Analisis Kondisi Bisnis UMKM">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.6/standalone/umd/vis-network.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/vis-network@9.1.6/styles/vis-network.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased flex flex-col min-h-screen overflow-x-hidden">
    {{-- Navbar --}}
    <nav class="bg-[#1e2d4a] sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="flex items-center justify-between h-16">
                {{-- KIRI: Logo --}}
                <a href="{{ auth()->check() ? route('dashboard') : '/' }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="14" width="3" height="8" rx="1"/>
                            <rect x="7" y="10" width="3" height="12" rx="1"/>
                            <rect x="12" y="6" width="3" height="16" rx="1"/>
                            <rect x="17" y="2" width="3" height="20" rx="1"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-lg tracking-wide">MASSIVE</span>
                </a>

                {{-- KANAN: Desktop Menu --}}
                <div class="hidden lg:flex items-center gap-1">
                    @auth
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all min-h-[40px] flex items-center {{ request()->routeIs('admin.*') ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Admin</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all min-h-[40px] flex items-center {{ request()->routeIs('dashboard') ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Dashboard</a>
                        <a href="{{ route('kuesioner') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all min-h-[40px] flex items-center {{ request()->routeIs('kuesioner') ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Kuesioner</a>
                        <a href="{{ route('modul') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all min-h-[40px] flex items-center {{ request()->routeIs('modul*') ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Modul</a>
                    @endif
                    <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all min-h-[40px] flex items-center {{ request()->routeIs('profile') ? 'text-white bg-white/15' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Profile</a>
                    @if(!auth()->user()?->isAdmin() && auth()->user()->nama_usaha)
                    <span class="hidden xl:flex items-center text-xs text-white/50 px-2 border-l border-white/10 ml-1">
                        {{ Str::limit(auth()->user()->nama_usaha, 20) }}
                    </span>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white/90 hover:bg-white/10 transition-all min-h-[40px]">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-md min-h-[40px] flex items-center">Login</a>
                    @endauth
                </div>

                {{-- Mobile: Hamburger --}}
                <button id="mobile-menu-btn" class="lg:hidden text-white/80 hover:text-white p-2 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg hover:bg-white/10 transition-all">
                    <svg id="hamburger-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="hamburger-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Dropdown --}}
        <div id="mobile-menu" class="lg:hidden hidden bg-[#1e2d4a] border-t border-white/10 px-4 py-3 space-y-1">
            @auth
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 min-h-[44px] flex items-center {{ request()->routeIs('admin.*') ? 'bg-white/15 text-white' : '' }}">Admin</a>
            @else
                <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 min-h-[44px] flex items-center {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : '' }}">Dashboard</a>
                <a href="{{ route('kuesioner') }}" class="block px-3 py-3 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 min-h-[44px] flex items-center {{ request()->routeIs('kuesioner') ? 'bg-white/15 text-white' : '' }}">Kuesioner</a>
                <a href="{{ route('modul') }}" class="block px-3 py-3 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 min-h-[44px] flex items-center {{ request()->routeIs('modul*') ? 'bg-white/15 text-white' : '' }}">Modul</a>
            @endif
            <a href="{{ route('profile') }}" class="block px-3 py-3 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 min-h-[44px] flex items-center {{ request()->routeIs('profile') ? 'bg-white/15 text-white' : '' }}">Profile</a>
            <hr class="border-white/10 my-2">
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block w-full text-left px-3 py-3 rounded-lg text-sm text-white/60 hover:text-white/90 hover:bg-white/10 min-h-[44px]">Logout</button></form>
            @else
            <a href="{{ route('login') }}" class="block px-3 py-3 rounded-lg text-sm text-blue-400 hover:bg-white/10 min-h-[44px] flex items-center font-semibold">Login</a>
            @endauth
        </div>
    </nav>

    {{-- Toast Notifications --}}
    @if(session('success'))
    <div id="flash-toast" class="animate-slide-down fixed top-20 right-4 z-[60] bg-green-500 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white text-lg leading-none">&times;</button>
    </div>
    @endif
    @if(session('error'))
    <div id="flash-toast-err" class="animate-slide-down fixed top-20 right-4 z-[60] bg-red-500 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white text-lg leading-none">&times;</button>
    </div>
    @endif
    @if(session('info'))
    <div id="flash-toast-info" class="animate-slide-down fixed top-20 right-4 z-[60] bg-blue-500 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('info') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white text-lg leading-none">&times;</button>
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-[#0f1a2e] text-gray-400 py-8 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 md:px-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-md bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="14" width="3" height="8" rx="1"/><rect x="7" y="10" width="3" height="12" rx="1"/><rect x="12" y="6" width="3" height="16" rx="1"/><rect x="17" y="2" width="3" height="20" rx="1"/>
                    </svg>
                </div>
                <span class="text-white font-bold">MASSIVE</span>
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} MASSIVE — Platform Analisis Kondisi Bisnis UMKM</p>
        </div>
    </footer>

    <script>
        // Hamburger toggle with icon swap
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            const menu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('hamburger-open');
            const closeIcon = document.getElementById('hamburger-close');
            menu?.classList.toggle('hidden');
            openIcon?.classList.toggle('hidden');
            closeIcon?.classList.toggle('hidden');
        });
        // Auto-dismiss toasts
        setTimeout(() => {
            document.getElementById('flash-toast')?.remove();
            document.getElementById('flash-toast-err')?.remove();
            document.getElementById('flash-toast-info')?.remove();
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
