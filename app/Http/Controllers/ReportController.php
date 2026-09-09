<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', ['reports' => [
            ['label' => 'Rekap Alsintan', 'count' => Alsintan::count(), 'route' => 'alsintans.index'],
            ['label' => 'Rekap Saprodi', 'count' => Saprodi::count(), 'route' => 'saprodis.index'],
            ['label' => 'Tanaman Pangan', 'count' => Crop::count(), 'route' => 'crops.index'],
            ['label' => 'Data Poktan', 'count' => Poktan::count(), 'route' => 'poktans.index'],
        ]]);
    }
}
