<?php

namespace App\Models;

use Database\Factories\CropFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Crop extends Model
{
    /** @use HasFactory<CropFactory> */
    use HasFactory;

    protected $fillable = ['commodity', 'poktan_id', 'district', 'planted_area', 'harvested_area', 'production', 'unit', 'period', 'notes'];

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }
}
