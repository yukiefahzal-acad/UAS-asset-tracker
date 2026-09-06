<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * Display list of currently active borrowings.
     */
    public function index(Request $request)
    {
        $borrowedAssets = Asset::where('status', 'borrowed')
            ->orderBy('borrowed_at', 'desc')
            ->paginate(15);

        $recentLogs = AssetLog::with('asset')
            ->whereIn('action', ['borrowed', 'returned', 'marked_broken', 'updated'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('borrowings.index', compact('borrowedAssets', 'recentLogs'));
    }

    /**
     * Borrow an asset (Transition from Available -> Borrowed).
     * Tagged with approving Admin (e.g. "Approved by Alex").
     */
    public function borrow(Request $request, string $uuid)
    {
        $request->validate([
            'borrower_name' => 'required|string|max:255',
            'admin_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $asset = Asset::where('uuid', $uuid)->firstOrFail();

        // State Machine validation
        if ($asset->trashed()) {
            return back()->with('error', 'Aset ini telah dihapus/dibuang dari inventaris.');
        }

        if ($asset->isBroken()) {
            return back()->with('error', 'Gagal meminjam: Barang dalam kondisi RUSAK dan tidak dapat dipinjam.');
        }

        if ($asset->isBorrowed()) {
            return back()->with('warning', "Barang sedang dipinjam oleh {$asset->borrowed_by}.");
        }

        // Determine approving Admin
        $approver = Auth::check() ? Auth::user()->name : ($request->input('admin_name') ?: 'Petugas Admin');

        // Apply state transition
        $asset->status = 'borrowed';
        $asset->borrowed_by = $request->input('borrower_name');
        $asset->approved_by = $approver; // e.g., "Approved by Alex"
        $asset->borrowed_at = now();
        $asset->save();

        // Log transaction tagged with Approver
        $asset->logs()->create([
            'action' => 'borrowed',
            'actor_name' => $request->input('borrower_name'),
            'admin_name' => $approver,
            'notes' => ($request->input('notes') ?: 'Peminjaman aset oleh karyawan.') . " (Disetujui oleh: {$approver})",
        ]);

        $redirectRoute = Auth::check() ? route('assets.show', $asset->id) : route('scan.show', $asset->uuid);

        return redirect($redirectRoute)
            ->with('success', "Peminjaman aset untuk {$asset->borrowed_by} telah disetujui oleh {$approver}!");
    }

    /**
     * Return an asset (Transition from Borrowed -> Available).
     * Tagged with accepting Admin (e.g. "Accepted by Anex").
     */
    public function returnItem(Request $request, string $uuid)
    {
        $request->validate([
            'returner_name' => 'nullable|string|max:255',
            'admin_name' => 'nullable|string|max:255',
            'condition' => 'required|in:good,broken',
            'notes' => 'nullable|string|max:500',
        ]);

        $asset = Asset::where('uuid', $uuid)->firstOrFail();

        if (!$asset->isBorrowed()) {
            return back()->with('info', 'Aset tidak sedang dalam status dipinjam.');
        }

        $borrower = $asset->borrowed_by;
        $returner = $request->input('returner_name', $borrower ?: 'Karyawan');
        $acceptor = Auth::check() ? Auth::user()->name : ($request->input('admin_name') ?: 'Petugas Admin');
        $condition = $request->input('condition');

        if ($condition === 'broken') {
            $asset->status = 'broken';
            $asset->borrowed_by = null;
            $asset->approved_by = null;
            $asset->borrowed_at = null;
            $asset->save();

            $asset->logs()->create([
                'action' => 'marked_broken',
                'actor_name' => $returner,
                'admin_name' => $acceptor,
                'notes' => "Dikembalikan dalam kondisi RUSAK (Diterima oleh: {$acceptor}). Catatan: " . ($request->input('notes') ?: 'Tidak ada catatan tambahan.'),
            ]);

            $redirectRoute = Auth::check() ? route('assets.show', $asset->id) : route('scan.show', $asset->uuid);

            return redirect($redirectRoute)
                ->with('destructive', "Aset telah dikembalikan oleh {$returner} dan diterima oleh {$acceptor} dalam kondisi RUSAK.");
        }

        // Return to available
        $asset->status = 'available';
        $asset->borrowed_by = null;
        $asset->approved_by = null;
        $asset->borrowed_at = null;
        $asset->save();

        $asset->logs()->create([
            'action' => 'returned',
            'actor_name' => $returner,
            'admin_name' => $acceptor,
            'notes' => "Aset diterima kembali dalam kondisi baik oleh {$acceptor}. " . ($request->input('notes') ?: ''),
        ]);

        $redirectRoute = Auth::check() ? route('assets.show', $asset->id) : route('scan.show', $asset->uuid);

        return redirect($redirectRoute)
            ->with('success', "Pengembalian aset dari {$returner} berhasil diverifikasi dan diterima oleh {$acceptor}.");
    }
}
