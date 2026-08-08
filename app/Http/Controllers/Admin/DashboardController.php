<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Order::whereDate('paid_at', today())->where('status', 'fulfilled')->sum('amount');
        $thisWeek = Order::where('paid_at', '>=', now()->startOfWeek())->where('status', 'fulfilled')->sum('amount');
        $thisMonth = Order::where('paid_at', '>=', now()->startOfMonth())->where('status', 'fulfilled')->sum('amount');

        $mostPopularPackage = Order::where('status', 'fulfilled')
            ->select('package_id', DB::raw('count(*) as sales'))
            ->groupBy('package_id')
            ->orderByDesc('sales')
            ->with('package')
            ->first();

        $sites = Site::active()->get();
        $packages = Package::active()->get();

        // Stock matrix: rows = packages, columns = sites, cell = unused voucher count.
        // Single grouped query rather than one COUNT per (package, site) pair — that
        // was 28 queries for 7 packages × 4 sites and only grows as either list does.
        $stockCounts = DB::table('vouchers')
            ->where('status', 'unused')
            ->select('package_id', 'site_id', DB::raw('count(*) as unused_count'))
            ->groupBy('package_id', 'site_id')
            ->get()
            ->groupBy('package_id');

        $stockMatrix = $packages->map(function (Package $package) use ($sites, $stockCounts) {
            $rowsBySite = ($stockCounts->get($package->id) ?? collect())->pluck('unused_count', 'site_id');

            return [
                'package' => $package,
                'bySite' => $sites->mapWithKeys(fn (Site $site) => [
                    $site->id => $rowsBySite[$site->id] ?? 0,
                ]),
            ];
        });

        $needsAttention = Order::where('status', 'paid')->whereNull('voucher_id')->count(); // paid but not fulfilled — out-of-stock cases

        $revenueByDay = Order::where('status', 'fulfilled')
            ->where('paid_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(paid_at) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.dashboard', compact(
            'today', 'thisWeek', 'thisMonth', 'mostPopularPackage',
            'sites', 'stockMatrix', 'needsAttention', 'revenueByDay'
        ));
    }
}
