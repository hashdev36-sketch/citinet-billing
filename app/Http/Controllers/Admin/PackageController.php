<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::withCount('vouchers')->orderBy('sort_order')->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('admin.packages.form', ['package' => new Package()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        $package = Package::create($data);
        AuditLog::record('package.created', $package, ['name' => $package->name]);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.form', compact('package'));
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $package->update($this->validated($request));
        AuditLog::record('package.updated', $package);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        // Never actually delete if it has sold vouchers/orders attached — deactivate instead,
        // to keep historical order/receipt data intact.
        if ($package->vouchers()->where('status', 'sold')->exists()) {
            $package->update(['is_active' => false]);
            AuditLog::record('package.deactivated', $package);
            return back()->with('success', 'Package has sales history, so it was deactivated instead of deleted.');
        }

        $package->delete();
        AuditLog::record('package.deleted', null, ['package_id' => $package->id, 'name' => $package->name]);

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_label' => ['required', 'string', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'device_limit' => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['integer'],
        ]);

        // Unchecked HTML checkboxes submit nothing at all, so 'is_active' would be
        // absent from the request — and since it's optional, validate() would then
        // omit it from the returned array too, meaning update() never touches the
        // column and an admin could never actually uncheck "Active" to deactivate a
        // package. $request->boolean() correctly treats "not present" as false.
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
