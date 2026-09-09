<?php

namespace App\Http\Controllers;

use App\Models\Poktan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PoktanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $poktans = Poktan::query()
            ->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('chairperson', 'like', "%{$search}%")))
            ->when($request->district, fn ($query, $district) => $query->where('district', $district))
            ->when($request->commodity, fn ($query, $commodity) => $query->where('commodity', $commodity))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(10)->withQueryString();

        return view('poktans.index', compact('poktans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('poktans.form', ['poktan' => new Poktan]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Poktan::create($this->validated($request));

        return redirect()->route('poktans.index')->with('success', 'Data kelompok tani berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Poktan $poktan): View
    {
        return view('poktans.show', compact('poktan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poktan $poktan): View
    {
        return view('poktans.form', compact('poktan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poktan $poktan): RedirectResponse
    {
        $poktan->update($this->validated($request));

        return redirect()->route('poktans.index')->with('success', 'Data kelompok tani diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poktan $poktan): RedirectResponse
    {
        $poktan->delete();

        return redirect()->route('poktans.index')->with('success', 'Data kelompok tani dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:120'], 'chairperson' => ['required', 'string', 'max:120'], 'district' => ['required', 'string', 'max:120'], 'village' => ['required', 'string', 'max:120'], 'commodity' => ['required', 'string', 'max:120'], 'member_count' => ['required', 'integer', 'min:0'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string'], 'status' => ['required', 'in:Aktif,Nonaktif']]);
    }
}
