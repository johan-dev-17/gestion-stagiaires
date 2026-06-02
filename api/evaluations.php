<?php
require_once __DIR__ . '/_helper.php';
require_once __DIR__ . '/../includes/hybrid_handler.php';
apiInit();
$handler = new HybridHandler('evaluations.xml', 'evaluations', 'evaluation', 'evaluations');
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    if ($method === 'GET' && $action === 'list') apiSuccess(['items' => $handler->all()]);
    if ($method === 'POST' && $action === 'create') {
        $data = apiInput();
        if (empty($data['stagiaire_id'])) apiError('Stagiaire requis');
        $data['date_evaluation'] = date('Y-m-d');
        apiSuccess(['id' => $handler->add($data), 'message' => 'Évaluation ajoutée']);
    }
    if ($method === 'POST' && $action === 'delete') {
        $data = apiInput();
        if (empty($data['id']) || !$handler->delete($data['id'])) apiError('Introuvable', 404);
        apiSuccess(['message' => 'Évaluation supprimée']);
    }
    apiError('Action inconnue', 404);
} catch (Exception $e) { apiError($e->getMessage(), 500); }
