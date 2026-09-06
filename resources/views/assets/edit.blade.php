@extends('layouts.app')

@section('title', 'Ubah Detail Aset - ' . $asset->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('assets.show', $asset->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-900 transition mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Kembali ke Detail Aset
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Ubah Detail Aset</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui data spesifikasi, lokasi, atau status fisik aset: <span class="font-bold text-gray-800">{{ $asset->code }}</span></p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs">
        <form method="POST" action="{{ route('assets.update', $asset->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Form Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Asset Code (Readonly) -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">
                        Kode Aset & UUID (Permanen)
                    </label>
                    <div class="p-3 bg-gray-100 rounded-xl font-mono text-xs text-gray-700 flex items-center justify-between border border-gray-200">
                        <span><strong>Kode:</strong> {{ $asset->code }}</span>
                        <span><strong>UUID:</strong> {{ $asset->uuid }}</span>
                    </div>
                </div>

                <!-- Asset Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Nama Aset / Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $asset->name) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('name') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
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
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $asset->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Lokasi Penempatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location', $asset->location) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('location') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                </div>

                <!-- Purchase Price (Financial) -->
                <div>
                    <label for="purchase_price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Harga Perolehan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rp</span>
                        <input type="number" step="1000" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', (int)$asset->purchase_price) }}" required
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border @error('purchase_price') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- Purchase Date -->
                <div>
                    <label for="purchase_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Tanggal Perolehan / Pembelian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border @error('purchase_date') border-rose-300 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                </div>

                <!-- Status -->
                <div class="sm:col-span-2">
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Status Aset Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <option value="available" {{ old('status', $asset->status) === 'available' ? 'selected' : '' }}>Tersedia (Available) - Siap dipinjam</option>
                        <option value="borrowed" {{ old('status', $asset->status) === 'borrowed' ? 'selected' : '' }}>Dipinjam (Borrowed)</option>
                        <option value="broken" {{ old('status', $asset->status) === 'broken' ? 'selected' : '' }}>Rusak (Broken) - Tidak bisa dipinjam</option>
                    </select>
                </div>

                <!-- Notes / Specs -->
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Spesifikasi / Catatan Tambahan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white transition">{{ old('notes', $asset->notes) }}</textarea>
                </div>
            </div>

            <!-- Audit Trail Notice -->
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-100 flex items-center gap-2.5 text-xs text-blue-900">
                <i data-lucide="user-check" class="w-4 h-4 text-blue-600 shrink-0"></i>
                <span>Perubahan ini akan dicatat dalam riwayat audit atas nama: <strong>{{ Auth::user()->name }}</strong>.</span>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('assets.show', $asset->id) }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md shadow-blue-600/20 transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
