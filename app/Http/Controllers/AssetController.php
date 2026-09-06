<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    /**
     * Display a listing of active assets.
     */
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('borrowed_by', 'like', "%{$search}%")
                  ->orWhere('approved_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $categories = Asset::select('category')->distinct()->pluck('category');
        
        $stats = [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'borrowed' => Asset::where('status', 'borrowed')->count(),
            'broken' => Asset::where('status', 'broken')->count(),
        ];

        return view('assets.index', compact('assets', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $categories = ['Elektronik & Komputer', 'Peralatan Kantor', 'Furnitur', 'Kendaraan Operasional', 'Mesin & Alat Berat', 'Lainnya'];
        return view('assets.create', compact('categories'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $asset = Asset::create([
            ...$validated,
            'status' => 'available',
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "Aset \"{$asset->name}\" ({$asset->code}) berhasil didaftarkan ke sistem!");
    }

    /**
     * Display the specified asset details and printable QR code.
     */
    public function show(string $id)
    {
        $asset = Asset::withTrashed()->with('logs')->findOrFail($id);
        
        $scanUrl = route('scan.show', $asset->uuid);
        $qrCodeSvg = QrCode::size(200)->generate($scanUrl);

        return view('assets.show', compact('asset', 'scanUrl', 'qrCodeSvg'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(string $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $categories = ['Elektronik & Komputer', 'Peralatan Kantor', 'Furnitur', 'Kendaraan Operasional', 'Mesin & Alat Berat', 'Lainnya'];
        
        return view('assets.edit', compact('asset', 'categories'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:available,borrowed,broken',
            'notes' => 'nullable|string',
        ]);

        $adminName = Auth::check() ? Auth::user()->name : 'Admin';
        
        // Track changes summary
        $changes = [];
        if ($asset->name !== $validated['name']) $changes[] = "Nama diubah";
        if ($asset->location !== $validated['location']) $changes[] = "Lokasi: {$asset->location} -> {$validated['location']}";
        if ($asset->purchase_price != $validated['purchase_price']) $changes[] = "Harga: Rp " . number_format($validated['purchase_price'], 0, ',', '.');
        if ($asset->status !== $validated['status']) $changes[] = "Status: {$asset->status} -> {$validated['status']}";

        $asset->update($validated);

        // Record audit log for edit
        $asset->logs()->create([
            'action' => 'updated',
            'actor_name' => $adminName,
            'admin_name' => $adminName,
            'notes' => count($changes) > 0 ? implode(', ', $changes) : 'Perubahan data spesifikasi aset.',
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "Data aset \"{$asset->name}\" berhasil diperbarui oleh {$adminName}.");
    }

    /**
     * Printable QR Code Label view.
     */
    public function printLabel(string $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $scanUrl = route('scan.show', $asset->uuid);
        $qrCodeSvg = QrCode::size(250)->generate($scanUrl);

        return view('assets.print-label', compact('asset', 'scanUrl', 'qrCodeSvg'));
    }

    /**
     * Soft delete asset (Finance End-of-Life: Tandai Rusak/Dibuang).
     */
    public function destroy(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->isBorrowed()) {
            return back()->with('error', 'Aset sedang dipinjam! Kembalikan aset terlebih dahulu sebelum menandai rusak/dibuang.');
        }

        $reason = $request->input('reason', 'Ditandai rusak/dibuang oleh Admin.');
        $asset->status = 'broken';
        $asset->save();

        // Soft delete execution
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('destructive', "Aset \"{$asset->name}\" ({$asset->code}) telah ditandai rusak/dibuang dan diarsipkan (Soft Deleted).");
    }

    /**
     * Restore a soft-deleted asset.
     */
    public function restore(string $id)
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->restore();
        $asset->status = 'available';
        $asset->save();

        $adminName = Auth::check() ? Auth::user()->name : 'Finance Admin';

        $asset->logs()->create([
            'action' => 'restored',
            'actor_name' => $adminName,
            'admin_name' => $adminName,
            'notes' => 'Aset berhasil dipulihkan dari arsip.',
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "Aset \"{$asset->name}\" berhasil dipulihkan.");
    }
}
