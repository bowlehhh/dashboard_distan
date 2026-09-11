<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password · SIMANTAP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-entry-loader="true">
<x-page-loader />
<main class="login-page">
    <section class="login-side">
        <div class="auth-pattern" aria-hidden="true"></div>
        <div class="auth-visual__content">
            <a class="auth-wordmark auth-wordmark--light" href="{{ route('home') }}">
                <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M24 43c-11 0-19-7-19-18 0-8 7-15 19-20 12 5 19 12 19 20 0 11-8 18-19 18Z" stroke="currentColor" stroke-width="3"/>
                    <path d="M24 40V18m0 10c-5 0-9-3-11-8m11 4c5 0 9-3 11-8" stroke="currentColor" stroke-linecap="round" stroke-width="3"/>
                </svg>
                <span>SIMANTAP<small>Sistem Informasi Alsintan<br>Terpadu, Modern, dan Transparan</small></span>
            </a>
            <p class="auth-kicker">SISTEM INFORMASI PERTANIAN</p>
            <h1>Pulihkan akses akun Anda.</h1>
            <p class="auth-description">Masukkan email yang terdaftar. Kami akan mengirimkan tautan untuk mereset kata sandi Anda.</p>
        </div>
    </section>
    <section class="login-form-wrap">
        <div class="auth-panel__inner">
            <div class="auth-panel__heading">
                <div>
                    <p>TINDAK LANJUT</p>
                    <h2>Lupa password?</h2>
                </div>
            </div>
            <p class="auth-panel__intro">Masukkan email akun Anda. Kami akan mengirim tautan pengaturan ulang.</p>
            <form class="auth-form" method="post" action="{{ route('password.email') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@distan.go.id" required autofocus>
                </div>
                @if(session('success'))
                    <div class="notice">✓ {{ session('success') }}</div>
                @endif
                @if(isset($errors) && $errors->any())
                    <div class="notice error">! {{ $errors->first() }}</div>
                @endif
                <button class="auth-submit" type="submit">Kirim Tautan <span>→</span></button>
                <p style="text-align:center;margin-top:24px;color:#8a9a8d;font-size:12px">
                    <a href="{{ route('login') }}" style="color:#137744;font-weight:700">← Kembali ke masuk</a>
                </p>
            </form>
        </div>
    </section>
</main>
</body>
</html>
