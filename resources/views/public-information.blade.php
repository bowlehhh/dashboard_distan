<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · SIMANTAP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-directory" data-entry-loader="true">
    <x-page-loader />

    <div class="public-directory-shell {{ $statistic === 0 ? 'public-directory-shell--empty' : '' }}">
    <header class="directory-header">
        <a class="directory-brand" href="{{ route('home') }}" aria-label="SIMANTAP, halaman utama">
            <img src="{{ asset('images/simantap-logo-reference.png') }}" alt="SIMANTAP — Sistem Informasi Alsintan Terpadu, Modern, dan Transparan">
        </a>

        <button class="directory-menu-toggle" type="button" data-directory-menu-toggle aria-expanded="false" aria-controls="directory-menu" aria-label="Buka menu informasi publik">☰</button>
        <nav class="directory-nav" id="directory-menu" aria-label="Navigasi informasi publik">
            <a href="{{ route('home') }}">Beranda</a>
            <a class="{{ request()->routeIs('public.poktans') ? 'is-active' : '' }}" href="{{ route('public.poktans') }}">Data Poktan</a>
            <a class="{{ request()->routeIs('public.alsintans') ? 'is-active' : '' }}" href="{{ route('public.alsintans') }}">Data Alsintan</a>
            <a class="{{ request()->routeIs('public.saprodis') ? 'is-active' : '' }}" href="{{ route('public.saprodis') }}">Saprodi</a>
            <a class="{{ request()->routeIs('public.reports') ? 'is-active' : '' }}" href="{{ route('public.reports') }}">Laporan</a>
            <a class="directory-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }}</a>
        </nav>
    </header>

    <main>
        <section class="directory-hero" aria-labelledby="directory-title">
            <div class="directory-hero-pattern" aria-hidden="true">
                <svg viewBox="0 0 1440 420" preserveAspectRatio="none" fill="none">
                    <path d="M-40 336c126-110 203-107 316-9 96 83 151 62 223-16 75-81 154-88 258-12 106 78 178 89 290-2 108-88 218-103 435 28" stroke="currentColor" stroke-width="2"/>
                    <path d="M-20 366c127-106 198-101 306-8 105 91 171 76 249-8 75-81 146-84 253-8 106 75 183 84 290-7 117-100 232-98 406 16" stroke="currentColor" stroke-width="2"/>
                    <path d="M102 112c-37 33-37 81 0 113 37-32 37-80 0-113Zm0 29v77" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M148 206c37-33 37-81 0-113-37 32-37 80 0 113Zm0-29v-77" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M1234 116c24-25 48-39 78-46m-78 46 23 3m-23-3 4-23M1235 116v101m-33-51v51m67-91v91m33-125v125" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="directory-eyebrow">SIMANTAP · INFORMASI PUBLIK</p>
                <h1 id="directory-title">{{ $title }}</h1>
                <p>{{ $description }}</p>
            </div>
            <aside class="directory-stat" aria-label="{{ $statisticLabel }}">
                <span class="directory-stat__icon" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none">
                        <circle cx="16" cy="17" r="5" stroke="currentColor" stroke-width="2.5"/>
                        <circle cx="32" cy="17" r="5" stroke="currentColor" stroke-width="2.5"/>
                        <path d="M7 35c0-6 4-10 9-10s9 4 9 10M23 35c0-6 4-10 9-10s9 4 9 10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <strong>{{ number_format($statistic, 0, ',', '.') }}</strong>
                <span>{{ $statisticLabel }}</span>
            </aside>
        </section>

        <section class="directory-content" aria-labelledby="directory-table-title">
            <div class="directory-content__heading">
                <div>
                    <p>DATA TERKINI</p>
                    <h2 id="directory-table-title">Ringkasan untuk masyarakat</h2>
                </div>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}">Akses sistem lengkap <span>→</span></a>
            </div>

            <div class="directory-data-grid">
                <div class="directory-table-column">
                    <div class="directory-table-stage">
                        <div class="directory-table-art directory-table-art--left" aria-hidden="true">
                            @for ($artIndex = 0; $artIndex < 2; $artIndex++)
                                <span class="directory-table-art__item directory-table-art__item--sprout">
                                    <svg viewBox="0 0 48 48" fill="none">
                                        <circle cx="12" cy="11" r="4" fill="#f4c642"/>
                                        <path d="M12 4v-2m0 16v-2m7-5h2M3 11h2m2-5 1.5 1.5m11 9 1.5 1.5M7 17l1.5-1.5M17 6l1.5-1.5" stroke="#e7b52f" stroke-width="1.4" stroke-linecap="round"/>
                                        <path d="M24 40V21m-8 19h17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        <path d="M24 29c-7 0-11-3-11-9 7 0 11 3 11 9Zm0-6c1-7 5-10 12-10 0 7-4 10-12 10Z" fill="#8bad42" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M18 40V29m12 11V24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            @endfor
                        </div>

                        <div class="directory-table-wrap">
                            <table class="directory-table">
                                <thead>
                                    <tr>
                                        @foreach ($columns as $index => $column)
                                            @php
                                                $iconKey = match (true) {
                                                    request()->routeIs('public.alsintans') => ['tractor', 'tag', 'pin', 'condition'][$index] ?? 'data',
                                                    request()->routeIs('public.poktans') => ['group', 'pin', 'leaf', 'members'][$index] ?? 'data',
                                                    request()->routeIs('public.saprodis') => ['bag', 'tag', 'stock', 'unit'][$index] ?? 'data',
                                                    request()->routeIs('public.crops') => ['leaf', 'pin', 'field', 'harvest'][$index] ?? 'data',
                                                    default => ['chart', 'data'][$index] ?? 'data',
                                                };
                                            @endphp
                                            <th scope="col">
                                                <span class="directory-column-icon" aria-hidden="true">
                                                    @switch($iconKey)
                                                        @case('tractor')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M5 15h13l2 4H3l2-4Zm3 0V8h5l2 7M5 19v2m14-2v2M3 21h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="7" cy="18" r="2" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="18" r="2" stroke="currentColor" stroke-width="1.8"/></svg>
                                                            @break
                                                        @case('tag')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="m4 5 8-2 9 9-8 8-9-9V5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="8" cy="8" r="1.5" fill="currentColor"/></svg>
                                                            @break
                                                        @case('pin')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 21s6-5.4 6-11a6 6 0 1 0-12 0c0 5.6 6 11 6 11Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2" stroke="currentColor" stroke-width="1.8"/></svg>
                                                            @break
                                                        @case('condition')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 17a8 8 0 0 1 16 0M12 17l4-5M4 20h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M6 16v2m12-2v2" stroke="currentColor" stroke-width="1.8"/></svg>
                                                            @break
                                                        @case('group')
                                                        @case('members')
                                                            <svg viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.8"/><circle cx="16" cy="8" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c0-3 2-5 4.5-5s4.5 2 4.5 5m3-5c2.5 0 4.5 2 4.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                            @break
                                                        @case('leaf')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 21V10m0 5c-4.5 0-7-2.5-7-7 4.5 0 7 2.5 7 7Zm0-3c.5-4.5 3-7 7-7 0 4.5-2.5 7-7 7Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            @break
                                                        @case('bag')
                                                        @case('stock')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M5 8h14l-1 13H6L5 8Zm3 0a4 4 0 0 1 8 0" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 12h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                            @break
                                                        @case('unit')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="m5 19 14-14M7 17l-2-2m6-2-2-2m6-2-2-2m6-2-2-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                            @break
                                                        @case('field')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M3 18c5-6 10-8 18-8M3 14c5-5 10-6 18-6M3 20V9m7 11v-8m7 8V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                            @break
                                                        @case('harvest')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 21V7m0 7c-4 0-6-2-6-6 4 0 6 2 6 6Zm0-4c1-4 3-6 7-6 0 4-2 6-7 6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 21h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                            @break
                                                        @case('chart')
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 19V5m0 14h17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m7 15 4-4 3 2 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            @break
                                                        @default
                                                            <svg viewBox="0 0 24 24" fill="none"><path d="M6 5h12M6 12h12M6 19h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                    @endswitch
                                                </span>
                                                {{ $column }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            @foreach ($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="directory-empty" colspan="{{ count($columns) }}">Data publik akan ditampilkan setelah tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="directory-table-art directory-table-art--right" aria-hidden="true">
                            @for ($artIndex = 0; $artIndex < 2; $artIndex++)
                                <span class="directory-table-art__item directory-table-art__item--sprout">
                                    <svg viewBox="0 0 48 48" fill="none">
                                        <circle cx="12" cy="11" r="4" fill="#f4c642"/>
                                        <path d="M12 4v-2m0 16v-2m7-5h2M3 11h2m2-5 1.5 1.5m11 9 1.5 1.5M7 17l1.5-1.5M17 6l1.5-1.5" stroke="#e7b52f" stroke-width="1.4" stroke-linecap="round"/>
                                        <path d="M24 40V21m-8 19h17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        <path d="M24 29c-7 0-11-3-11-9 7 0 11 3 11 9Zm0-6c1-7 5-10 12-10 0 7-4 10-12 10Z" fill="#8bad42" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M18 40V29m12 11V24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            @endfor
                        </div>
                    </div>

                    @if ($rows instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $rows->hasPages())
                        <nav class="directory-pagination" aria-label="Paginasi tabel">
                            @foreach ($rows->getUrlRange(1, $rows->lastPage()) as $page => $url)
                                @if ($page === $rows->currentPage())
                                    <span aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        </nav>
                    @endif
                </div>

                <aside class="directory-chart-card" aria-label="Ringkasan visual data">
                    @php
                        $chartTotal = max(1, array_sum(array_column($chart, 'value')));
                        $chartOffset = 0;
                        $chartStops = [];
                        foreach ($chart as $chartItem) {
                            $chartPercentage = $chartItem['value'] / $chartTotal * 100;
                            $chartStops[] = $chartItem['color'].' '.$chartOffset.'% '.($chartOffset + $chartPercentage).'%';
                            $chartOffset += $chartPercentage;
                        }
                    @endphp
                    <p>DISTRIBUSI DATA</p>
                    <div class="directory-donut" role="img" aria-label="Distribusi {{ $statisticLabel }}" style="background: conic-gradient({{ implode(', ', $chartStops) }})">
                        <span><strong>{{ number_format($statistic, 0, ',', '.') }}</strong><small>data</small></span>
                    </div>
                    <ul class="directory-chart-legend">
                        @foreach ($chart as $chartItem)
                            <li>
                                <i style="background: {{ $chartItem['color'] }}"></i>
                                <span>{{ $chartItem['label'] }}</span>
                                <strong>{{ number_format($chartItem['value'], 0, ',', '.') }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            </div>

            @if (count($recommendations) > 0)
                <section class="directory-recommendations" aria-labelledby="recommendations-title">
                <div class="directory-recommendations__heading">
                    <div>
                        <p>UNTUK PIMPINAN</p>
                        <h2 id="recommendations-title">Rekomendasi singkat</h2>
                    </div>
                    <details class="directory-recommendations__guide">
                        <summary>Cara membaca</summary>
                        <div class="directory-recommendations__guide-content">
                            <p>Gunakan tiga langkah singkat ini untuk membaca ringkasan:</p>
                            <ol>
                                <li><strong>Prioritas tinggi</strong> ditangani lebih dulu.</li>
                                <li>Lihat angka untuk mengetahui jumlah data yang perlu diperhatikan.</li>
                                <li>Gunakan keterangan kartu sebagai arahan tindak lanjut.</li>
                            </ol>
                        </div>
                    </details>
                </div>

                <div class="directory-recommendations__grid">
                    @foreach ($recommendations as $recommendation)
                        <article class="directory-recommendation directory-recommendation--{{ $recommendation['tone'] }}">
                            <span class="directory-recommendation__icon" aria-hidden="true">
                                @if ($recommendation['tone'] === 'danger')
                                    !
                                @elseif ($recommendation['tone'] === 'warning')
                                    ↗
                                @else
                                    ✓
                                @endif
                            </span>
                            <div>
                                <span class="directory-recommendation__label">{{ $recommendation['label'] }} · {{ number_format($recommendation['percentage'], 1, ',', '.') }}%</span>
                                <h3>{{ $recommendation['title'] }}</h3>
                                <p>{{ $recommendation['description'] }}</p>
                            </div>
                            <strong>{{ number_format($recommendation['value'], 0, ',', '.') }}</strong>
                        </article>
                    @endforeach
                </div>
                </section>
            @else
                <section class="directory-recommendations directory-recommendations--empty" aria-label="Rekomendasi pimpinan">
                    <p>Rekomendasi pimpinan akan muncul setelah data tersedia.</p>
                </section>
            @endif
        </section>
    </main>

    <footer class="directory-footer">
        <span>Dinas Pertanian Kabupaten Kutai Barat</span>
        <span>Data terintegrasi · Geotagging · Monitoring real-time</span>
    </footer>
    </div>
</body>
</html>
