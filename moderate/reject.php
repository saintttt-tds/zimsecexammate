<?php
require_once __DIR__ . '/../core/App.php';
appInit();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$hash = $input['hash'] ?? '';

if (empty($hash)) {
    echo json_encode(['success' => false, 'error' => 'Missing file hash']);
    exit;
}

$result = Moderation::vote($hash, 'reject');

if ($result['success']) {
    Cache::clearAll();
}

echo json_encode($result);
exit;