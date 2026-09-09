@props(['title', 'subtitle' => 'Dinas Pertanian Kabupaten Kutai Barat'])
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $title }} · SIMANTAP</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="app-body app-density-{{ $appearanceSettings['density'] ?? 'comfortable' }}" @if(session('page_loader')) data-loader-reason="{{ session('page_loader') }}" @endif>
<x-page-loader />
<div class="app-shell">
    <aside class="sidebar" data-sidebar>
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 48 48" fill="none"><path d="M24 43c-11 0-19-7-19-18 0-8 7-15 19-20 12 5 19 12 19 20 0 11-8 18-19 18Z" stroke="currentColor" stroke-width="3"/><path d="M24 40V18m0 10c-5 0-9-3-11-8m11 4c5 0 9-3 11-8" stroke="currentColor" stroke-linecap="round" stroke-width="3"/></svg></span>
            <span>SIMANTAP<small>Sistem Informasi Alsintan<br>Saprodi &amp; Tanaman Pangan</small></span>
        </a>
        <nav>
            <p class="nav-label">Menu Utama</p>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="nav-icon">⌂</i>Dashboard</a>
            @if(auth()->user()->hasRole('admin', 'operator'))
                <a class="nav-link {{ request()->routeIs('poktans.*') ? 'active' : '' }}" href="{{ route('poktans.index') }}"><i class="nav-icon">◉</i>Data Poktan</a>
            @endif
            <a class="nav-link {{ request()->routeIs('alsintans.*') ? 'active' : '' }}" href="{{ route('alsintans.index') }}"><i class="nav-icon">▣</i>Data Alsintan</a>
            @if(auth()->user()->hasRole('admin', 'operator'))
                <a class="nav-link {{ request()->routeIs('saprodis.*') ? 'active' : '' }}" href="{{ route('saprodis.index') }}"><i class="nav-icon">◇</i>Data Saprodi</a>
                <a class="nav-link {{ request()->routeIs('crops.*') ? 'active' : '' }}" href="{{ route('crops.index') }}"><i class="nav-icon">◆</i>Tanaman Pangan</a>
            @endif
            <a class="nav-link {{ request()->routeIs('survey.*') ? 'active' : '' }}" href="{{ route('survey.create') }}"><i class="nav-icon">⌾</i>Survei Lapangan</a>
            @if(auth()->user()->hasRole('admin', 'operator'))<a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="nav-icon">▤</i>Laporan</a>@endif
            @if(auth()->user()->hasRole('admin'))
                <p class="nav-label">Administrasi</p>
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="nav-icon">●</i>Manajemen Akun</a>
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="nav-icon">⚙</i>Pengaturan</a>
            @endif
        </nav>
        <div class="sidebar-footer"><form id="logout-form" method="post" action="{{ route('logout') }}">@csrf<button class="nav-link logout" type="button" data-logout-trigger><i class="nav-icon">↪</i>Keluar</button></form></div>
    </aside>
    <main class="main">
        <header class="topbar"><button class="mobile-toggle" data-menu-toggle aria-label="Buka menu">☰</button><span class="topbar-brand">SIMANTAP</span><span class="topbar-title">Sistem Informasi Alsintan, Saprodi, dan Tanaman Pangan</span><div class="topbar-profile">@if(! empty($institutionProfile['logo']))<img class="topbar-institution__logo" src="{{ asset('storage/'.$institutionProfile['logo']) }}" alt="Logo {{ $institutionProfile['name'] ?? 'instansi' }}">@else<i class="avatar">{{ strtoupper(str(auth()->user()->name)->substr(0, 1)) }}</i>@endif<div><strong>{{ $institutionProfile['name'] ?? 'SIMANTAP' }}</strong><small>{{ auth()->user()->name }} · {{ auth()->user()->role === 'ppl' ? 'Penyuluh Pertanian' : ucfirst(auth()->user()->role) }}</small></div></div></header>
        <section class="content">
            <div class="page-head"><div><h1>{{ $title }}</h1><p class="subhead">{{ $subtitle === 'Dinas Pertanian Kabupaten Kutai Barat' ? ($institutionProfile['name'] ?? $subtitle) : $subtitle }}</p></div>{{ $actions ?? '' }}</div>
            @if(session('success'))<div class="notice">✓ {{ session('success') }}</div>@endif
            @if(session('error'))<div class="notice error">! {{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="notice error" role="alert">
                    <strong>Periksa kembali isian Anda.</strong>
                    <ul class="validation-errors">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $slot }}
        </section>
    </main>
    <div class="logout-modal" data-logout-modal hidden aria-hidden="true">
        <button class="logout-modal__backdrop" type="button" data-logout-dismiss aria-label="Tutup konfirmasi keluar"></button>
        <section class="logout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="logout-title" aria-describedby="logout-description" tabindex="-1" data-logout-dialog>
            <button class="logout-modal__close" type="button" data-logout-dismiss aria-label="Tutup">×</button>
            <div class="logout-modal__icon" aria-hidden="true">↪</div>
            <h2 id="logout-title">Keluar dari SIMANTAP?</h2>
            <p id="logout-description">Sesi Anda akan diakhiri dan Anda akan kembali ke halaman utama.</p>
            <div class="logout-modal__actions">
                <button class="logout-modal__cancel" type="button" data-logout-dismiss>Batal</button>
                <button class="logout-modal__confirm" type="submit" form="logout-form">Ya, Keluar</button>
            </div>
        </section>
    </div>
</div>
</body></html>
