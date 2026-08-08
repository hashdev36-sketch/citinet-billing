<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Site;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $packages = Package::active()->get();
        $siteCount = Site::where('is_active', true)->count();

        return view('home.index', compact('packages', 'siteCount'));
    }
}
