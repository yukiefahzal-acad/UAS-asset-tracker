@extends('layouts.app')

@section('title', 'Peminjaman Aktif')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Peminjaman Aset Aktif</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua barang yang saat ini sedang dipinjam beserta admin penyetuju dan riwayat sirkulasi.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $borrowedAssets->total() }} Sedang Dipinjam
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Active Borrowings Table (Left 2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                    Daftar Barang Belum Kembali
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-bold text-gray-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Aset</th>
                            <th class="px-6 py-3.5">Peminjam</th>
                            <th class="px-6 py-3.5">Disetujui Oleh</th>
                            <th class="px-6 py-3.5">Waktu Pinjam</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($borrowedAssets as $asset)
                            <tr class="hover:bg-amber-50/30 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 font-mono text-xs font-bold">
                                            AST
                                        </div>
                                        <div>
                                            <a href="{{ route('assets.show', $asset->id) }}" class="font-bold text-gray-900 hover:text-blue-600 transition">
                                                {{ $asset->name }}
                                            </a>
                                            <p class="text-xs font-mono text-gray-500">{{ $asset->code }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-gray-800 text-xs">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
                                        {{ $asset->borrowed_by }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        <i data-lucide="shield-check" class="w-3 h-3 text-blue-500"></i>
                                        {{ $asset->approved_by ?: 'Admin' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                    {{ $asset->borrowed_at ? $asset->borrowed_at->translatedFormat('d M Y, H:i') : '-' }}
                                    <p class="text-[11px] text-amber-600 font-sans font-semibold mt-0.5">
                                        {{ $asset->borrowed_at ? $asset->borrowed_at->diffForHumans() : '' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('assets.show', $asset->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition">
                                        <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 mr-1"></i>
                                        Terima Kembali
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mb-3">
                                            <i data-lucide="check-check" class="w-6 h-6"></i>
                                        </div>
                                        <p class="font-bold text-gray-800">Semua Aset Tersedia di Inventaris</p>
                                        <p class="text-xs text-gray-500 mt-1">Tidak ada aset yang sedang dipinjam saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($borrowedAssets->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $borrowedAssets->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Audit Trail Logs (Right 1 col) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-6 space-y-4">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i>
                Aktivitas & Tindakan Admin Terkini
            </h2>

            <div class="space-y-3">
                @forelse($recentLogs as $log)
                    @php $actionMeta = $log->action_details; @endphp
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                <i data-lucide="{{ $actionMeta['icon'] }}" class="w-3.5 h-3.5 text-blue-600"></i>
                                {{ $actionMeta['label'] }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700 font-medium truncate">{{ $log->asset ? $log->asset->name : 'Aset' }}</p>
                        
                        <div class="flex items-center justify-between mt-1 text-[11px]">
                            <span class="text-gray-500">Peminjam: <strong class="text-gray-700">{{ $log->actor_name }}</strong></span>
                            @if($log->admin_name)
                                <span class="font-bold text-blue-600">Admin: {{ $log->admin_name }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada riwayat transaksi.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
