<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMANTAP · Dinas Pertanian Kutai Barat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public public-home" data-loader-reason="{{ session('page_loader', 'initial') }}">
    <x-page-loader />
    <main class="landing-shell">
        @if(session('success'))
            <p class="landing-notice" role="status" data-auto-dismiss="2600">{{ session('success') }}</p>
        @endif
        <header class="landing-header">
            <a class="landing-brand" href="{{ route('home') }}" aria-label="SIMANTAP, halaman utama">
                <img class="landing-brand__image" src="{{ asset('images/simantap-logo-reference.png') }}" alt="SIMANTAP — Sistem Informasi Alsintan Terpadu, Modern, dan Transparan">
            </a>

            <a class="landing-mobile-login landing-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }}</a>
            <button class="landing-menu-toggle" type="button" data-landing-menu-toggle aria-expanded="false" aria-controls="landing-menu" aria-label="Buka menu navigasi">☰</button>
            <nav class="landing-nav" id="landing-menu" aria-label="Navigasi utama">
                <button class="landing-menu-close" type="button" data-landing-menu-close aria-label="Tutup menu navigasi">×</button>
                <a class="{{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('public.poktans') }}">Data Poktan</a>
                <a href="{{ route('public.alsintans') }}">Data Alsintan</a>
                <a href="{{ route('public.saprodis') }}">Data Saprodi</a>
                <a href="{{ route('public.reports') }}">Laporan</a>
                <a class="landing-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }}</a>
            </nav>
            <button class="landing-menu-backdrop" type="button" data-landing-menu-backdrop aria-label="Tutup menu navigasi"></button>
        </header>

        <section class="landing-hero" aria-labelledby="welcome-heading">
            <div class="landing-copy">
                <p class="landing-eyebrow">DINAS PERTANIAN KABUPATEN KUTAI BARAT</p>
                <h1 id="welcome-heading">Selamat Datang<br>di SIMANTAP</h1>
                <p class="landing-description" id="tentang">Digitalisasi data pertanian untuk mewujudkan tata kelola yang terintegrasi, akurat, dan real-time di Kabupaten Kutai Barat.</p>
                <div class="landing-actions">
                    <a class="landing-button landing-button--primary" href="{{ config('simantap.institution_url') }}">Web Dinas Pertanian</a>
                    <a class="landing-button landing-button--secondary" href="#statistik">Pelajari SIMANTAP</a>
                </div>

                <section class="landing-stats" id="statistik" aria-label="Ringkasan data pertanian">
                    @foreach($stats as $stat)
                        <article class="landing-stat">
                            <strong>{{ number_format($stat['value'], 0, ',', '.') }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </article>
                    @endforeach
                </section>
            </div>

            <div class="landing-visual">
                <div class="office-collage" aria-label="Gedung Dinas Pertanian Kutai Barat">
                    <svg class="office-mosaic" viewBox="0 0 583 527" preserveAspectRatio="none" role="img" aria-label="Kolase Gedung Dinas Pertanian Kutai Barat">
                        <defs>
                            <clipPath id="office-mosaic-clip">
                                <polygon points="87.5,100 177.5,100 224.5,177 177.5,254 87.5,254 40.5,177"/>
                                <polygon points="230.5,180 319.5,180 367,257 319.5,334 230.5,334 183,257"/>
                                <polygon points="372.5,100 462.5,100 509.5,177 462.5,254 372.5,254 325.5,177"/>
                                <polygon points="87.5,260 177.5,260 224.5,337 177.5,414 87.5,414 40.5,337"/>
                                <polygon points="372.5,260 462.5,260 509.5,337 462.5,414 372.5,414 325.5,337"/>
                                <polygon points="230.5,340 319.5,340 367,417 319.5,494 230.5,494 183,417"/>
                            </clipPath>
                        </defs>
                        <image href="{{ asset('images/simantap-office-reference.jpg') }}" x="0" y="-12" width="630" height="570" preserveAspectRatio="xMinYMin slice" clip-path="url(#office-mosaic-clip)"/>
                        <g fill="none" stroke="#ffffff" stroke-width="2" stroke-linejoin="round">
                            <polygon points="87.5,100 177.5,100 224.5,177 177.5,254 87.5,254 40.5,177"/>
                            <polygon points="230.5,180 319.5,180 367,257 319.5,334 230.5,334 183,257"/>
                            <polygon points="372.5,100 462.5,100 509.5,177 462.5,254 372.5,254 325.5,177"/>
                            <polygon points="87.5,260 177.5,260 224.5,337 177.5,414 87.5,414 40.5,337"/>
                            <polygon points="372.5,260 462.5,260 509.5,337 462.5,414 372.5,414 325.5,337"/>
                            <polygon points="230.5,340 319.5,340 367,417 319.5,494 230.5,494 183,417"/>
                        </g>
                    </svg>
                    <img class="office-crest" src="{{ asset('images/kutai-barat-emblem.png') }}" alt="Lambang Kabupaten Kutai Barat">
                </div>
                <p class="landing-promise">Data terintegrasi <span>•</span> Geotagging <span>•</span> Monitoring real-time</p>
            </div>
        </section>
    </main>
</body>
</html>
