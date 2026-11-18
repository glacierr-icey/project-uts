<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Beberapa runtime serverless tidak mengisi REMOTE_ADDR. TrustProxies
// membutuhkan nilai string, jadi beri default agar tidak TypeError.
$_SERVER['REMOTE_ADDR'] ??= '127.0.0.1';

/*
|--------------------------------------------------------------------------
| 1. Siapkan direktori writable di /tmp
|--------------------------------------------------------------------------
| Vercel bersifat read-only kecuali /tmp. Blade, cache, dan session harus
| diarahkan ke sana. Versi sebelumnya hanya men-set env var tanpa membuat
| direktori /tmp/views, sehingga Blade gagal menulis compiled view -> 500.
*/
foreach (['/tmp/views', '/tmp/cache', '/tmp/cache/data', '/tmp/sessions', '/tmp/logs'] as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0777, true);
    }
}

/*
|--------------------------------------------------------------------------
| 2. Default environment untuk serverless
|--------------------------------------------------------------------------
| Nilai di bawah HANYA dipakai bila variabel belum diset di Vercel Dashboard.
| Ini mencegah 500 karena .env tidak ikut ter-deploy (lihat .vercelignore).
*/
$vercelDefaults = [
    'APP_ENV'              => 'production',
    'APP_DEBUG'            => 'false',
    'LOG_CHANNEL'          => 'stderr',
    'LOG_LEVEL'            => 'error',
    'DB_CONNECTION'        => 'sqlite',
    'DB_DATABASE'          => '/tmp/database.sqlite',
    'SESSION_DRIVER'       => 'cookie',
    'SESSION_ENCRYPT'      => 'false',
    'SESSION_PATH'         => '/',
    'CACHE_STORE'          => 'array',
    'QUEUE_CONNECTION'     => 'sync',
    'BROADCAST_CONNECTION' => 'log',
    'FILESYSTEM_DISK'      => 'local',
    'APP_SERVICES_CACHE'   => '/tmp/services.php',
    'APP_PACKAGES_CACHE'   => '/tmp/packages.php',
    'APP_CONFIG_CACHE'     => '/tmp/config.php',
    'APP_ROUTES_CACHE'     => '/tmp/routes.php',
    'VIEW_COMPILED_PATH'   => '/tmp/views',
];

if (! array_key_exists('APP_URL', $_ENV)
    && ! array_key_exists('APP_URL', $_SERVER)
    && getenv('APP_URL') === false) {
    $vercelDefaults['APP_URL'] = isset($_SERVER['VERCEL_URL'])
        ? 'https://'.$_SERVER['VERCEL_URL']
        : 'http://localhost';
}

$envValue = static function (string $key): mixed {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
};

$setEnv = static function (string $key, string $value): void {
    $_ENV[$key] = $_SERVER[$key] = $value;
    putenv($key.'='.$value);
};

foreach ($vercelDefaults as $key => $value) {
    $current = $envValue($key);

    if ($current === false || $current === null || $current === '') {
        $setEnv($key, $value);
    }
}

/*
| APP_KEY wajib ada untuk cookie/session/enkripsi. Bila belum diset di
| Vercel, kunci dibuat otomatis dan disimpan di /tmp agar stabil selama
| instance hidup. Untuk produksi, tetap set APP_KEY di Vercel Dashboard.
*/
$appKey = $envValue('APP_KEY');

if (! is_string($appKey) || trim($appKey) === '') {
    $keyFile = '/tmp/.app_key';
    $cachedKey = is_file($keyFile) ? trim((string) file_get_contents($keyFile)) : '';

    if ($cachedKey !== '') {
        $appKey = $cachedKey;
    } else {
        $appKey = 'base64:'.base64_encode(random_bytes(32));
        @file_put_contents($keyFile, $appKey, LOCK_EX);
    }

    $setEnv('APP_KEY', $appKey);
}

/*
|--------------------------------------------------------------------------
| 3. Bootstrap Laravel
|--------------------------------------------------------------------------
| Entry point ini sengaja tidak require public/index.php agar kita bisa
| menjalankan migrasi/seeder terlebih dahulu sebelum request diproses.
*/
require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| 4. Inisialisasi SQLite di /tmp saat cold start
|--------------------------------------------------------------------------
| File /tmp/database.sqlite selalu kosong pada instance baru. Tanpa migrasi,
| halaman awal yang mem-query tabel products/categories menghasilkan error
| "no such table" -> 500. Marker + flock memastikan migrate hanya jalan sekali.
*/
$dbConnection = (string) $envValue('DB_CONNECTION');
$dbDatabase = $envValue('DB_DATABASE');

if ($dbConnection === 'sqlite' && is_string($dbDatabase) && str_starts_with($dbDatabase, '/tmp')) {
    if (! is_dir(dirname($dbDatabase))) {
        @mkdir(dirname($dbDatabase), 0777, true);
    }

    if (! file_exists($dbDatabase)) {
        touch($dbDatabase);
    }

    $marker = '/tmp/.db_initialized_'.md5($dbDatabase);
    $lockHandle = @fopen('/tmp/.db_initialize.lock', 'c');

    if ($lockHandle !== false) {
        flock($lockHandle, LOCK_EX);

        try {
            if (! file_exists($marker)) {
                /** @var ConsoleKernel $consoleKernel */
                $consoleKernel = $app->make(ConsoleKernel::class);
                $consoleKernel->bootstrap();
                $consoleKernel->call('migrate', ['--force' => true]);
                $consoleKernel->call('db:seed', ['--force' => true]);

                file_put_contents($marker, date(DATE_ATOM), LOCK_EX);
            }
        } catch (Throwable $exception) {
            @unlink($marker);
            error_log('[vercel] SQLite initialization failed: '.$exception->getMessage());
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }
}

/*
|--------------------------------------------------------------------------
| 5. Proses request
|--------------------------------------------------------------------------
*/
$app->handleRequest(Request::capture());
