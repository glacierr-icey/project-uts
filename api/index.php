<?php

$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

$dbConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? null;
$dbDatabase = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? null;
if ($dbConnection === 'sqlite' && is_string($dbDatabase) && str_starts_with($dbDatabase, '/tmp') && !file_exists($dbDatabase)) {
    if (!is_dir(dirname($dbDatabase))) {
        mkdir(dirname($dbDatabase), 0755, true);
    }
    touch($dbDatabase);
}

require __DIR__ . '/../public/index.php';