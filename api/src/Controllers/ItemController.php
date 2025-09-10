<?php
namespace App\Controllers;

use App\Database;
use App\Utils\Response;
use MongoDB\BSON\ObjectId;

class ItemController
{
    private $col;

    public function __construct()
    {
        $this->col = Database::collection('items');
    }

    public function index()
    {
        $q = $_GET['q'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 10)));
        $filter = [];
        if ($q) {
            // Para búsqueda simple usamos text index if exists; si no, fallback a regex en 'name'
            $filter = ['$or' => [['name' => new \MongoDB\BSON\Regex($q, 'i')], ['description' => new \MongoDB\BSON\Regex($q, 'i')]]];
        }
        $cursor = $this->col->find($filter, [
            'skip' => ($page - 1) * $perPage,
            'limit' => $perPage,
            'sort' => ['created_at' => -1]
        ]);

        $items = array_map(function($i){
            $i['_id'] = (string)$i['_id'];
            // Convertir BSON datetimes
            if (isset($i['created_at']) && $i['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
                $i['created_at'] = $i['created_at']->toDateTime()->format(DATE_ATOM);
            }
            return $i;
        }, iterator_to_array($cursor));

        Response::json(['data' => $items, 'page' => $page, 'per_page' => $perPage]);
    }

    public function show($id)
    {
        try {
            $oid = new ObjectId($id);
        } catch (\Exception $e) {
            Response::error('ID inválido', 400);
        }
        $item = $this->col->findOne(['_id' => $oid]);
        if (!$item) Response::error('No encontrado', 404);
        $item['_id'] = (string)$item['_id'];
        if (isset($item['created_at']) && $item['created_at'] instanceof \MongoDB\BSON\UTCDateTime) {
            $item['created_at'] = $item['created_at']->toDateTime()->format(DATE_ATOM);
        }
        Response::json($item);
    }

    public function store()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) Response::error('JSON inválido', 400);

        $name = trim($payload['name'] ?? '');
        $desc = trim($payload['description'] ?? '');
        if ($name === '') Response::error('El nombre es obligatorio', 422);

        $doc = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $res = $this->col->insertOne($doc);
        $doc['_id'] = (string)$res->getInsertedId();
        Response::json($doc, 201);
    }

    public function update($id)
    {
        try { $oid = new ObjectId($id); } catch (\Exception $e) { Response::error('ID inválido', 400); }
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) Response::error('JSON inválido', 400);

        $update = [];
        if (isset($payload['name'])) $update['name'] = htmlspecialchars(trim($payload['name']), ENT_QUOTES, 'UTF-8');
        if (isset($payload['description'])) $update['description'] = htmlspecialchars(trim($payload['description']), ENT_QUOTES, 'UTF-8');

        if (empty($update)) Response::error('Nada para actualizar', 422);

        $res = $this->col->updateOne(['_id' => $oid], ['$set' => $update]);
        if ($res->getMatchedCount() === 0) Response::error('No encontrado', 404);
        Response::json(['updated' => $res->getModifiedCount()]);
    }

    public function destroy($id)
    {
        try { $oid = new ObjectId($id); } catch (\Exception $e) { Response::error('ID inválido', 400); }
        $res = $this->col->deleteOne(['_id' => $oid]);
        if ($res->getDeletedCount() === 0) Response::error('No encontrado', 404);
        Response::json(['deleted' => true]);
    }
}
