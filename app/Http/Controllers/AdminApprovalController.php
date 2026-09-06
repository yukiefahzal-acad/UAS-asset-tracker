<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApprovalController extends Controller
{
    /**
     * Display list of admin accounts and pending registration proposals.
     */
    public function index(Request $request)
    {
        // Enforce Super Admin only
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Akses Terbatas: Halaman ini hanya dapat diakses oleh Super Admin.');
        }

        $pendingAdmins = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $approvedAdmins = User::where('status', 'approved')->orderBy('created_at', 'desc')->get();
        $rejectedAdmins = User::where('status', 'rejected')->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('pendingAdmins', 'approvedAdmins', 'rejectedAdmins'));
    }

    /**
     * Approve an admin registration proposal.
     */
    public function approve(Request $request, string $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Akses Terbatas: Hanya Super Admin yang dapat menyetujui akun admin.');
        }

        $user = User::findOrFail($id);
        $user->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun Admin \"{$user->name}\" ({$user->email}) telah berhasil DISETUJUI dan sekarang dapat masuk ke sistem.");
    }

    /**
     * Reject an admin registration proposal.
     */
    public function reject(Request $request, string $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Akses Terbatas: Hanya Super Admin yang dapat menolak akun admin.');
        }

        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat menolak akun Super Admin utama.');
        }

        $user->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('destructive', "Pendaftaran Admin \"{$user->name}\" ({$user->email}) telah DITOLAK.");
    }

    /**
     * Delete an admin account.
     */
    public function destroy(Request $request, string $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat menghapus akun Super Admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('destructive', "Akun Admin \"{$user->name}\" telah dihapus permanen.");
    }
}
