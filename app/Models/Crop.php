<?php

namespace App\Models;

use Database\Factories\CropFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    /** @use HasFactory<CropFactory> */
    use HasFactory;

    protected $fillable = ['commodity', 'district', 'planted_area', 'harvested_area', 'production', 'period', 'notes'];
}
