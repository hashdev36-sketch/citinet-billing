<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function index(): View
    {
        $sites = Site::withCount('vouchers')->orderBy('sort_order')->get();

        return view('admin.sites.index', compact('sites'));
    }

    public function create(): View
    {
        return view('admin.sites.form', ['site' => new Site()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        $site = Site::create($data);
        AuditLog::record('site.created', $site, ['name' => $site->name]);

        return redirect()->route('admin.sites.index')->with('success', "{$site->name} added — you can now import voucher stock for it.");
    }

    public function edit(Site $site): View
    {
        return view('admin.sites.form', compact('site'));
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $site->update($this->validated($request));
        AuditLog::record('site.updated', $site);

        return redirect()->route('admin.sites.index')->with('success', 'Location updated.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        // Never delete a site with sold vouchers/orders attached — deactivate instead,
        // to keep historical order data and receipts pointing at a real location.
        if ($site->vouchers()->where('status', 'sold')->exists()) {
            $site->update(['is_active' => false]);
            AuditLog::record('site.deactivated', $site);

            return back()->with('success', "{$site->name} has sales history, so it was deactivated instead of deleted.");
        }

        $site->delete();
        AuditLog::record('site.deleted', null, ['site_id' => $site->id, 'name' => $site->name]);

        return redirect()->route('admin.sites.index')->with('success', 'Location deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'telegram_admin_chat_id' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['integer'],
        ]);

        // See PackageController::validated() for why this can't just be a 'boolean'
        // validation rule — an unchecked checkbox submits nothing, so the key would be
        // silently absent and update() would never actually deactivate a location.
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
