<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ScanController extends Controller
{
    /**
     * Mobile-first QR Scan Asset Detail Page.
     * Accessed directly when phone camera or in-app scanner scans QR.
     */
    public function show(string $uuid)
    {
        $asset = Asset::withTrashed()->with('logs')->where('uuid', $uuid)->firstOrFail();

        $scanUrl = route('scan.show', $asset->uuid);
        $qrCodeSvg = QrCode::size(160)->generate($scanUrl);

        return view('scan.show', compact('asset', 'scanUrl', 'qrCodeSvg'));
    }

    /**
     * Camera Scanner Interface.
     */
    public function scanner()
    {
        $recentAssets = Asset::latest()->limit(5)->get();
        return view('scan.scanner', compact('recentAssets'));
    }
}
