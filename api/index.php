<?php
// api/index.php - simple front controller for API routes
require __DIR__ . '/bootstrap.php';

use App\Controllers\ItemController;
use App\Utils\Response;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$base = '/api/items';

$controller = new ItemController();

if ($uri === $base && $method === 'GET') {
    $controller->index();
}
elseif (preg_match('#^/api/items/([0-9a-fA-F]{24})$#', $uri, $m)) {
    $id = $m[1];
    if ($method === 'GET') $controller->show($id);
    elseif ($method === 'PUT') $controller->update($id);
    elseif ($method === 'DELETE') $controller->destroy($id);
    else Response::error('Método no permitido', 405);
}
elseif ($uri === $base && $method === 'POST') {
    $controller->store();
}
else {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Ruta no encontrada']);
}
