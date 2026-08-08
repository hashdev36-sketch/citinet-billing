<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = ['actor_type', 'actor_id', 'action', 'subject_type', 'subject_id', 'meta', 'ip_address'];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(string $action, ?Model $subject = null, array $meta = []): self
    {
        $actor = auth('admin')->user() ?? auth()->user();

        return static::create([
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'meta' => $meta,
            'ip_address' => request()->ip(),
        ]);
    }
}
