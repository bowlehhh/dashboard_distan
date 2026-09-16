<?php

namespace App\Http\Controllers;

use App\Models\Crop;
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
        $search = $request->string('search')->trim()->toString();
        $crops = Crop::query()
            ->when($search !== '', fn ($query) => $query->whereAny(
                ['commodity', 'district', 'production', 'period', 'notes'],
                'like',
                "%{$search}%",
            ))
            ->when($request->commodity, fn ($query, $commodity) => $query->where('commodity', $commodity))
            ->when($request->district, fn ($query, $district) => $query->where('district', $district))
            ->latest('period')
            ->paginate(100)
            ->withQueryString();
        $summary = Crop::query()->selectRaw('commodity, sum(planted_area) as planted_area, sum(harvested_area) as harvested_area')->groupBy('commodity')->orderByDesc('planted_area')->get();

        return view('crops.index', compact('crops', 'summary'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan data baru.');

        return view('crops.form', ['crop' => new Crop]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan data baru.');

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
        abort_unless(auth()->user()->hasRole('admin', 'penyuluh'), 403, 'Akun Anda hanya dapat melihat data tanaman pangan.');

        if (auth()->user()->hasRole('penyuluh')) {
            return view('crops.harvest-form', compact('crop'));
        }

        return view('crops.form', compact('crop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin', 'penyuluh'), 403, 'Akun Anda hanya dapat melihat data tanaman pangan.');

        $crop->update(auth()->user()->hasRole('penyuluh') ? $this->validatedHarvest($request) : $this->validated($request));

        return redirect()->route('crops.index')->with('success', 'Data tanaman pangan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menghapus data.');
        $crop->delete();

        return redirect()->route('crops.index')->with('success', 'Data tanaman pangan dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['commodity' => ['required', 'string', 'max:120'], 'district' => ['required', 'string', 'max:120'], 'planted_area' => ['required', 'numeric', 'min:0'], 'harvested_area' => ['required', 'numeric', 'min:0'], 'production' => ['required', 'string', 'max:100'], 'period' => ['required', 'string', 'max:40'], 'notes' => ['nullable', 'string']]);
    }

    private function validatedHarvest(Request $request): array
    {
        return $request->validate(['harvested_area' => ['required', 'numeric', 'min:0'], 'production' => ['required', 'string', 'max:100'], 'period' => ['required', 'string', 'max:40'], 'notes' => ['nullable', 'string']]);
    }
}
