<?php

namespace App\Http\Controllers;

use App\Models\Poktan;
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
        $search = $request->string('search')->trim()->toString();

        $saprodis = Saprodi::query()
            ->with('poktan')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->whereAny(['name', 'unit', 'notes'], 'like', "%{$search}%")
                    ->orWhereHas('poktan', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(100)
            ->withQueryString();

        return view('saprodis.index', compact('saprodis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan data baru.');

        return view('saprodis.form', ['saprodi' => new Saprodi, 'poktans' => Poktan::query()->orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan data baru.');

        Saprodi::create($this->validated($request));

        return redirect()->route('saprodis.index')->with('success', 'Data saprodi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Saprodi $saprodi): View
    {
        $saprodi->load('poktan');

        return view('saprodis.show', compact('saprodi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Saprodi $saprodi): View
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Akun Anda hanya dapat melihat data saprodi.');

        return view('saprodis.form', ['saprodi' => $saprodi, 'poktans' => Poktan::query()->orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Saprodi $saprodi): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Akun Anda hanya dapat melihat data saprodi.');

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
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menghapus data.');
        if ($saprodi->photo_path) {
            Storage::disk('public')->delete($saprodi->photo_path);
        }

        $saprodi->delete();

        return redirect()->route('saprodis.index')->with('success', 'Data saprodi dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'poktan_id' => ['nullable', 'exists:poktans,id'], 'unit' => ['required', 'string', 'max:40'], 'quantity_distributed' => ['required', 'numeric', 'min:0'], 'distributed_year' => ['required', 'integer', 'between:1950,'.now()->year], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'notes' => ['nullable', 'string']]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('saprodi', 'public');
        }

        unset($data['photo']);

        return $data;
    }
}
