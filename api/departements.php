<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();
$handler = new HybridHandler('departements.xml', 'departements', 'departement', 'departements');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') apiSuccess(['items' => $handler->all()]);
    if ($method === 'GET' && $action === 'get') {
        $item = $handler->find($_GET['id'] ?? null);
        if (!$item) apiError('Introuvable', 404);
        apiSuccess(['item' => $item]);
    }
    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['nom'])) apiError('Nom requis');
        apiSuccess(['id' => $handler->add($data), 'message' => 'Département ajouté']);
    }
    if ($method === 'POST' && $action === 'update') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->update($data['id'], $data)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Département mis à jour']);
    }
    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->delete($data['id'])) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Département supprimé']);
    }
    apiError('Action inconnue', 404);
} catch (Exception $e) { apiError($e->getMessage(), 500); }
