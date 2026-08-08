<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::active()->get();

        return view('packages.index', compact('packages'));
    }

    public function show(Package $package): View
    {
        abort_unless($package->is_active, 404);

        $sites = $package->sitesWithStock();

        return view('packages.show', compact('package', 'sites'));
    }
}
