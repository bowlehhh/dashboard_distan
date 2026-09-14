<x-app-layout title="Input Hasil Panen">
    <section class="panel">
        <p class="form-intro">Lengkapi realisasi panen untuk {{ $crop->commodity }}. Data komoditas, kecamatan, dan luas tanam tidak dapat diubah oleh Penyuluh.</p>
        <form method="post" action="{{ route('crops.update', $crop) }}">
            @csrf @method('put')
            <div class="form-grid">
                <div class="field"><label>Komoditas</label><div>{{ $crop->commodity }}</div></div>
                <div class="field"><label>Kecamatan</label><div>{{ $crop->district }}</div></div>
                <div class="field"><label>Luas Tanam</label><div>{{ $crop->planted_area }} Ha</div></div>
                <div class="field"><label>Luas Panen (Ha)</label><input type="number" step="0.01" min="0" name="harvested_area" value="{{ old('harvested_area', $crop->harvested_area) }}" placeholder="Contoh: 10.50" required></div>
                <div class="field"><label>Jumlah Produksi</label><input name="production" value="{{ old('production', $crop->production) }}" placeholder="Contoh: 1500 Kg" required></div>
                <div class="field"><label>Tahun / Periode</label><input name="period" value="{{ old('period', $crop->period) }}" placeholder="Contoh: 2026" required></div>
                <div class="field full"><label>Keterangan</label><textarea name="notes" placeholder="Contoh: Hasil panen musim pertama telah ditimbang dan diverifikasi.">{{ old('notes', $crop->notes) }}</textarea></div>
            </div>
            <div class="form-footer"><a class="btn btn-outline" href="{{ route('crops.index') }}">Batal</a><button class="btn btn-primary">Simpan Hasil Panen</button></div>
        </form>
    </section>
</x-app-layout>
