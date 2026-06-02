<?php
require_once __DIR__ . '/_helper.php';
apiInit();

$stagiaires = (new XMLHandler('stagiaires.xml', 'stagiaires', 'stagiaire'))->all();
$encadrants = (new XMLHandler('encadrants.xml', 'encadrants', 'encadrant'))->all();
$departements = (new XMLHandler('departements.xml', 'departements', 'departement'))->all();
$stages = (new XMLHandler('stages.xml', 'stages', 'stage'))->all();

$parStatut = ['en_cours' => 0, 'termine' => 0, 'annule' => 0];
foreach ($stagiaires as $s) {
    $st = $s['statut'] ?? 'en_cours';
    if (isset($parStatut[$st])) $parStatut[$st]++;
}

$parDepartement = [];
foreach ($departements as $d) $parDepartement[$d['nom']] = 0;
foreach ($stagiaires as $s) {
    $dep = $s['departement'] ?? 'Non assigné';
    $parDepartement[$dep] = ($parDepartement[$dep] ?? 0) + 1;
}

apiSuccess([
    'totaux' => [
        'stagiaires' => count($stagiaires),
        'encadrants' => count($encadrants),
        'departements' => count($departements),
        'stages' => count($stages),
        'actifs' => $parStatut['en_cours'],
    ],
    'parStatut' => $parStatut,
    'parDepartement' => $parDepartement,
]);
