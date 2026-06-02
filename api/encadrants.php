<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();
$handler = new HybridHandler('encadrants.xml', 'encadrants', 'encadrant', 'encadrants');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') {
        $search = strtolower(trim($_GET['search'] ?? ''));
        $items = $handler->all();
        if ($search !== '') {
            $items = array_filter($items, fn($e) => strpos(strtolower($e['nom'].' '.$e['prenom'].' '.($e['email']??'')), $search) !== false);
        }
        apiSuccess(['items' => array_values($items)]);
    }
    if ($method === 'GET' && $action === 'get') {
        $item = $handler->find($_GET['id'] ?? null);
        if (!$item) apiError('Introuvable', 404);
        apiSuccess(['item' => $item]);
    }
    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['nom']) || empty($data['prenom'])) apiError('Nom et prénom requis');
        apiSuccess(['id' => $handler->add($data), 'message' => 'Encadrant ajouté']);
    }
    if ($method === 'POST' && $action === 'update') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->update($data['id'], $data)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Encadrant mis à jour']);
    }
    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->delete($data['id'])) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Encadrant supprimé']);
    }
    apiError('Action inconnue', 404);
} catch (Exception $e) { apiError($e->getMessage(), 500); }
