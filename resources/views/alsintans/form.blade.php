<x-app-layout :title="$alsintan->exists ? 'Ubah Data Alsintan' : 'Tambah Alsintan'">
    <section class="panel">
        <p class="form-intro">Isi identitas, lokasi, dan kondisi alsintan. Poktan dapat dipilih bila alat dimiliki kelompok tani; kosongkan bila belum atau tidak terkait Poktan.</p>
        <form method="post" enctype="multipart/form-data" action="{{ $alsintan->exists ? route('alsintans.update', $alsintan) : route('alsintans.store') }}">
            @csrf
            @if ($alsintan->exists) @method('put') @endif
            <div class="form-grid">
                <div class="field"><label>Jenis Alsintan</label><input name="type" value="{{ old('type', $alsintan->type) }}" required></div>
                <div class="field"><label>Merk / tipe</label><input name="brand_type" value="{{ old('brand_type', $alsintan->brand_type) }}" required></div>
                <div class="field"><label>Nomor inventaris</label><input name="inventory_number" value="{{ old('inventory_number', $alsintan->inventory_number) }}" required>@error('inventory_number')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label>Poktan / pemilik</label><select name="poktan_id"><option value="">— Tidak terkait Poktan —</option>@foreach ($poktans as $poktan)<option value="{{ $poktan->id }}" @selected(old('poktan_id', $alsintan->poktan_id) === $poktan->id)>{{ $poktan->name }}</option>@endforeach</select></div>
                <div class="field"><label>Kecamatan</label><input name="district" value="{{ old('district', $alsintan->district) }}" required></div>
                <div class="field"><label>Kampung / desa</label><input name="village" value="{{ old('village', $alsintan->village) }}" required></div>
                <div class="field"><label>Tahun pengadaan</label><input type="number" name="procurement_year" value="{{ old('procurement_year', $alsintan->procurement_year) }}" required></div>
                <div class="field"><label>Kondisi</label><select name="condition">@foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $condition)<option @selected(old('condition', $alsintan->condition) === $condition)>{{ $condition }}</option>@endforeach</select></div>
                <div class="field"><label>Status penggunaan</label><input name="usage_status" value="{{ old('usage_status', $alsintan->usage_status ?? 'Digunakan') }}" required></div>
                <div class="field"><label>Foto</label><input type="file" name="photo" accept="image/*">@error('photo')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label>Latitude</label><input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $alsintan->latitude) }}" placeholder="Contoh: -0.239586"></div>
                <div class="field"><label>Longitude</label><input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $alsintan->longitude) }}" placeholder="Contoh: 115.094046"></div>
                <div class="field full"><label>Keterangan</label><textarea name="notes">{{ old('notes', $alsintan->notes) }}</textarea></div>
            </div>
            <div class="form-footer"><a class="btn btn-outline" href="{{ route('alsintans.index') }}">Batal</a><button class="btn btn-primary">Simpan Data</button></div>
        </form>
    </section>
</x-app-layout>
