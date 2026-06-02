<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();

if (!hasRole('admin')) {
    apiError('Accès refusé', 403);
}

$handler = new HybridHandler('users.xml', 'users', 'user', 'users');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') {
        $items = $handler->all();
        // Remove passwords from output for security
        foreach ($items as &$item) {
            unset($item['password']);
        }
        apiSuccess(['items' => array_values($items)]);
    }

    if ($method === 'GET' && $action === 'get') {
        $id = $_GET['id'] ?? null;
        $item = $handler->find($id);
        if (!$item) apiError('Introuvable', 404);
        unset($item['password']); // Never send password hash to client
        apiSuccess(['item' => $item]);
    }

    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['username']) || empty($data['nom']) || empty($data['role']) || empty($data['password'])) {
            apiError('Tous les champs sont requis pour la création');
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $id = $handler->add($data);
        apiSuccess(['id' => $id, 'message' => 'Compte créé']);
    }

    if ($method === 'POST' && $action === 'update') {
        $data = apiInput();
        $id = $data['id'] ?? null;
        if (!$id) apiError('ID manquant');
        
        if (empty($data['password'])) {
            unset($data['password']); // Keep existing password
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (!$handler->update($id, $data)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Compte mis à jour']);
    }

    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        $id = $data['id'] ?? null;
        if (!$id) apiError('ID manquant');
        
        // Prevent deleting the last admin or oneself if possible
        $user = $handler->find($id);
        if ($user && $user['username'] === currentUser()['username']) {
            apiError('Vous ne pouvez pas supprimer votre propre compte', 400);
        }
        
        if (!$handler->delete($id)) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Compte supprimé']);
    }

    apiError('Action inconnue', 404);
} catch (Exception $e) {
    apiError($e->getMessage(), 500);
}
