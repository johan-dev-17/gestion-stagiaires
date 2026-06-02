<?php
// Capture the output of export.php
$_GET['entity'] = 'encadrants';
ob_start();
include __DIR__ . '/api/export.php';
$output = ob_get_clean();
file_put_contents(__DIR__ . '/export_dump.txt', $output);
echo "Dumped " . strlen($output) . " bytes";
