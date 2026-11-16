<?php

// Vercel serverless entry point for Laravel

// Ensure writable directories exist in Vercel's ephemeral filesystem
foreach ([
    '/tmp/views',
    '/tmp/bootstrap/cache',
    '/tmp/framework/cache',
    '/tmp/framework/sessions',
] as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// If using SQLite in /tmp, create an empty database file on first request (ephemeral)
$db = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: null;
$connection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: null;
if ($connection === 'sqlite' && is_string($db) && str_starts_with($db, '/tmp') && ! file_exists($db)) {
    @touch($db);
}

require __DIR__ . '/../public/index.php';
