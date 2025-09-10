<?php
// api/bootstrap.php
require __DIR__ . '/vendor/autoload.php';

// Cargar .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) == 2) putenv(trim($parts[0]) . '=' . trim($parts[1]));
    }
}

// Config DB
$mongoHost = getenv('MONGO_HOST') ?: '127.0.0.1';
$mongoPort = getenv('MONGO_PORT') ?: '27017';
$mongoDb   = getenv('MONGO_DB') ?: 'neurobyteai';
$mongoUser = getenv('MONGO_USER');
$mongoPass = getenv('MONGO_PASS');

$uri = 'mongodb://' . ($mongoUser && $mongoPass ? $mongoUser . ':' . $mongoPass . '@' : '') . $mongoHost . ':' . $mongoPort;

use App\Database;
Database::init($uri, $mongoDb);
