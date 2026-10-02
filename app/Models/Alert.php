<?php

namespace App\Models;

use App\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'disaster_id', 'latitude', 'longitude', 'address', 'google_place_id',
        'description', 'status', 'rejection_reason', 'validated_by', 'validated_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'status' => AlertStatus::class,
            'validated_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disaster(): BelongsTo
    {
        return $this->belongsTo(Disaster::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AlertPhoto::class)->orderBy('position');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AlertStatus::PENDING->value,
            AlertStatus::VALIDATED->value,
            AlertStatus::RESOLVED->value,
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AlertStatus::PENDING->value);
    }
}
