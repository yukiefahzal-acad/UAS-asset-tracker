<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AssetTrack') }} - @yield('title', 'Sistem Manajemen Aset & Inventaris')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-gray-900 bg-slate-50 flex flex-col selection:bg-blue-500 selection:text-white">

    <!-- Top Corporate Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('assets.index') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:bg-blue-700 transition">
                            <i data-lucide="qr-code" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight text-gray-900 flex items-center gap-1.5">
                                Asset<span class="text-blue-600">Track</span>
                            </span>
                            <!-- <p class="text-[10px] font-medium tracking-wider text-gray-400 uppercase -mt-1">Corporate Inventory</p> -->
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                @auth
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('assets.index') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition flex items-center gap-2 {{ request()->routeIs('assets.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                        Daftar Aset
                    </a>
                    
                    <a href="{{ route('borrowings.index') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition flex items-center gap-2 {{ request()->routeIs('borrowings.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                        Peminjaman Aktif
                    </a>

                    <a href="{{ route('reports.index') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition flex items-center gap-2 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Laporan Akhir Tahun
                    </a>

                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-3.5 py-2 text-sm font-semibold rounded-lg transition flex items-center gap-2 {{ request()->routeIs('admin.users.*') ? 'bg-purple-50 text-purple-800 font-bold' : 'text-purple-600 hover:text-purple-900 hover:bg-purple-50' }}">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            Kelola Admin
                            @php $pendingCount = \App\Models\User::where('status', 'pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="px-1.5 py-0.2 bg-amber-500 text-white rounded-full text-[10px] font-black animate-pulse">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                </nav>
                @endauth

                <!-- Right Actions & Authenticated Admin Info -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <a href="{{ route('scan.camera') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition border border-gray-200">
                        <i data-lucide="scan-line" class="w-4 h-4 text-blue-600"></i>
                        <span class="hidden sm:inline">Scan QR</span>
                    </a>

                    @auth
                        <a href="{{ route('assets.create') }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-600/20 transition active:scale-95">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Aset</span>
                        </a>

                        <!-- Admin Profile & Switcher / Logout -->
                        <div class="flex items-center pl-2 border-l border-gray-200 space-x-2">
                            <div class="flex items-center space-x-2 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl">
                                <div class="w-6 h-6 rounded-full {{ Auth::user()->isSuperAdmin() ? 'bg-purple-600' : 'bg-blue-600' }} text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="text-left hidden lg:block">
                                    <p class="text-xs font-bold text-gray-800 leading-none flex items-center gap-1">
                                        {{ Auth::user()->name }}
                                        @if(Auth::user()->isSuperAdmin())
                                            <span class="text-[9px] bg-purple-100 text-purple-800 px-1 py-0.2 rounded font-black">SUPER</span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-gray-400 leading-none mt-0.5">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Logout">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-bold text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            Login Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        @auth
        <!-- Mobile Navigation bar -->
        <div class="md:hidden border-t border-gray-100 bg-white px-4 py-2 flex items-center justify-around text-xs">
            <a href="{{ route('assets.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('assets.*') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                <i data-lucide="boxes" class="w-4 h-4 mb-0.5"></i>
                Aset
            </a>
            <a href="{{ route('borrowings.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('borrowings.*') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                <i data-lucide="handshake" class="w-4 h-4 mb-0.5"></i>
                Peminjaman
            </a>
            <a href="{{ route('reports.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('reports.*') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mb-0.5"></i>
                Laporan
            </a>
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.users.*') ? 'text-purple-600 font-bold' : 'text-purple-500' }}">
                    <i data-lucide="shield-check" class="w-4 h-4 mb-0.5"></i>
                    Admin
                </a>
            @endif
            <a href="{{ route('scan.camera') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('scan.*') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                <i data-lucide="scan-line" class="w-4 h-4 mb-0.5"></i>
                Scanner
            </a>
        </div>
        @endauth
    </header>

    <!-- Flash Messages & Semantic Toasts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs">
                <div class="p-1 bg-emerald-100 rounded-lg text-emerald-700 shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-emerald-950">Berhasil!</h4>
                    <p class="text-sm text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
                <div class="p-1 bg-amber-100 rounded-lg text-amber-700 shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-amber-950">Perhatian!</h4>
                    <p class="text-sm text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if(session('destructive') || session('error'))
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-xs">
                <div class="p-1 bg-rose-100 rounded-lg text-rose-700 shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-sm text-rose-950">Informasi Tindakan</h4>
                    <p class="text-sm text-rose-800">{{ session('destructive') ?: session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-blue-600 border-t border-blue-400 py-6 text-center text-xs text-white">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium text-white">Tugas UAS Pemrograman Framework | <span class="font-bold text-white"> Yukie Fahzal Adi Kurnia (223111021) </span> | Universitas Informatika dan Bisnis Indonesia</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
