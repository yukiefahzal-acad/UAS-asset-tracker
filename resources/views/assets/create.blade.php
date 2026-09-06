@extends('layouts.app')

@section('title', 'Tambah Aset Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-900 transition mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Kembali ke Daftar Aset
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tambah Aset Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem akan otomatis menghasilkan Kode Aset, UUID unik, dan QR Code.</p>
        </div>
    </div>

    <!-- Create Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs">
        <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
            @csrf

            <!-- Form Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Asset Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Nama Aset / Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: MacBook Pro M2 14-inch, Proyektor Epson EB-X500..."
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('name') border-rose-300 bg-rose-50/50 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select id="category" name="category" required
                            class="w-full px-4 py-2.5 bg-gray-50 border @error('category') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Lokasi Penempatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required
                           placeholder="Contoh: Ruang IT Server, Lab Komputer 2..."
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('location') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    @error('location')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Price (Financial) -->
                <div>
                    <label for="purchase_price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Harga Perolehan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rp</span>
                        <input type="number" step="1000" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', 0) }}" required
                               placeholder="15000000"
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border @error('purchase_price') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk audit dan laporan keuangan akhir tahun.</p>
                    @error('purchase_price')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Date -->
                <div>
                    <label for="purchase_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Tanggal Perolehan / Pembelian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('purchase_date') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    @error('purchase_date')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes / Specs -->
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Spesifikasi / Catatan Tambahan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Nomor seri hardware, kelengkapan aksesoris, kondisi awal..."
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Auto-generated Info Notice -->
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-start gap-3">
                <div class="p-1 bg-blue-100 rounded-lg text-blue-600 shrink-0">
                    <i data-lucide="info" class="w-4 h-4"></i>
                </div>
                <div class="text-xs text-blue-800 leading-relaxed">
                    <span class="font-bold">Otomatisasi Sistem:</span> Setelah formulir disubmit, sistem akan meng-generate <span class="font-mono font-semibold">UUID v4</span> dan merender QR Code visual untuk label aset secara otomatis.
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('assets.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md shadow-blue-600/20 transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan & Buat QR Code
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
