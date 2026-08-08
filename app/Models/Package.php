<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'price', 'duration_label', 'duration_minutes',
        'device_limit', 'description', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Count of unused stock — what admins and the buy button both check.
     * Pass a Site to scope it to one location; omit it for the total across all sites.
     */
    public function availableStockCount(?Site $site = null): int
    {
        return $this->vouchers()
            ->where('status', 'unused')
            ->when($site, fn ($q) => $q->where('site_id', $site->id))
            ->count();
    }

    /** Active sites that currently have unused stock for this package — what the buy flow offers. */
    public function sitesWithStock()
    {
        // One grouped query instead of one COUNT per site — matters once there are
        // more than a handful of locations.
        $countsBySite = $this->vouchers()
            ->where('status', 'unused')
            ->selectRaw('site_id, count(*) as unused_count')
            ->groupBy('site_id')
            ->pluck('unused_count', 'site_id');

        return Site::active()
            ->get()
            ->filter(fn (Site $site) => ($countsBySite[$site->id] ?? 0) > 0)
            ->values();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
