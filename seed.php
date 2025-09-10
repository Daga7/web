<?php
// seed.php - pobla la colección 'items' con datos de ejemplo.
// Requiere que la configuración en api/bootstrap.php lea .env o uses values por defecto.
require __DIR__ . '/api/vendor/autoload.php';
require __DIR__ . '/api/bootstrap.php';

use App\Database;

$data = json_decode(file_get_contents(__DIR__ . '/seed_data.json'), true);
$col = Database::collection('items');

foreach ($data as $d) {
    $d['created_at'] = new MongoDB\BSON\UTCDateTime();
    $col->insertOne($d);
}

echo "Seed completado\n";
