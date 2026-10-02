<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'instructions', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DisasterCategory::class, 'category_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function scopeAvailableForReporting(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $category) => $category->where('is_active', true));
    }
}
