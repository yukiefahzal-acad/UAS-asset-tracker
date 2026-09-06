<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Pindai Aset - {{ $asset->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 text-gray-900 flex flex-col font-sans pb-12">

    <!-- Mobile Header -->
    <header class="bg-white border-b border-gray-200 px-4 py-3.5 sticky top-0 z-30 shadow-xs">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900 leading-tight">Asset<span class="text-blue-600">Track</span></h1>
                    <p class="text-[10px] text-gray-400 font-medium">QR Inspection & Borrowing</p>
                </div>
            </div>

            @auth
                <a href="{{ route('assets.show', $asset->id) }}" class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                    Dashboard Admin
                </a>
            @else
                <a href="{{ route('login') }}" class="px-2.5 py-1 text-xs font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Login Petugas
                </a>
            @endauth
        </div>
    </header>

    <!-- Content Container -->
    <main class="max-w-md mx-auto w-full px-4 pt-4 space-y-4">

        <!-- Flash messages -->
        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-2.5 shadow-xs text-xs">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-bold text-emerald-950">Berhasil</p>
                    <p class="text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-2.5 shadow-xs text-xs">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-bold text-amber-950">Perhatian</p>
                    <p class="text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if(session('destructive') || session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-2.5 shadow-xs text-xs">
                <i data-lucide="x-circle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-bold text-rose-950">Status Aset Diubah</p>
                    <p class="text-rose-800">{{ session('destructive') ?: session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Asset Profile Card -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 space-y-4">
            
            <!-- Code and Status Badge -->
            <div class="flex items-center justify-between gap-2">
                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-mono font-bold rounded-lg border border-gray-200">
                    {{ $asset->code }}
                </span>

                @php $badge = $asset->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                    <span class="w-2 h-2 rounded-full {{ $asset->trashed() ? 'bg-gray-400' : ($asset->status === 'available' ? 'bg-emerald-500' : ($asset->status === 'borrowed' ? 'bg-amber-500' : 'bg-rose-500')) }}"></span>
                    {{ $badge['label'] }}
                </span>
            </div>

            <!-- Asset Title -->
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 leading-tight">{{ $asset->name }}</h2>
                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                    {{ $asset->category }} &bull; 📍 {{ $asset->location }}
                </p>
            </div>

            <!-- Dynamic Borrowing Status Banner & Actions -->
            @if($asset->trashed())
                <!-- Soft Deleted State -->
                <div class="p-3.5 rounded-xl bg-gray-100 border border-gray-300 text-gray-700 text-xs flex items-center gap-3">
                    <i data-lucide="archive" class="w-5 h-5 text-gray-500 shrink-0"></i>
                    <div>
                        <p class="font-bold text-gray-900">Aset Diarsipkan / Dibuang</p>
                        <p class="text-gray-600 mt-0.5">Barang ini tidak lagi aktif di inventaris operasional.</p>
                    </div>
                </div>

            @elseif($asset->isAvailable())
                <!-- Available State: Blue Action Button [Flow 2] -->
                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center gap-2.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span>Aset ini <strong>Tersedia</strong> dan dapat dipinjam sekarang.</span>
                    </div>

                    <button type="button" onclick="document.getElementById('borrowDrawer').classList.toggle('hidden')"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/20 transition active:scale-98 flex items-center justify-center gap-2">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                        Pinjam Barang Ini
                    </button>

                    <!-- Borrow Form Drawer (Reveals on click) -->
                    <div id="borrowDrawer" class="hidden pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('borrowings.borrow', $asset->uuid) }}" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            @csrf
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Form Peminjaman Cepat</h4>
                            
                            <div>
                                <label for="borrower_name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Peminjam / NIK <span class="text-rose-500">*</span></label>
                                <input type="text" id="borrower_name" name="borrower_name" required placeholder="Contoh: Rian Anggara (Marketing)"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                            </div>

                            @guest
                            <div>
                                <label for="admin_name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Petugas Penyerah / Admin</label>
                                <input type="text" id="admin_name" name="admin_name" placeholder="Contoh: Alex Pratama"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                            </div>
                            @endguest

                            <div>
                                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">Catatan Keperluan</label>
                                <input type="text" id="notes" name="notes" placeholder="Meeting luar kota, expo..."
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                Konfirmasi Pinjam
                            </button>
                        </form>
                    </div>
                </div>

            @elseif($asset->isBorrowed())
                <!-- Borrowed State: Yellow Warning Banner [Flow 2] -->
                <div class="space-y-3">
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
                        <i data-lucide="clock" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-amber-950 text-sm">Sedang Dipinjam</p>
                            <p class="text-amber-900 mt-0.5">Peminjam: <strong class="font-bold underline">{{ $asset->borrowed_by }}</strong></p>
                            @if($asset->approved_by)
                                <p class="text-[11px] text-amber-800 mt-0.5">Disetujui oleh: <strong>{{ $asset->approved_by }}</strong></p>
                            @endif
                            <p class="text-[11px] text-amber-700 mt-0.5">Dipinjam sejak {{ $asset->borrowed_at ? $asset->borrowed_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                        </div>
                    </div>

                    <!-- Return Form Drawer -->
                    <button type="button" onclick="document.getElementById('returnDrawer').classList.toggle('hidden')"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-98 flex items-center justify-center gap-2">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                        Kembalikan Aset Ini
                    </button>

                    <div id="returnDrawer" class="hidden pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('borrowings.return', $asset->uuid) }}" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs">
                            @csrf
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Form Pengembalian Barang</h4>
                            <div>
                                <label for="returner_name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Yang Mengembalikan</label>
                                <input type="text" id="returner_name" name="returner_name" value="{{ $asset->borrowed_by }}"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                            </div>

                            @guest
                            <div>
                                <label for="admin_name_ret" class="block text-xs font-semibold text-gray-700 mb-1">Nama Petugas Penerima / Admin</label>
                                <input type="text" id="admin_name_ret" name="admin_name" placeholder="Contoh: Anex Santoso"
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                            </div>
                            @endguest

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Barang Saat Ini</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-1.5 p-2 bg-white border border-gray-300 rounded-lg cursor-pointer">
                                        <input type="radio" name="condition" value="good" checked class="text-emerald-600">
                                        <span class="font-bold text-gray-800">Kondisi Baik</span>
                                    </label>
                                    <label class="flex items-center gap-1.5 p-2 bg-white border border-rose-300 rounded-lg cursor-pointer">
                                        <input type="radio" name="condition" value="broken" class="text-rose-600">
                                        <span class="font-bold text-rose-700">Rusak</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="return_notes" class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                                <input type="text" id="return_notes" name="notes" placeholder="Kondisi bersih, lengkap..."
                                       class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                                Selesai & Simpan Pengembalian
                            </button>
                        </form>
                    </div>
                </div>

            @elseif($asset->isBroken())
                <!-- Broken State: Red Banner -->
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 flex items-start gap-3">
                    <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-rose-950 text-sm">Aset Rusak</p>
                        <p class="text-rose-800 mt-0.5">Barang tidak dapat dipinjam. Harap laporkan ke bagian maintenance / admin inventaris.</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Timeline Log of Transactions [AT-104 & Admin Attribution] -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                <i data-lucide="history" class="w-3.5 h-3.5 text-blue-600"></i>
                Riwayat Peminjaman & Audit Log
            </h3>

            <div class="space-y-3 divide-y divide-gray-100">
                @forelse($asset->logs as $log)
                    @php $actionMeta = $log->action_details; @endphp
                    <div class="pt-3 first:pt-0 text-xs">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                <i data-lucide="{{ $actionMeta['icon'] }}" class="w-3.5 h-3.5 text-blue-600"></i>
                                {{ $actionMeta['label'] }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->translatedFormat('d/m/y H:i') }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 text-gray-600">
                            <p>Oleh: <strong class="text-gray-800">{{ $log->actor_name }}</strong></p>
                            @if($log->admin_name)
                                <p class="text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded font-semibold text-[11px]">Admin: {{ $log->admin_name }}</p>
                            @endif
                        </div>
                        @if($log->notes)
                            <p class="text-[11px] text-gray-500 italic mt-0.5 font-mono">"{{ $log->notes }}"</p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-2">Belum ada riwayat tercatat.</p>
                @endforelse
            </div>
        </div>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
