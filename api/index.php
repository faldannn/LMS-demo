<?php

// Pastikan direktori storage di /tmp dibuat saat dijalankan di Vercel Serverless
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/storage/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy pre-compiled package manifests to writable /tmp
foreach (['packages.php', 'services.php'] as $file) {
    $src = __DIR__ . '/../bootstrap/cache/' . $file;
    $dst = '/tmp/storage/bootstrap/cache/' . $file;
    if (!file_exists($dst) && file_exists($src)) {
        copy($src, $dst);
    }
}

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Fallback environment variables untuk Vercel Serverless
$defaults = [
    'APP_NAME' => 'LMS Sekolah Digital',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'true',
    'APP_KEY' => 'base64:biqCuYXu4iPsb4IKWrlvFxV5cy6odGJV2AwAyk9+rAU=',
    'APP_PACKAGES_CACHE' => '/tmp/storage/bootstrap/cache/packages.php',
    'APP_SERVICES_CACHE' => '/tmp/storage/bootstrap/cache/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com',
    'DB_PORT' => '4000',
    'DB_DATABASE' => 'lms_demo',
    'DB_USERNAME' => 'xeGakznEYY7uFXn.root',
    'DB_PASSWORD' => 'wE8wp0ZaE312350p',
    'MYSQL_ATTR_SSL_CA' => 'true',
    'MYSQL_ATTR_SSL_VERIFY_SERVER_CERT' => 'false',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'LOG_CHANNEL' => 'stderr',
];

foreach ($defaults as $key => $value) {
    $existing = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($existing === false || $existing === null || $existing === '') {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

require __DIR__ . '/../public/index.php';
