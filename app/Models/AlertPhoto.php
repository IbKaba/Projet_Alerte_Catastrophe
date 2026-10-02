<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertPhoto extends Model
{
    public $timestamps = false;

    protected $fillable = ['alert_id', 'path', 'original_name', 'position'];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
