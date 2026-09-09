<?php

namespace App\Models;

use Database\Factories\PoktanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poktan extends Model
{
    /** @use HasFactory<PoktanFactory> */
    use HasFactory;

    protected $fillable = ['name', 'chairperson', 'district', 'village', 'commodity', 'member_count', 'phone', 'address', 'status'];

    public function alsintans(): HasMany
    {
        return $this->hasMany(Alsintan::class);
    }

    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class);
    }
}
