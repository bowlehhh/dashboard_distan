<?php

namespace App\Http\Controllers;

use App\Models\Saprodi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaprodiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $saprodis = Saprodi::query()->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))->when($request->category, fn ($query, $category) => $query->where('category', $category))->latest()->paginate(100)->withQueryString();

        return view('saprodis.index', compact('saprodis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('saprodis.form', ['saprodi' => new Saprodi]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Saprodi::create($this->validated($request));

        return redirect()->route('saprodis.index')->with('success', 'Data saprodi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Saprodi $saprodi): View
    {
        return view('saprodis.show', compact('saprodi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Saprodi $saprodi): View
    {
        return view('saprodis.form', compact('saprodi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Saprodi $saprodi): RedirectResponse
    {
        $data = $this->validated($request);
        $oldPhotoPath = $saprodi->photo_path;
        $saprodi->update($data);

        if ($oldPhotoPath && isset($data['photo_path']) && $data['photo_path'] !== $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()->route('saprodis.index')->with('success', 'Data saprodi diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Saprodi $saprodi): RedirectResponse
    {
        if ($saprodi->photo_path) {
            Storage::disk('public')->delete($saprodi->photo_path);
        }

        $saprodi->delete();

        return redirect()->route('saprodis.index')->with('success', 'Data saprodi dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'category' => ['required', 'in:Pupuk,Benih,Pestisida,Peralatan Pertanian,Lainnya'], 'custom_category' => ['nullable', 'required_if:category,Lainnya', 'string', 'max:120'], 'unit' => ['required', 'string', 'max:40'], 'stock' => ['required', 'numeric', 'min:0'], 'minimum_stock' => ['required', 'numeric', 'min:0'], 'photo' => ['nullable', 'image', 'max:2048'], 'notes' => ['nullable', 'string']]);

        if ($data['category'] === 'Lainnya') {
            $data['category'] = trim($data['custom_category']);
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('saprodi', 'public');
        }

        unset($data['custom_category'], $data['photo']);

        return $data;
    }
}
