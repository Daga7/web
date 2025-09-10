<?php
namespace App;

use MongoDB\Client;

class Database
{
    private static $client;
    private static $dbName;

    public static function init(string $uri, string $dbName)
    {
        // el constructor de Client puede lanzar excepción si la extensión no está disponible.
        try {
            self::$client = new Client($uri);
        } catch (\Throwable $e) {
            // si falla, dejamos un mensaje claro para el desarrollador.
            error_log('MongoDB Client init error: ' . $e->getMessage());
            throw $e;
        }
        self::$dbName = $dbName;
    }

    public static function collection(string $name)
    {
        return self::$client->selectCollection(self::$dbName, $name);
    }
}
