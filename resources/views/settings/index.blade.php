<x-app-layout title="Pengaturan">
    <nav class="settings-nav" aria-label="Bagian pengaturan">
        @if(auth()->user()->hasRole('admin'))
            <a href="#profil">Profil Instansi</a><a href="#akses">Hak Akses</a><a href="#backup">Backup Data</a>
        @endif
        @if(auth()->user()->hasRole('admin', 'operator'))
            <a href="#tampilan">Tampilan</a><a href="#keamanan">Keamanan Akun</a>
        @endif
    </nav>

    <section class="settings-stack">
        @if(auth()->user()->hasRole('admin'))
        <article class="settings-card" id="profil">
            <div class="settings-card__heading"><i>✎</i><div><h2>Profil Instansi</h2><p>Perbarui identitas yang digunakan oleh SIMANTAP.</p></div></div>
            <form method="post" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" aria-label="Form profil instansi">
                @csrf @method('put')
                <div class="form-grid">
                    <div class="field full"><label for="name">Nama instansi</label><input id="name" name="name" value="{{ old('name', $profile['name']) }}" required></div>
                    <div class="field"><label for="address">Alamat</label><input id="address" name="address" value="{{ old('address', $profile['address']) }}"></div>
                    <div class="field"><label for="phone">Telepon</label><input id="phone" name="phone" value="{{ old('phone', $profile['phone']) }}"></div>
                    <div class="field"><label for="email">Email instansi</label><input id="email" type="email" name="email" value="{{ old('email', $profile['email']) }}"></div>
                    <div class="field"><label for="logo">Logo instansi (opsional)</label><input id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp"></div>
                </div>
                <div class="form-footer"><button class="btn btn-primary">Simpan Profil</button></div>
            </form>
        </article>

        <article class="settings-card" id="akses">
            <div class="settings-card__heading"><i>⌘</i><div><h2>Manajemen Hak Akses</h2><p>Atur peran, status aktif, dan akun setiap pengguna.</p></div></div>
            <div class="settings-card__action"><p>Pengaturan peran Admin, Operator, dan Penyuluh dikelola dari halaman Manajemen Akun.</p><a class="btn btn-outline" href="{{ route('users.index') }}">Kelola Pengguna <span>→</span></a></div>
        </article>

        <article class="settings-card" id="backup">
            <div class="settings-card__heading"><i>▤</i><div><h2>Backup Data</h2><p>Unduh atau pulihkan data operasional SIMANTAP.</p></div></div>
            <div class="backup-summary"><span><b>{{ $dataCounts['poktans'] }}</b> Poktan</span><span><b>{{ $dataCounts['alsintans'] }}</b> Alsintan</span><span><b>{{ $dataCounts['saprodis'] }}</b> Saprodi</span><span><b>{{ $dataCounts['crops'] }}</b> Tanaman</span></div>
            <div class="settings-card__action"><p>Backup berformat JSON dan mencakup data operasional serta pengaturan instansi. Akun pengguna tidak termasuk.</p><a class="btn btn-primary" href="{{ route('settings.backup.download') }}">↓ Unduh Backup</a></div>
            <form class="restore-form" method="post" action="{{ route('settings.backup.restore') }}" enctype="multipart/form-data" data-confirm="Pulihkan backup? Data operasional saat ini akan diganti.">
                @csrf
                <div class="field"><label for="backup">Pulihkan dari file backup</label><input id="backup" type="file" name="backup" accept="application/json,.json" required></div><button class="btn btn-outline">Pulihkan Data</button>
            </form>
        </article>
        @endif

        <article class="settings-card" id="tampilan">
            <div class="settings-card__heading"><i>◐</i><div><h2>Tampilan</h2><p>Pilih kepadatan antarmuka yang nyaman untuk digunakan.</p></div></div>
            <form method="post" action="{{ route('settings.appearance.update') }}" aria-label="Form tampilan">@csrf @method('put')<div class="field settings-choice"><label for="density">Mode tampilan</label><select id="density" name="density"><option value="comfortable" @selected(old('density', $appearance['density'] ?? 'comfortable') === 'comfortable')>Nyaman</option><option value="bright" @selected(old('density', $appearance['density'] ?? 'comfortable') === 'bright')>Terang</option></select></div><div class="form-footer"><button class="btn btn-primary">Simpan Tampilan</button></div></form>
        </article>

        <article class="settings-card" id="keamanan">
            <div class="settings-card__heading"><i>✓</i><div><h2>Keamanan Akun</h2><p>Ganti kata sandi akun Anda sendiri.</p></div></div>
            <form method="post" action="{{ route('settings.security.update') }}" aria-label="Form keamanan akun" data-security-form>@csrf @method('put')<div class="field"><label for="security-user">Akun Anda</label><input id="security-user" type="text" value="{{ auth()->user()->email }} — {{ auth()->user()->name }}" readonly></div><div class="form-grid"><div class="field full" data-current-password-field><label for="current_password">Kata sandi saat ini</label><input id="current_password" type="password" name="current_password" autocomplete="current-password"></div><div class="field"><label for="password">Kata sandi baru</label><input id="password" type="password" name="password" autocomplete="new-password" required></div><div class="field"><label for="password_confirmation">Konfirmasi kata sandi baru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div></div><label class="settings-checkbox"><input type="checkbox" name="logout_other_sessions" value="1" checked> Akhiri sesi pada perangkat lain</label><div class="form-footer"><button class="btn btn-primary">Perbarui Keamanan</button></div></form>
        </article>
    </section>
</x-app-layout>
