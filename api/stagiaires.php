<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();

$handler = new HybridHandler('stagiaires.xml', 'stagiaires', 'stagiaire', 'stagiaires');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') {
        $search = strtolower(trim($_GET['search'] ?? ''));
        $departement = $_GET['departement'] ?? '';
        $statut = $_GET['statut'] ?? '';
        $items = $handler->all();
        if ($search !== '') {
            $items = array_filter($items, function($s) use ($search) {
                return strpos(strtolower($s['nom'] . ' ' . $s['prenom'] . ' ' . ($s['email'] ?? '') . ' ' . ($s['ecole'] ?? '')), $search) !== false;
            });
        }
        if ($departement !== '') $items = array_filter($items, fn($s) => ($s['departement'] ?? '') === $departement);
        if ($statut !== '') $items = array_filter($items, fn($s) => ($s['statut'] ?? '') === $statut);
        apiSuccess(['items' => array_values($items)]);
    }

    if ($method === 'GET' && $action === 'get') {
        $id = $_GET['id'] ?? null;
        $item = $handler->find($id);
        if (!$item) apiError('Introuvable', 404);
        apiSuccess(['item' => $item]);
    }

    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['nom']) || empty($data['prenom'])) apiError('Nom et prénom requis');
        $data['date_creation'] = date('Y-m-d');
        $id = $handler->add($data);
        apiSuccess(['id' => $id, 'message' => 'Stagiaire ajouté']);
    }

    if ($method === 'POST' && $action === 'update') {
        $data = apiInput();
        $id = $data['id'] ?? null;
        if (!$id) apiError('ID manquant');
        if (!$handler->update($id, $data)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Stagiaire mis à jour']);
    }

    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        $id = $data['id'] ?? null;
        if (!$id) apiError('ID manquant');
        if (!$handler->delete($id)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Stagiaire supprimé']);
    }

    apiError('Action inconnue', 404);
} catch (Exception $e) {
    apiError($e->getMessage(), 500);
}
