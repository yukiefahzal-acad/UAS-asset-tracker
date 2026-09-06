@extends('layouts.app')

@section('title', 'Manajemen Akun Admin & Persetujuan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 font-bold text-xs uppercase tracking-wider">
                    Super Admin Only
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight mt-1.5">Persetujuan & Manajemen Akun Admin</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan setujui permohonan pendaftaran akun admin baru untuk mengamankan akses inventaris.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingAdmins->count() }} Menunggu Persetujuan
            </span>
        </div>
    </div>

    <!-- 1. Pending Approval Section (Highlighted) -->
    <div class="bg-white rounded-2xl border-2 border-amber-200/80 shadow-xs overflow-hidden">
        <div class="p-5 bg-amber-50/50 border-b border-amber-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-amber-950 flex items-center gap-2">
                <i data-lucide="user-check" class="w-5 h-5 text-amber-600"></i>
                Permohonan Registrasi Admin Baru (Pending)
            </h2>
            <span class="text-xs font-bold text-amber-700 font-mono">{{ $pendingAdmins->count() }} akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-amber-50/20 border-b border-amber-100 text-xs uppercase font-bold text-amber-900/60 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Nama & Email Admin</th>
                        <th class="px-6 py-3.5">Waktu Pendaftaran</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Tindakan Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingAdmins as $admin)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $admin->name }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-xs font-mono text-gray-600">
                                {{ $admin->created_at->translatedFormat('d M Y, H:i') }}
                                <p class="text-[11px] text-gray-400 font-sans mt-0.5">{{ $admin->created_at->diffForHumans() }}</p>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending Approval
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <!-- Approve Button -->
                                <form method="POST" action="{{ route('admin.users.approve', $admin->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        Setujui Akun
                                    </button>
                                </form>

                                <!-- Reject Button -->
                                <form method="POST" action="{{ route('admin.users.reject', $admin->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 transition">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-xs">
                                <p class="font-bold text-gray-700">Tidak ada permohonan admin yang menunggu persetujuan saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Active Approved Admins List -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                Daftar Admin Aktif (Approved)
            </h2>
            <span class="text-xs font-bold text-gray-400 font-mono">{{ $approvedAdmins->count() }} akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Admin</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Disetujui Oleh</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($approvedAdmins as $admin)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl {{ $admin->isSuperAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 flex items-center gap-1.5">
                                            {{ $admin->name }}
                                            @if($admin->id === Auth::id())
                                                <span class="text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.2 rounded font-bold">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if($admin->isSuperAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-800">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700">
                                        Admin Staf
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-xs">
                                <span class="font-semibold text-gray-700">{{ $admin->approved_by ?: 'System' }}</span>
                                @if($admin->approved_at)
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $admin->approved_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if(!$admin->isSuperAdmin())
                                    <form method="POST" action="{{ route('admin.users.destroy', $admin->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Akun">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold italic">Master</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-400 text-xs">Tidak ada admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Rejected Admins (If any) -->
    @if($rejectedAdmins->count() > 0)
    <div class="bg-white rounded-2xl border border-rose-200/80 shadow-xs overflow-hidden">
        <div class="p-4 bg-rose-50/50 border-b border-rose-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-900 flex items-center gap-2">
                <i data-lucide="user-x" class="w-4 h-4 text-rose-600"></i>
                Permohonan Yang Ditolak
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($rejectedAdmins as $rej)
                <div class="p-4 flex items-center justify-between text-xs">
                    <div>
                        <p class="font-bold text-gray-800">{{ $rej->name }} ({{ $rej->email }})</p>
                        <p class="text-[11px] text-gray-400">Ditolak pada {{ $rej->approved_at ? $rej->approved_at->format('d/m/Y H:i') : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.approve', $rej->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-gray-100 hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 rounded-lg font-bold transition">
                            Pulihkan & Setujui
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
