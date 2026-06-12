<?php
require_once __DIR__ . '/../core/App.php';
appInit();

header('Content-Type: application/json');

$hash = Helpers::getParam('hash', '');

if (empty($hash) || !preg_match('/^[a-f0-9]{32}$/i', $hash)) {
    echo json_encode(['success' => false, 'error' => 'Invalid hash']);
    exit;
}

$metadataPath = METADATA_DIR . '/' . $hash . '.json';

if (!file_exists($metadataPath)) {
    echo json_encode(['success' => false, 'error' => 'File not found']);
    exit;
}

$metadata = Helpers::readJson($metadataPath);
$votesPath = VOTES_DIR . '/' . $hash . '.json';
$votes = Helpers::readJson($votesPath, ['approvals' => [], 'rejections' => []]);

echo json_encode([
    'success'       => true,
    'status'        => $metadata['status'] ?? 'pending',
    'filename'      => $metadata['filename'] ?? 'unknown',
    'approvals'     => count($votes['approvals']),
    'rejections'    => count($votes['rejections']),
    'needed'        => VERIFICATION_THRESHOLD,
]);
exit;