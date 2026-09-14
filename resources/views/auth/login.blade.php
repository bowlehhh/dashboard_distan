<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · SIMANTAP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page" data-entry-loader="true">
    <x-page-loader />
    <main class="auth-shell">
        <section class="auth-visual">
            <div class="auth-pattern" aria-hidden="true">
                <x-simantap-mark class="auth-pattern__mark auth-pattern__mark--one" />
                <x-simantap-mark class="auth-pattern__mark auth-pattern__mark--two" />
            </div>
            <div class="auth-visual__content">
                <div class="auth-wordmark auth-wordmark--light" aria-label="SIMANTAP">
                    <x-simantap-mark class="auth-wordmark__icon" />
                    <span>SIMANTAP<small>Sistem Informasi Alsintan, Saprodi, dan Tanaman Pangan</small></span>
                </div>
                <p class="auth-kicker">SISTEM INFORMASI PERTANIAN</p>
                <h1>Data pertanian yang lebih terarah.</h1>
                <p class="auth-description">Kelola Poktan, Alsintan, Saprodi, dan Tanaman Pangan Kabupaten Kutai Barat dalam satu sistem terpadu.</p>
                <div class="auth-highlights">
                    <span><b>01</b>Data terintegrasi</span>
                    <span><b>03</b>Monitoring pimpinan</span>
                </div>
                <a class="auth-home-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> Kembali ke Beranda</a>
            </div>
            <img class="auth-emblem" src="{{ asset('images/kutai-barat-emblem.png') }}" alt="Lambang Kabupaten Kutai Barat">
        </section>
        <section class="auth-panel">
            <div class="auth-panel__inner">
                <div class="auth-panel__identity">
                    <div class="auth-wordmark auth-wordmark--green" aria-label="SIMANTAP">
                        <x-simantap-mark class="auth-wordmark__icon" />
                        <span>SIMANTAP<small>Sistem Informasi Alsintan, Saprodi, dan Tanaman Pangan</small></span>
                    </div>
                    <img src="{{ asset('images/kutai-barat-emblem.png') }}" alt="Lambang Kabupaten Kutai Barat">
                </div>
                <div class="auth-panel__heading"><div><p>SELAMAT DATANG</p><h2>Masuk ke SIMANTAP</h2></div></div>
                <p class="auth-panel__intro">Gunakan akun resmi Anda untuk melanjutkan ke dashboard.</p>
                @if(session('success'))
                    <div class="notice">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="notice error">{{ $errors->first() }}</div>
                @endif
                <form class="auth-form" method="post" action="{{ route('login.store') }}" data-login-form>
                    @csrf
                    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@distan.go.id" autocomplete="email" required autofocus></div>
                    <div class="field"><label for="password">Kata sandi</label><input id="password" type="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required></div>
                    <div class="auth-form__options"><label><input type="checkbox" name="remember"> Ingat saya</label><a href="{{ route('password.request') }}">Lupa kata sandi?</a></div>
                    <button class="auth-submit" type="submit">Masuk ke Dashboard <span>→</span></button>
                </form>
                <a class="auth-panel-home-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> Kembali ke Beranda</a>
                <p class="auth-panel__footer">Akses aman untuk Admin, Operator, dan Penyuluh Pertanian</p>
            </div>
        </section>
    </main>
</body>
</html>
