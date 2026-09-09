@props(['value'])
@php($class = match($value) { 'Aktif', 'Baik', 'Tersedia', 'Disetujui', 'Selesai', 'Diajukan' => 'success', 'Rusak Ringan', 'Perlu Restok', 'Menunggu Verifikasi', 'Perlu Perbaikan' => 'warning', 'Rusak Berat', 'Nonaktif', 'Ditolak' => 'danger', default => 'neutral' })
<span class="badge {{ $class }}">{{ $value }}</span>
