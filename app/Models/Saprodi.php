<?php

namespace App\Models;

use Database\Factories\SaprodiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saprodi extends Model
{
    /** @use HasFactory<SaprodiFactory> */
    use HasFactory;

    protected $fillable = ['name', 'poktan_id', 'unit', 'quantity_distributed', 'distributed_year', 'photo_path', 'notes'];

    protected function casts(): array
    {
        return [
            'quantity_distributed' => 'decimal:2',
            'distributed_year' => 'integer',
        ];
    }

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }
}
