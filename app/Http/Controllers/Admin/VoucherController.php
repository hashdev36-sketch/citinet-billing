<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Site;
use App\Models\Voucher;
use App\Services\CsvVoucherImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $vouchers = Voucher::with(['package', 'site', 'customer'])
            ->when($request->filled('site_id'), fn ($q) => $q->where('site_id', $request->site_id))
            ->when($request->filled('package_id'), fn ($q) => $q->where('package_id', $request->package_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(50);

        $packages = Package::orderBy('sort_order')->get();
        $sites = Site::orderBy('sort_order')->get();

        return view('admin.vouchers.index', compact('vouchers', 'packages', 'sites'));
    }

    public function showImportForm(): View
    {
        $packages = Package::active()->get();
        $sites = Site::active()->get();

        return view('admin.vouchers.import', compact('packages', 'sites'));
    }

    public function import(Request $request, CsvVoucherImportService $importer): RedirectResponse
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'package_id' => ['nullable', 'exists:packages,id'], // optional if CSV has a per-row package column
            'site_id' => ['nullable', 'exists:sites,id'],        // optional if CSV has a per-row site column
        ]);

        $defaultPackage = $validated['package_id'] ? Package::find($validated['package_id']) : null;
        $defaultSite = $validated['site_id'] ? Site::find($validated['site_id']) : null;

        $result = $importer->import($request->file('csv_file'), $defaultPackage, $defaultSite);

        return back()->with('success', "Imported {$result['imported']} vouchers, skipped {$result['skipped']} duplicates.")
            ->with('import_errors', $result['errors']);
    }
}
