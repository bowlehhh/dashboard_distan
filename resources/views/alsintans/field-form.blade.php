<x-app-layout title="Pembaruan Lapangan Alsintan">
    <section class="panel">
        <p class="form-intro">Perbarui kondisi, foto, dan lokasi alsintan dari lapangan. Data identitas alat tidak dapat diubah oleh Penyuluh.</p>
        <form method="post" enctype="multipart/form-data" action="{{ route('alsintans.update', $alsintan) }}">
            @csrf @method('put')
            <div class="form-grid">
                <div class="field"><label>Jenis Alsintan</label><div>{{ $alsintan->type }}</div></div>
                <div class="field"><label>Nomor Inventaris</label><div>{{ $alsintan->inventory_number }}</div></div>
                <div class="field"><label>Kondisi</label><select name="condition">@foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $condition)<option @selected(old('condition', $alsintan->condition) === $condition)>{{ $condition }}</option>@endforeach</select></div>
                <div class="field"><label>Foto dari kamera</label><input type="file" name="photo" accept="image/*" capture="environment" aria-describedby="field-photo-hint"><small id="field-photo-hint">Ambil foto terbaru yang memperlihatkan kondisi alsintan.</small>@error('photo')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field full"><label>Lokasi Google Maps</label><input type="url" name="google_maps_url" value="{{ old('google_maps_url', $alsintan->google_maps_url) }}" placeholder="Ambil lokasi saat ini atau tempel link Google Maps" data-location-input><button class="btn btn-outline" type="button" data-current-location>Ambil lokasi saat ini</button>@error('google_maps_url')<span class="error">{{ $message }}</span>@enderror</div>
            </div>
            <div class="form-footer"><a class="btn btn-outline" href="{{ route('alsintans.index') }}">Batal</a><button class="btn btn-primary">Simpan Pembaruan Lapangan</button></div>
        </form>
    </section>
</x-app-layout>
