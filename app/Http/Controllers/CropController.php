<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Poktan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $crops = Crop::with('poktan')->when($request->commodity, fn ($query, $commodity) => $query->where('commodity', $commodity))->when($request->district, fn ($query, $district) => $query->where('district', $district))->latest('period')->paginate(100)->withQueryString();
        $summary = Crop::selectRaw('commodity, sum(planted_area) as planted_area, sum(production) as production')->groupBy('commodity')->orderByDesc('planted_area')->get();

        return view('crops.index', compact('crops', 'summary'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('crops.form', ['crop' => new Crop, 'poktans' => Poktan::orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Crop::create($this->validated($request));

        return redirect()->route('crops.index')->with('success', 'Data tanaman pangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Crop $crop): View
    {
        return view('crops.show', compact('crop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crop $crop): View
    {
        return view('crops.form', ['crop' => $crop, 'poktans' => Poktan::orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop): RedirectResponse
    {
        $crop->update($this->validated($request));

        return redirect()->route('crops.index')->with('success', 'Data tanaman pangan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop): RedirectResponse
    {
        $crop->delete();

        return redirect()->route('crops.index')->with('success', 'Data tanaman pangan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['commodity' => ['required', 'string', 'max:120'], 'poktan_id' => ['nullable', 'exists:poktans,id'], 'district' => ['required', 'string', 'max:120'], 'planted_area' => ['required', 'numeric', 'min:0'], 'harvested_area' => ['required', 'numeric', 'min:0'], 'production' => ['required', 'numeric', 'min:0'], 'unit' => ['required', 'string', 'max:40'], 'period' => ['required', 'string', 'max:40'], 'notes' => ['nullable', 'string']]);
    }
}
