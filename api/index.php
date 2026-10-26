<?php

// Pastikan direktori storage di /tmp dibuat saat dijalankan di Vercel Serverless
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Fallback environment variables untuk Vercel Serverless
$defaults = [
    'APP_NAME' => 'LMS Sekolah Digital',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'true',
    'APP_KEY' => 'base64:biqCuYXu4iPsb4IKWrlvFxV5cy6odGJV2AwAyk9+rAU=',
    'APP_STORAGE' => '/tmp/storage',
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
    if (empty(getenv($key)) && empty($_ENV[$key]) && empty($_SERVER[$key])) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

require __DIR__ . '/../public/index.php';
