<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$entity = $_GET['entity'] ?? 'stagiaires';
$allowed = ['stagiaires', 'encadrants', 'departements', 'stages', 'evaluations'];
if (!in_array($entity, $allowed)) {
    http_response_code(400);
    exit('Entité invalide');
}

$file = DATA_PATH . $entity . '.xml';
if (!file_exists($file)) {
    http_response_code(404);
    exit('Fichier introuvable');
}

$filename = $entity . '_' . date('Y-m-d_His') . '.xls';

// Forcer le téléchargement au format Excel (.xls)
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$xml = simplexml_load_file($file);

// On utilise le "HTML Excel Trick" qui est nativement compris par Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta charset="utf-8"></head><body>';
echo '<table border="1">';
echo '<thead><tr>';

$first = true;
foreach ($xml->children() as $item) {
    if ($first) {
        foreach ($item->children() as $child) {
            echo '<th style="background-color:#4f46e5; color:#ffffff; font-weight:bold;">' . htmlspecialchars(ucfirst($child->getName())) . '</th>';
        }
        echo '</tr></thead><tbody>';
        $first = false;
    }
    echo '<tr>';
    foreach ($item->children() as $child) {
        echo '<td>' . htmlspecialchars((string)$child) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table></body></html>';
