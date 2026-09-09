# SIMANTAP

Sistem informasi Alsintan, Saprodi, dan Tanaman Pangan untuk Dinas Pertanian Kabupaten Kutai Barat.

## Menjalankan lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
```

Gunakan nilai `SIMANTAP_ADMIN_EMAIL` dan `SIMANTAP_ADMIN_PASSWORD` pada `.env` untuk akun administrator hasil seeder.

## Deployment produksi

1. Siapkan PHP 8.3+, ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `xml`, `curl`, dan `zip`, lalu buat database produksi.
2. Isi `.env` produksi dengan `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` yang benar, kredensial database, `FILESYSTEM_DISK=public`, SMTP yang aktif, serta password admin yang kuat.
3. Setelah kode terbaru sudah berada di server, jalankan `bash deploy.sh` dari root aplikasi. Skrip ini memasang dependency produksi, menghapus cache lama, menjalankan migrasi, membangun aset Vite baru, lalu mengaktifkan kembali aplikasi.

   Skrip tidak menjalankan seeder supaya data produksi dan akun admin tidak tertimpa. Jangan menjalankan `php artisan db:seed --force` pada deploy rutin.

   Jika tidak menggunakan skrip, jalankan urutan berikut dari root aplikasi:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link --force
npm ci
npm run build
php artisan config:cache
php artisan view:cache
```

`public/build` tidak disimpan di repository. Karena itu `npm ci` dan `npm run build` wajib dijalankan pada setiap deploy agar server tidak memuat CSS/JavaScript versi lama. Aplikasi ini memakai route closure, jadi jangan menjalankan `php artisan route:cache`.

Document root web server harus diarahkan ke direktori `public`, bukan root repository. Pastikan `storage` dan `bootstrap/cache` dapat ditulis oleh user web server. Jika memakai queue database, jalankan worker yang dikelola Supervisor/systemd. Jangan gunakan `MAIL_MAILER=log` di produksi jika fitur reset password harus mengirim email.

## Pemeriksaan sebelum rilis

```bash
composer audit
php artisan route:cache
php artisan view:cache
php artisan test
```

Test database membutuhkan ekstensi PHP `pdo_sqlite` pada environment CI/lokal, atau konfigurasi database test khusus yang tersedia di runner.

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
