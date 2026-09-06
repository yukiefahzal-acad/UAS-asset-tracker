@extends('layouts.app')

@section('title', 'Daftar Aset')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Daftar Inventaris Aset</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('assets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm shadow-blue-600/20 transition active:scale-95 text-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Aset Baru
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Aset</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <!-- Available (Green) -->
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Tersedia</p>
                <h3 class="text-2xl font-bold text-emerald-950 mt-0.5">{{ $stats['available'] }}</h3>
            </div>
        </div>

        <!-- Borrowed (Yellow) -->
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Sedang Dipinjam</p>
                <h3 class="text-2xl font-bold text-amber-950 mt-0.5">{{ $stats['borrowed'] }}</h3>
            </div>
        </div>

        <!-- Broken (Red) -->
        <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Rusak</p>
                <h3 class="text-2xl font-bold text-rose-950 mt-0.5">{{ $stats['broken'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs">
        <form method="GET" action="{{ route('assets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="md:col-span-5 relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode aset, nama, kategori, approved by..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Status Filter -->
            <div class="md:col-span-3">
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam (Borrowed)</option>
                    <option value="broken" {{ request('status') === 'broken' ? 'selected' : '' }}>Rusak (Broken)</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="md:col-span-3">
                <select name="category" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="md:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl flex items-center justify-center transition">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                @if(request()->anyFilled(['search', 'status', 'category']))
                    <a href="{{ route('assets.index') }}" class="py-2 px-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kode & Nama Aset</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Nilai Beli</th>
                        <th class="px-6 py-4">Status & Peminjam</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Code & Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0 border border-gray-200">
                                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('assets.show', $asset->id) }}" class="font-bold text-gray-900 hover:text-blue-600 transition">
                                            {{ $asset->name }}
                                        </a>
                                        <p class="text-xs font-mono text-gray-500 mt-0.5">{{ $asset->code }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $asset->category }}
                                </span>
                            </td>

                            <!-- Location -->
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                                    {{ $asset->location }}
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4 font-mono font-medium text-gray-900">
                                {{ $asset->formatted_price }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @php $badge = $asset->status_badge; @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $asset->status === 'available' ? 'bg-emerald-500' : ($asset->status === 'borrowed' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                @if($asset->isBorrowed())
                                    <p class="text-[11px] text-amber-800 font-semibold mt-1">
                                        Dipinjam: {{ $asset->borrowed_by }}
                                    </p>
                                    @if($asset->approved_by)
                                        <p class="text-[10px] text-gray-500 font-medium">
                                            Disetujui: <strong class="text-gray-700">{{ $asset->approved_by }}</strong>
                                        </p>
                                    @endif
                                @endif
                            </td>

                            <!-- Actions with Edit Option -->
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('assets.edit', $asset->id) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-lg transition border border-amber-200"
                                   title="Ubah Detail Aset">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 mr-1"></i>
                                    Edit
                                </a>
                                <a href="{{ route('assets.show', $asset->id) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition border border-gray-200">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mb-3">
                                        <i data-lucide="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <p class="font-bold text-gray-800">Belum ada data aset</p>
                                    <p class="text-xs text-gray-500 mt-1">Klik "Tambah Aset Baru" untuk mendaftarkan aset pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
