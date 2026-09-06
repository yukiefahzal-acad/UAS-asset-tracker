@extends('layouts.app')

@section('title', 'Laporan Keuangan Akhir Tahun')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Laporan Keuangan & Audit Inventaris</h1>
            <p class="text-sm text-gray-500 mt-1">Audit siklus hidup aset lengkap menggunakan Eloquent <code class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded text-blue-600">withTrashed()</code> untuk transparansi nilai perolehan & write-off.</p>
        </div>
        <div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Laporan Audit
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase text-gray-500">Tahun Perolehan:</span>
                <select name="year" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase text-gray-500">Kategori:</span>
                <select name="category" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            @if(request()->has('year') || request()->has('category'))
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Financial Valuation Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Total Gross Valuation -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Nilai Kapitalisasi Aset</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="landmark" class="w-5 h-5"></i></span>
            </div>
            <h3 class="text-2xl font-black text-gray-900 font-mono">
                Rp {{ number_format($totalOriginalValuation, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-gray-500">Akumulasi perolehan {{ $allAssets->count() }} unit aset (Termasuk unit aktif & dihapus).</p>
        </div>

        <!-- Active Valuation -->
        <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Nilai Aset Aktif Operasional</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl"><i data-lucide="shield-check" class="w-5 h-5"></i></span>
            </div>
            <h3 class="text-2xl font-black text-emerald-950 font-mono">
                Rp {{ number_format($activeValuation, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-emerald-700 font-medium">{{ $allAssets->whereNull('deleted_at')->count() }} unit aset aktif siap beroperasi.</p>
        </div>

        <!-- Written-Off / Discarded Valuation (Financial Depreciation) -->
        <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Nilai Dihapuskan (Write-Off / Rusak)</span>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl"><i data-lucide="trash-2" class="w-5 h-5"></i></span>
            </div>
            <h3 class="text-2xl font-black text-rose-950 font-mono">
                Rp {{ number_format($discardedValuation, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-rose-700 font-medium">{{ $statusCounts['discarded'] }} unit aset dihapus (Soft Deleted audit).</p>
        </div>

    </div>

    <!-- Category Breakdown Table -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-4 h-4 text-blue-600"></i>
                Rekapitulasi Nilai Berdasarkan Kategori
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Total Unit</th>
                        <th class="px-6 py-3.5">Unit Aktif</th>
                        <th class="px-6 py-3.5">Unit Dihapus (Write-Off)</th>
                        <th class="px-6 py-3.5 text-right">Nilai Aktif</th>
                        <th class="px-6 py-3.5 text-right">Total Akumulasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categoryBreakdown as $categoryName => $catData)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $categoryName }}</td>
                            <td class="px-6 py-4 font-bold">{{ $catData['total_count'] }}</td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold">{{ $catData['active_count'] }}</td>
                            <td class="px-6 py-4 text-rose-600 font-semibold">{{ $catData['discarded_count'] }}</td>
                            <td class="px-6 py-4 text-right font-mono font-medium text-gray-800">
                                Rp {{ number_format($catData['active_valuation'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-gray-900">
                                Rp {{ number_format($catData['total_valuation'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complete Ledger with Soft-Deleted rows (Audit Trail) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                Buku Besar Aset Lengkap (Active & Discarded Items)
            </h3>
            <span class="text-xs font-mono font-semibold text-gray-400">Query: Asset::withTrashed()->get()</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 uppercase font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode</th>
                        <th class="px-6 py-3.5">Nama Aset</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Tgl Perolehan</th>
                        <th class="px-6 py-3.5">Harga Beli</th>
                        <th class="px-6 py-3.5">Status Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allAssets as $item)
                        <tr class="{{ $item->trashed() ? 'bg-rose-50/40 text-gray-400' : 'hover:bg-slate-50' }}">
                            <td class="px-6 py-3.5 font-mono font-bold {{ $item->trashed() ? 'line-through text-gray-400' : 'text-blue-600' }}">
                                {{ $item->code }}
                            </td>
                            <td class="px-6 py-3.5 font-bold {{ $item->trashed() ? 'text-gray-500' : 'text-gray-900' }}">
                                {{ $item->name }}
                                @if($item->trashed())
                                    <span class="ml-1 text-[10px] text-rose-600 font-semibold">(Dihapus pada {{ $item->deleted_at->format('d/m/Y') }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">{{ $item->category }}</td>
                            <td class="px-6 py-3.5 font-mono">{{ $item->purchase_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 font-mono font-bold text-gray-900">
                                {{ $item->formatted_price }}
                            </td>
                            <td class="px-6 py-3.5">
                                @if($item->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Soft Deleted (Write-Off)
                                    </span>
                                @elseif($item->isBorrowed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Dipinjam ({{ $item->borrowed_by }})
                                    </span>
                                @elseif($item->isBroken())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Rusak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Aktif (Tersedia)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada data untuk laporan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
