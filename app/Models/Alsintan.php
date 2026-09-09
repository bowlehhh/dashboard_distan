<?php

namespace App\Models;

use Database\Factories\AlsintanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alsintan extends Model
{
    /** @use HasFactory<AlsintanFactory> */
    use HasFactory;

    protected $fillable = ['type', 'brand_type', 'inventory_number', 'poktan_id', 'district', 'village', 'procurement_year', 'condition', 'usage_status', 'photo_path', 'notes', 'latitude', 'longitude'];

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }
}
