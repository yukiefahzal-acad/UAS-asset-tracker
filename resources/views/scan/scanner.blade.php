@extends('layouts.app')

@section('title', 'Kamera Pemindai QR')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Header -->
    <div class="text-center space-y-1">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pemindai QR Code Aset</h1>
        <p class="text-sm text-gray-500">Arahkan kamera ke QR Code label aset untuk memeriksa status & meminjam barang.</p>
    </div>

    <!-- Camera Scanner Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
        
        <!-- Video Viewport -->
        <div id="reader-wrapper" class="relative rounded-xl overflow-hidden bg-black aspect-square flex items-center justify-center border-2 border-dashed border-gray-300">
            <div id="reader" class="w-full h-full"></div>
            <div id="scanner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-gray-900/90 p-6 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-blue-600/20 border border-blue-500/40 flex items-center justify-center text-blue-400">
                    <i data-lucide="camera" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base">Aktifkan Kamera</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-xs">Izinkan akses kamera browser untuk mulai memindai label QR aset.</p>
                </div>
                <button id="btn-start-scan" type="button" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-2">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Mulai Scan Kamera
                </button>
            </div>
        </div>

        <div id="scan-feedback" class="hidden p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold text-center">
            QR Terdeteksi! Membuka halaman aset...
        </div>

        <!-- Manual UUID / Code fallback -->
        <div class="pt-4 border-t border-gray-100">
            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Atau Masukkan UUID / Kode Manual</label>
            <form id="manual-form" class="flex gap-2" onsubmit="handleManualSubmit(event)">
                <input type="text" id="manual-uuid" placeholder="Contoh: 1b9d6bcd-bbfd-4b2d-9b5d-ab9dfbbd4bed" required
                       class="flex-1 px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-hidden focus:ring-2 focus:ring-blue-500 font-mono">
                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white font-bold text-xs rounded-xl transition">
                    Cari
                </button>
            </form>
        </div>

    </div>

    <!-- Quick Test Assets Section -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Pintasan Uji Coba Cepat (Aset Terbaru)</h3>
        <div class="space-y-2">
            @forelse($recentAssets as $asset)
                <a href="{{ route('scan.show', $asset->uuid) }}" 
                   class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-200 border border-gray-100 transition group text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full {{ $asset->status === 'available' ? 'bg-emerald-500' : ($asset->status === 'borrowed' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600">{{ $asset->name }}</p>
                            <p class="text-[11px] font-mono text-gray-400">{{ $asset->code }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-blue-600 flex items-center gap-1">
                        Buka Detail <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </span>
                </a>
            @empty
                <p class="text-xs text-gray-400">Belum ada aset terdaftar.</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;

    document.getElementById('btn-start-scan').addEventListener('click', function() {
        document.getElementById('scanner-placeholder').classList.add('hidden');
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                // Success QR decoded
                document.getElementById('scan-feedback').classList.remove('hidden');
                html5QrCode.stop().then(() => {
                    if (decodedText.startsWith('http')) {
                        window.location.href = decodedText;
                    } else {
                        window.location.href = '/scan/' + decodedText;
                    }
                });
            },
            (errorMessage) => {
                // scanning...
            }
        ).catch((err) => {
            alert('Tidak dapat mengakses kamera: ' + err);
            document.getElementById('scanner-placeholder').classList.remove('hidden');
        });
    });

    function handleManualSubmit(e) {
        e.preventDefault();
        const val = document.getElementById('manual-uuid').value.trim();
        if (val) {
            window.location.href = '/scan/' + val;
        }
    }
</script>
@endpush
@endsection
