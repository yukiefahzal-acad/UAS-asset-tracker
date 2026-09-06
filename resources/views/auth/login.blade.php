<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - AssetTrack</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-50">

    <div class="max-w-md w-full space-y-6">

        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-500/20 mb-2">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                Asset<span class="text-blue-600">Track</span> Admin
            </h1>
            <p class="text-xs text-gray-500">Masuk untuk mengelola aset, persetujuan peminjaman, dan audit inventaris.</p>
        </div>

        @if(session('info'))
            <div class="p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs font-semibold text-center">
                {{ session('info') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-semibold flex items-start gap-2 shadow-xs">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs space-y-5">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Username / Email Admin
                    </label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" id="login" name="login" value="{{ old('login', 'sadmin') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition font-mono">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" id="password" name="password" value="yukisadmin" required
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded-md border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-600">Ingat sesi saya</span>
                    </label>

                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">
                        Daftar Admin Baru &rarr;
                    </a>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/20 transition active:scale-98 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Super Admin Badge Info -->
            <div class="pt-3 border-t border-gray-100 text-center">
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 flex items-center justify-between text-xs">
                    <span class="font-bold text-blue-900 flex items-center gap-1.5">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-blue-600"></i> Super Admin
                    </span>
                    <span class="font-mono font-bold text-gray-700">sadmin / yukisadmin</span>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('scan.camera') }}" class="text-xs font-semibold text-gray-500 hover:text-blue-600 transition flex items-center justify-center gap-1">
                <i data-lucide="scan-line" class="w-3.5 h-3.5"></i>
                Buka Kamera Scanner Tanpa Login
            </a>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
