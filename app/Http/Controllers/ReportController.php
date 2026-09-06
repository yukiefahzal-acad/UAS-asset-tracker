<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Financial & Inventory Year-End Audit Report.
     * Uses withTrashed() to ensure discarded assets remain accounted for.
     */
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        $selectedCategory = $request->input('category');

        // Main financial query using withTrashed()
        $query = Asset::withTrashed();

        if ($request->filled('year')) {
            $query->whereYear('purchase_date', $selectedYear);
        }

        if ($request->filled('category')) {
            $query->where('category', $selectedCategory);
        }

        $allAssets = $query->orderBy('created_at', 'asc')->get();

        // Financial Metrics
        $totalOriginalValuation = $allAssets->sum('purchase_price');
        $activeAssets = $allAssets->whereNull('deleted_at');
        $activeValuation = $activeAssets->sum('purchase_price');
        $discardedAssets = $allAssets->whereNotNull('deleted_at');
        $discardedValuation = $discardedAssets->sum('purchase_price');

        // Breakdown by status
        $statusCounts = [
            'available' => $allAssets->where('status', 'available')->whereNull('deleted_at')->count(),
            'borrowed' => $allAssets->where('status', 'borrowed')->whereNull('deleted_at')->count(),
            'broken' => $allAssets->where('status', 'broken')->whereNull('deleted_at')->count(),
            'discarded' => $discardedAssets->count(),
        ];

        // Breakdown by category
        $categoryBreakdown = $allAssets->groupBy('category')->map(function ($group) {
            return [
                'total_count' => $group->count(),
                'active_count' => $group->whereNull('deleted_at')->count(),
                'discarded_count' => $group->whereNotNull('deleted_at')->count(),
                'total_valuation' => $group->sum('purchase_price'),
                'active_valuation' => $group->whereNull('deleted_at')->sum('purchase_price'),
                'written_off_valuation' => $group->whereNotNull('deleted_at')->sum('purchase_price'),
            ];
        });

        // Available years for filter
        $years = Asset::withTrashed()
            ->selectRaw('strftime("%Y", purchase_date) as year')
            ->distinct()
            ->pluck('year')
            ->filter()
            ->sortDesc()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        $categories = Asset::withTrashed()->select('category')->distinct()->pluck('category');

        return view('reports.index', compact(
            'allAssets',
            'totalOriginalValuation',
            'activeValuation',
            'discardedValuation',
            'statusCounts',
            'categoryBreakdown',
            'years',
            'categories',
            'selectedYear',
            'selectedCategory'
        ));
    }
}
