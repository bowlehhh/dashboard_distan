<?php

namespace App\Models;

use Database\Factories\SaprodiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprodi extends Model
{
    /** @use HasFactory<SaprodiFactory> */
    use HasFactory;

    protected $fillable = ['name', 'category', 'unit', 'stock', 'minimum_stock', 'notes'];

    protected $appends = ['stock_status'];

    public function getStockStatusAttribute(): string
    {
        return $this->stock <= $this->minimum_stock ? 'Perlu Restok' : 'Tersedia';
    }
}
