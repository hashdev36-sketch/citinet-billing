<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'package_id', 'username', 'password', 'status',
        'customer_id', 'order_id', 'imported_at', 'sold_at', 'expires_at',
    ];

    protected $hidden = ['password']; // never serialize raw password in API/JSON responses by accident

    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
            'sold_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Explicit accessor for showing the password on receipts/dashboard, since it's hidden by default. */
    public function revealPassword(): string
    {
        return $this->password;
    }
}
