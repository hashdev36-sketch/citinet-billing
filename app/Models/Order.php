<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_id', 'site_id', 'package_id', 'voucher_id',
        'amount', 'currency', 'status', 'paystack_reference',
        'paid_at', 'fulfilled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'fulfilled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateOrderNumber(): string
    {
        // CIT-20260806-8K2W4Q — date-scoped and human-readable on receipts/support tickets.
        // Alphanumeric random suffix (36^6 ≈ 2.2 billion combinations/day) rather than a
        // 4-digit number (only 9,000/day) — the old range was small enough that a busy
        // day could realistically hit a unique-constraint collision on order_number.
        return 'CIT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }
}
