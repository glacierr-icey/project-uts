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
| Diagnostic ringan (sebelum Laravel dimuat)
|--------------------------------------------------------------------------
| Buka /?__vercel_diag=1 untuk memastikan autoload, seed DB, dan /tmp
| tersedia di Vercel. Tidak menampilkan APP_KEY.
*/
if (isset($_GET['__vercel_diag'])) {
    $seedFile = __DIR__.'/../database/vercel-seed.sqlite';
    $autoload = __DIR__.'/../vendor/autoload.php';

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'php'           => PHP_VERSION,
        'autoload'      => is_file($autoload),
        'seed_db'       => is_file($seedFile),
        'seed_db_size'  => is_file($seedFile) ? filesize($seedFile) : 0,
        'tmp_writable'  => is_writable('/tmp'),
        'app_key_set'   => ! empty($_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? getenv('APP_KEY')),
        'db_connection' => (string) ($_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?? ''),
        'db_database'   => (string) ($_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? ''),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    exit;
}

try {
    /*
    |----------------------------------------------------------------------
    | 1. Direktori writable di /tmp
    |----------------------------------------------------------------------
    */
    foreach (['/tmp/views', '/tmp/cache', '/tmp/cache/data', '/tmp/sessions', '/tmp/logs'] as $directory) {
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }
    }

    /*
    |----------------------------------------------------------------------
    | 2. Environment
    |----------------------------------------------------------------------
    | Nilai "forced" menimpa nilai lama yang tidak kompatibel dengan Vercel
    | (mis. LOG_CHANNEL=single yang menulis ke storage read-only).
    | Nilai "default" hanya dipakai bila variabel benar-benar kosong.
    */
    $envValue = static function (string $key): mixed {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    };

    $setEnv = static function (string $key, string $value): void {
        $_ENV[$key] = $_SERVER[$key] = $value;
        putenv($key.'='.$value);
    };

    $defaults = [
        'APP_ENV'              => 'production',
        'APP_DEBUG'            => 'false',
        'DB_CONNECTION'        => 'sqlite',
        'DB_DATABASE'          => '/tmp/database.sqlite',
        'FILESYSTEM_DISK'      => 'local',
    ];

    $forced = [
        'LOG_CHANNEL'          => 'stderr',
        'LOG_LEVEL'            => 'error',
        'SESSION_DRIVER'       => 'cookie',
        'SESSION_ENCRYPT'      => 'false',
        'SESSION_PATH'         => '/',
        'CACHE_STORE'          => 'array',
        'QUEUE_CONNECTION'     => 'sync',
        'BROADCAST_CONNECTION' => 'log',
        'APP_SERVICES_CACHE'   => '/tmp/services.php',
        'APP_PACKAGES_CACHE'   => '/tmp/packages.php',
        'APP_CONFIG_CACHE'     => '/tmp/config.php',
        'APP_ROUTES_CACHE'     => '/tmp/routes.php',
        'VIEW_COMPILED_PATH'   => '/tmp/views',
    ];

    if (! array_key_exists('APP_URL', $_ENV)
        && ! array_key_exists('APP_URL', $_SERVER)
        && getenv('APP_URL') === false) {
        $vercelUrl = (string) (getenv('VERCEL_URL') ?: '');
        $defaults['APP_URL'] = $vercelUrl !== '' ? 'https://'.$vercelUrl : 'http://localhost';
    }

    foreach ($defaults as $key => $value) {
        $current = $envValue($key);

        if ($current === false || $current === null || $current === '') {
            $setEnv($key, $value);
        }
    }

    foreach ($forced as $key => $value) {
        $setEnv($key, $value);
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
    | Cache stale dari request/instance sebelumnya bisa berisi konfigurasi
    | lama (mis. DB mysql). Bersihkan sekali per instance sebelum bootstrap.
    */
    $cleanupMarker = '/tmp/.vercel_cache_cleaned';

    if (! file_exists($cleanupMarker)) {
        foreach (['/tmp/services.php', '/tmp/packages.php', '/tmp/config.php', '/tmp/routes.php'] as $cacheFile) {
            @unlink($cacheFile);
        }

        @file_put_contents($cleanupMarker, date(DATE_ATOM), LOCK_EX);
    }

    /*
    |----------------------------------------------------------------------
    | 3. Bootstrap Laravel
    |----------------------------------------------------------------------
    */
    require __DIR__.'/../vendor/autoload.php';

    /** @var Application $app */
    $app = require __DIR__.'/../bootstrap/app.php';

    /*
    |----------------------------------------------------------------------
    | 4. Siapkan SQLite di /tmp
    |----------------------------------------------------------------------
    | File SQLite sudah di-migrate + di-seed saat build dan dikirim sebagai
    | database/vercel-seed.sqlite. Cukup disalin ke /tmp saat cold start.
    | Migrasi di dalam request dihapus karena sering menyebabkan 500/timeout.
    */
    $dbConnection = (string) $envValue('DB_CONNECTION');
    $dbDatabase = $envValue('DB_DATABASE');

    if ($dbConnection === 'sqlite' && is_string($dbDatabase) && $dbDatabase !== '' && str_starts_with($dbDatabase, '/tmp')) {
        if (! is_dir(dirname($dbDatabase))) {
            @mkdir(dirname($dbDatabase), 0777, true);
        }

        $databaseIsReady = false;

        if (file_exists($dbDatabase)) {
            try {
                $pdo = new PDO('sqlite:'.$dbDatabase);
                $databaseIsReady = (bool) $pdo
                    ->query("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'products' LIMIT 1")
                    ->fetchColumn();
            } catch (Throwable $exception) {
                $databaseIsReady = false;
            }
        }

        if (! $databaseIsReady) {
            $seedFile = __DIR__.'/../database/vercel-seed.sqlite';

            if (is_file($seedFile) && copy($seedFile, $dbDatabase)) {
                $databaseIsReady = true;
            } else {
                // Fallback terakhir bila file seed tidak ikut ter-deploy.
                touch($dbDatabase);

                /** @var ConsoleKernel $consoleKernel */
                $consoleKernel = $app->make(ConsoleKernel::class);
                $consoleKernel->bootstrap();
                $consoleKernel->call('migrate', ['--force' => true]);
                $consoleKernel->call('db:seed', ['--force' => true]);

                $databaseIsReady = true;
            }
        }
    }

    /*
    |----------------------------------------------------------------------
    | 5. Proses request
    |----------------------------------------------------------------------
    */
    $app->handleRequest(Request::capture());
} catch (Throwable $exception) {
    /*
    | Jangan biarkan halaman menjadi blank "This page isn't working".
    | Tampilkan penyebabnya, dan tetap tulis ke Vercel Function Logs.
    */
    if (! headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }

    error_log(
        '[vercel] FATAL: '.get_class($exception).': '.$exception->getMessage()
        .' at '.$exception->getFile().':'.$exception->getLine()
    );

    $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">'
        .'<title>500 - Vercel bootstrap error</title>'
        .'<style>body{font-family:system-ui,sans-serif;background:#f8fafc;color:#0f172a;padding:32px}'
        .'pre{white-space:pre-wrap;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:16px}</style>'
        .'</head><body><h1>500 - Gagal membootstrap aplikasi di Vercel</h1>'
        .'<pre>'.$e(get_class($exception)).': '.$e($exception->getMessage())."\n"
        .'Lokasi: '.$e($exception->getFile()).':'.$e((string) $exception->getLine())."\n\n"
        .'Trace singkat: '.$e((string) $exception->getTraceAsString()).'</pre>'
        .'<p>Buka <code>/?__vercel_diag=1</code> untuk mengecek autoload, seed database, dan izin <code>/tmp</code>. '
        .'Detail lengkap ada di Vercel &rarr; Deployments &rarr; Functions &rarr; Logs.</p>'
        .'</body></html>';
}
