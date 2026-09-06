@extends('layouts.app')

@section('title', 'Detail Aset - ' . $asset->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-900 transition mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Kembali ke Daftar Aset
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $asset->name }}</h1>
                @php $badge = $asset->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $asset->trashed() ? 'bg-gray-400' : ($asset->status === 'available' ? 'bg-emerald-500' : ($asset->status === 'borrowed' ? 'bg-amber-500' : 'bg-rose-500')) }}"></span>
                    {{ $badge['label'] }}
                </span>
            </div>
            <p class="text-xs font-mono text-gray-500 mt-1">Kode: <span class="font-bold text-gray-800">{{ $asset->code }}</span> &bull; UUID: <span class="text-gray-600">{{ $asset->uuid }}</span></p>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <!-- Edit Button -->
            <a href="{{ route('assets.edit', $asset->id) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs rounded-xl transition border border-amber-200 shadow-xs">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Ubah Data Aset
            </a>

            <a href="{{ route('assets.print-label', $asset->id) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs rounded-xl transition border border-gray-200">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Label QR
            </a>

            <a href="{{ route('scan.show', $asset->uuid) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs rounded-xl transition border border-blue-200">
                <i data-lucide="smartphone" class="w-4 h-4"></i>
                Buka Scan Page
            </a>

            @if(!$asset->trashed())
                <!-- End-of-life trigger: Red button for Soft Delete -->
                <button type="button" onclick="openDeleteModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-xl transition border border-rose-200">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Tandai Rusak / Dibuang
                </button>
            @else
                <form method="POST" action="{{ route('assets.restore', $asset->id) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition border border-indigo-200">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Pulihkan Aset
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($asset->trashed())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3">
            <div class="p-1 bg-rose-100 rounded-lg text-rose-700 shrink-0">
                <i data-lucide="archive" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-rose-950">Aset Berstatus Diarsipkan (Soft Deleted)</h4>
                <p class="text-xs text-rose-800 mt-0.5">Aset ini telah ditandai rusak/dibuang pada {{ $asset->deleted_at->translatedFormat('d F Y, H:i') }}. Nilai perolehan tetap tercatat dalam Laporan Akhir Tahun untuk audit keuangan.</p>
            </div>
        </div>
    @endif

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Asset Info & Borrowing State -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Asset Metadata Card -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                        Informasi & Spesifikasi Aset
                    </h3>
                    <a href="{{ route('assets.edit', $asset->id) }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                        <i data-lucide="edit-2" class="w-3 h-3"></i> Edit Data
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-400 block uppercase">Kategori</span>
                        <span class="font-bold text-gray-800 mt-0.5 block">{{ $asset->category }}</span>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-400 block uppercase">Lokasi Saat Ini</span>
                        <span class="font-bold text-gray-800 mt-0.5 block">{{ $asset->location }}</span>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-400 block uppercase">Harga Perolehan</span>
                        <span class="font-bold font-mono text-gray-900 mt-0.5 block">{{ $asset->formatted_price }}</span>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-400 block uppercase">Tanggal Perolehan</span>
                        <span class="font-bold text-gray-800 mt-0.5 block">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 sm:col-span-2">
                        <span class="text-xs font-semibold text-gray-400 block uppercase">Catatan & Seri</span>
                        <span class="font-medium text-gray-700 mt-0.5 block">{{ $asset->notes ?: 'Tidak ada catatan tambahan.' }}</span>
                    </div>
                </div>
            </div>

            <!-- Borrowing & Quick State Machine Action Card -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-600"></i>
                    Status Peminjaman Terkini
                </h3>

                @if($asset->isAvailable())
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-emerald-950">Aset Tersedia di Inventaris</p>
                                <p class="text-xs text-emerald-800">Barang siap untuk dipinjam dan disetujui oleh admin <strong>{{ Auth::check() ? Auth::user()->name : '' }}</strong>.</p>
                            </div>
                        </div>

                        <!-- Pinjam Action Button -->
                        <button type="button" onclick="openBorrowModal()" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs shadow-blue-600/20 transition active:scale-95 flex items-center justify-center gap-1.5">
                            <i data-lucide="handshake" class="w-4 h-4"></i>
                            Setujui Peminjaman
                        </button>
                    </div>
                @elseif($asset->isBorrowed())
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-amber-950">Sedang Dipinjam: {{ $asset->borrowed_by }}</p>
                                <p class="text-xs text-amber-800">
                                    Disetujui oleh: <strong class="font-semibold">{{ $asset->approved_by ?: 'Admin' }}</strong> &bull; Sejak {{ $asset->borrowed_at ? $asset->borrowed_at->translatedFormat('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Return Action Button -->
                        <button type="button" onclick="openReturnModal()" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs shadow-emerald-600/20 transition active:scale-95 flex items-center justify-center gap-1.5">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            Terima Pengembalian
                        </button>
                    </div>
                @elseif($asset->isBroken())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-rose-950">Aset Rusak / Perlu Perbaikan</p>
                            <p class="text-xs text-rose-800">Aset berstatus rusak dan tidak dapat dipinjam sampai diperbaiki atau dihapus.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Activity & Audit Timeline [AT-104 & Admin Attribution] -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
                    Riwayat Aktivitas & Log Peminjaman
                </h3>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
                    @forelse($asset->logs as $log)
                        @php $actionMeta = $log->action_details; @endphp
                        <div class="relative flex items-start gap-4">
                            <!-- Bullet -->
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            </div>
                            <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="font-bold text-gray-900 flex items-center gap-1.5">
                                        <i data-lucide="{{ $actionMeta['icon'] }}" class="w-3.5 h-3.5 text-blue-600"></i>
                                        {{ $actionMeta['label'] }}
                                    </span>
                                    <span class="text-gray-400 font-mono text-[11px]">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-gray-600">
                                    <p><strong class="text-gray-800 font-semibold">Peminjam / Pelaku:</strong> {{ $log->actor_name }}</p>
                                    @if($log->admin_name)
                                        <p class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md font-semibold border border-blue-100">
                                            Admin: {{ $log->admin_name }}
                                        </p>
                                    @endif
                                </div>

                                @if($log->notes)
                                    <p class="text-gray-500 mt-2 italic font-mono bg-white p-2.5 rounded-lg border border-gray-100">"{{ $log->notes }}"</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 py-4">Belum ada riwayat aktivitas yang tercatat.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Printable QR Code Card -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col items-center text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Visual QR Code Aset</span>

                <!-- QR Code Box -->
                <div class="p-4 bg-white rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center shadow-inner my-2">
                    {!! $qrCodeSvg !!}
                </div>

                <div class="mt-3 w-full">
                    <p class="text-sm font-bold text-gray-900">{{ $asset->code }}</p>
                    <p class="text-[11px] text-gray-400 font-mono break-all mt-0.5">{{ $scanUrl }}</p>
                </div>

                <div class="w-full mt-6 space-y-2">
                    <a href="{{ route('assets.print-label', $asset->id) }}" target="_blank"
                       class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs shadow-blue-600/20 transition active:scale-95 flex items-center justify-center gap-1.5">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Cetak Format Stiker
                    </a>
                    <a href="{{ route('scan.show', $asset->uuid) }}"
                       class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Tes Scan Langsung
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Pinjam Barang -->
<div id="borrowModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="handshake" class="w-5 h-5 text-blue-600"></i>
                Setujui Peminjaman Aset
            </h3>
            <button onclick="closeBorrowModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form method="POST" action="{{ route('borrowings.borrow', $asset->uuid) }}" class="space-y-4">
            @csrf

            <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-900">
                Admin Penyetuju: <strong class="font-bold">{{ Auth::check() ? Auth::user()->name : 'Petugas' }}</strong>
            </div>

            <div>
                <label for="borrower_name" class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Karyawan Peminjam <span class="text-rose-500">*</span></label>
                <input type="text" id="borrower_name" name="borrower_name" required placeholder="Contoh: Budi Santoso (Dept. Finance)"
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="borrow_notes" class="block text-xs font-bold uppercase text-gray-700 mb-1">Keperluan / Catatan Peminjaman</label>
                <textarea id="borrow_notes" name="notes" rows="2" placeholder="Keperluan rapat client, presentasi..."
                          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeBorrowModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Setujui & Catat Peminjaman</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Kembalikan Barang -->
<div id="returnModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="arrow-down-left" class="w-5 h-5 text-emerald-600"></i>
                Terima Pengembalian Aset
            </h3>
            <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form method="POST" action="{{ route('borrowings.return', $asset->uuid) }}" class="space-y-4">
            @csrf

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-xs text-emerald-900">
                Admin Penerima: <strong class="font-bold">{{ Auth::check() ? Auth::user()->name : 'Petugas' }}</strong>
            </div>

            <div>
                <label for="returner_name" class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Yang Mengembalikan</label>
                <input type="text" id="returner_name" name="returner_name" value="{{ $asset->borrowed_by }}"
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Kondisi Barang Saat Dikembalikan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <label class="flex items-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition">
                        <input type="radio" name="condition" value="good" checked class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-gray-800">Kondisi Baik</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-rose-50 hover:border-rose-200 transition">
                        <input type="radio" name="condition" value="broken" class="text-rose-600 focus:ring-rose-500">
                        <span class="text-xs font-bold text-rose-700">Rusak / Bermasalah</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="return_notes" class="block text-xs font-bold uppercase text-gray-700 mb-1">Catatan Pengembalian</label>
                <textarea id="return_notes" name="notes" rows="2" placeholder="Barang diterima dalam kondisi lengkap..."
                          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeReturnModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs">Verifikasi & Terima</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Soft Delete / Tandai Rusak/Dibuang (End-of-life) -->
<div id="deleteModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-2xl p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Tandai Rusak / Dibuang?</h3>
                <p class="text-xs text-gray-500">Aset akan diarsipkan menggunakan Soft Deletes.</p>
            </div>
        </div>

        <p class="text-xs text-gray-600 bg-amber-50 p-3 rounded-xl border border-amber-200 mb-4 leading-relaxed">
            <strong class="text-amber-900 font-bold">Integritas Keuangan:</strong> Data aset tidak akan dihapus permanen dari basis data agar tetap dapat diaudit pada Laporan Keuangan Akhir Tahun.
        </p>

        <form method="POST" action="{{ route('assets.destroy', $asset->id) }}" class="space-y-4">
            @csrf
            @method('DELETE')

            <div>
                <label for="reason" class="block text-xs font-bold uppercase text-gray-700 mb-1">Alasan Penonaktifan / Pembuangan</label>
                <input type="text" id="reason" name="reason" placeholder="Contoh: Rusak total akibat korsleting listrik..."
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs">Ya, Tandai Rusak & Hapus</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openBorrowModal() {
        document.getElementById('borrowModal').classList.remove('hidden');
        document.getElementById('borrowModal').classList.add('flex');
    }
    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
        document.getElementById('borrowModal').classList.remove('flex');
    }
    function openReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
        document.getElementById('returnModal').classList.add('flex');
    }
    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
        document.getElementById('returnModal').classList.remove('flex');
    }
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
</script>
@endpush
@endsection
