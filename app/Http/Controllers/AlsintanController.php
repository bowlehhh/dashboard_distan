<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Poktan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AlsintanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $alsintans = Alsintan::with('poktan')->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('type', 'like', "%{$search}%")->orWhere('brand_type', 'like', "%{$search}%")->orWhere('inventory_number', 'like', "%{$search}%")))->when($request->district, fn ($query, $district) => $query->where('district', $district))->when($request->condition, fn ($query, $condition) => $query->where('condition', $condition))->latest()->paginate(100)->withQueryString();

        return view('alsintans.index', compact('alsintans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan data baru.');

        return view('alsintans.form', ['alsintan' => new Alsintan, 'poktans' => Poktan::query()->where('status', 'Aktif')->orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Alsintan::create($this->validated($request));

        return redirect()->route('alsintans.index')->with('success', 'Data alsintan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Alsintan $alsintan): View
    {
        return view('alsintans.show', compact('alsintan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alsintan $alsintan): View
    {
        return view('alsintans.form', ['alsintan' => $alsintan, 'poktans' => Poktan::query()->where('status', 'Aktif')->orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alsintan $alsintan): RedirectResponse
    {
        $data = $this->validated($request, $alsintan);
        $oldPhotoPath = $alsintan->photo_path;
        $alsintan->update($data);

        if ($oldPhotoPath && isset($data['photo_path']) && $data['photo_path'] !== $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()->route('alsintans.index')->with('success', 'Data alsintan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alsintan $alsintan): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menghapus data.');
        if ($alsintan->photo_path) {
            Storage::disk('public')->delete($alsintan->photo_path);
        }

        $alsintan->delete();

        return redirect()->route('alsintans.index')->with('success', 'Data alsintan dihapus.');
    }

    private function validated(Request $request, ?Alsintan $alsintan = null): array
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:120'], 'brand_type' => ['required', 'string', 'max:120'], 'inventory_number' => ['required', 'string', 'max:80', $this->inventoryNumberRule($alsintan)], 'poktan_id' => ['nullable', 'exists:poktans,id'], 'district' => ['required', 'string', 'max:120'], 'village' => ['required', 'string', 'max:120'], 'procurement_year' => ['required', 'integer', 'between:1950,'.now()->year], 'condition' => ['required', 'in:Baik,Rusak Ringan,Rusak Berat'], 'usage_status' => ['required', 'string', 'max:100'], 'photo' => ['nullable', 'image', 'max:2048'], 'notes' => ['nullable', 'string'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180']]);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('alsintan', 'public');
        }
        unset($data['photo']);

        return $data;
    }

    private function inventoryNumberRule(?Alsintan $alsintan = null): Unique
    {
        return Rule::unique('alsintans', 'inventory_number')->ignore($alsintan);
    }
}
