<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Package;
use App\Models\Site;
use App\Models\Voucher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CsvVoucherImportService
{
    /**
     * Import format: "username,password" or "username,password,package_slug"
     * or "username,password,package_slug,site_slug".
     *
     * $defaultPackage / $defaultSite are used when a row doesn't specify its own
     * package/site column — this lets an admin import a single-site, single-package
     * CSV (the common case: "here's 200 new Daily vouchers for Citinet 3") without
     * having to repeat the same two columns on every row, while still supporting a
     * mixed CSV that spans multiple sites/packages via per-row overrides.
     *
     * Duplicate (site_id, package_id, username) rows are skipped rather than erroring
     * the whole batch — the unique DB constraint is the real backstop, this just lets
     * us report a clean "imported X, skipped Y duplicates" summary.
     */
    public function import(UploadedFile $file, ?Package $defaultPackage = null, ?Site $defaultSite = null): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $lineNumber = 0;

        $packageCache = [];
        $siteCache = [];

        DB::transaction(function () use ($handle, $defaultPackage, $defaultSite, &$imported, &$skipped, &$errors, &$lineNumber, &$packageCache, &$siteCache) {
            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;

                if ($lineNumber === 1 && strtolower($row[0] ?? '') === 'username') {
                    continue; // skip header row if present
                }

                [$username, $password, $packageSlug, $siteSlug] = array_pad($row, 4, null);
                $username = trim((string) $username);
                $password = trim((string) $password);
                $packageSlug = $packageSlug ? trim($packageSlug) : null;
                $siteSlug = $siteSlug ? trim($siteSlug) : null;

                if ($username === '' || $password === '') {
                    $errors[] = "Line {$lineNumber}: missing username or password.";
                    continue;
                }

                $package = $defaultPackage;
                if ($packageSlug) {
                    $package = $packageCache[$packageSlug] ??= Package::where('slug', $packageSlug)->first();
                }

                if (! $package) {
                    $errors[] = "Line {$lineNumber}: no package specified or found for '{$packageSlug}'.";
                    continue;
                }

                $site = $defaultSite;
                if ($siteSlug) {
                    $site = $siteCache[$siteSlug] ??= Site::where('slug', $siteSlug)->first();
                }

                if (! $site) {
                    $errors[] = "Line {$lineNumber}: no site/location specified or found for '{$siteSlug}'.";
                    continue;
                }

                $exists = Voucher::where('site_id', $site->id)
                    ->where('package_id', $package->id)
                    ->where('username', $username)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                Voucher::create([
                    'site_id' => $site->id,
                    'package_id' => $package->id,
                    'username' => $username,
                    'password' => $password,
                    'status' => 'unused',
                    'imported_at' => now(),
                ]);

                $imported++;
            }
        });

        fclose($handle);

        AuditLog::record('voucher.csv_import', meta: [
            'imported' => $imported,
            'skipped' => $skipped,
            'error_count' => count($errors),
        ]);

        return compact('imported', 'skipped', 'errors');
    }
}
