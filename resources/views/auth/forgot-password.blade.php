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
                <x-simantap-mark class="auth-wordmark__icon" />
                <span>SIMANTAP<small>Sistem Informasi Alsintan, Saprodi, dan Tanaman Pangan</small></span>
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
