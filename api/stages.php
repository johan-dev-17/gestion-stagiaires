<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();
$handler = new HybridHandler('stages.xml', 'stages', 'stage', 'stages');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') {
        $statut = $_GET['statut'] ?? '';
        $items = $handler->all();
        if ($statut !== '') $items = array_values(array_filter($items, fn($s) => ($s['statut']??'') === $statut));
        apiSuccess(['items' => $items]);
    }
    if ($method === 'GET' && $action === 'get') {
        $item = $handler->find($_GET['id'] ?? null);
        if (!$item) apiError('Introuvable', 404);
        apiSuccess(['item' => $item]);
    }
    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['sujet'])) apiError('Sujet requis');
        if (empty($data['statut'])) $data['statut'] = 'en_cours';
        apiSuccess(['id' => $handler->add($data), 'message' => 'Stage ajouté']);
    }
    if ($method === 'POST' && $action === 'update') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->update($data['id'], $data)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Stage mis à jour']);
    }
    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->delete($data['id'])) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Stage supprimé']);
    }
    apiError('Action inconnue', 404);
} catch (Exception $e) { apiError($e->getMessage(), 500); }
