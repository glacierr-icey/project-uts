<?php

// Vercel serverless entry point for Laravel

// Pastikan folder-folder ephemeral (/tmp) yang dibutuhkan Laravel tersedia dan bisa ditulis
$directories = [
    '/tmp/views',
    '/tmp/bootstrap/cache',
    '/tmp/framework/cache',
    '/tmp/framework/sessions',
    '/tmp/framework/views',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Redirect cache dan view path Laravel ke /tmp agar bisa diakses di Vercel
$_ENV['APP_SERVICES_CACHE'] = $_ENV['APP_SERVICES_CACHE'] ?? '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $_ENV['APP_PACKAGES_CACHE'] ?? '/tmp/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $_ENV['APP_CONFIG_CACHE'] ?? '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = $_ENV['APP_ROUTES_CACHE'] ?? '/tmp/routes.php';
$_ENV['VIEW_COMPILED_PATH'] = $_ENV['VIEW_COMPILED_PATH'] ?? '/tmp/views';

// Inisialisasi Database SQLite di /tmp jika pakai SQLite
$dbConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? null;
$dbDatabase = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? null;
if ($dbConnection === 'sqlite' && is_string($dbDatabase) && str_starts_with($dbDatabase, '/tmp') && !file_exists($dbDatabase)) {
    touch($dbDatabase);
}

// Teruskan request ke public/index.php bawaan Laravel
require __DIR__ . '/../public/index.php';