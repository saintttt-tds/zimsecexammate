<?php
require_once __DIR__ . '/../core/App.php';
appInit();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flashSet('upload_error', 'Invalid request method.');
    Helpers::redirect('../uploadindex.php');
}

// Check for file
if (empty($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    flashSet('upload_error', 'No file uploaded or upload error.');
    Helpers::redirect('../uploadindex.php');
}

$file = $_FILES['pdf_file'];

// Validate extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    flashSet('upload_error', 'Only PDF files are accepted.');
    Helpers::redirect('../uploadindex.php');
}

// Validate MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, ['application/pdf', 'application/x-pdf'])) {
    flashSet('upload_error', 'Invalid file type. Only PDF files allowed.');
    Helpers::redirect('../uploadindex.php');
}

// Validate size (10MB for InfinityFree)
if ($file['size'] > 10 * 1024 * 1024) {
    flashSet('upload_error', 'File too large. Maximum size is 10MB.');
    Helpers::redirect('../uploadindex.php');
}

// Get form data
$level = trim($_POST['level'] ?? '');
$subjectCode = trim($_POST['subject_code'] ?? '');
$year = trim($_POST['year'] ?? '');
$resourceType = trim($_POST['resource_type'] ?? '');
$subtype = trim($_POST['subtype'] ?? '');

// Validate fields
if (empty($level) || empty($subjectCode) || empty($year) || empty($resourceType)) {
    flashSet('upload_error', 'All fields are required.');
    Helpers::redirect('../uploadindex.php');
}

// Build filename
$filename = $subjectCode . '_' . $year . '_' . $resourceType;
if (!empty($subtype)) {
    $filename .= '_' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $subtype));
}
$filename .= '.pdf';

// Get type directory
$typeDirs = [
    'PP' => 'pastpapers', 'MS' => 'markingschemes', 'TP' => 'topicalpapers',
    'NT' => 'notesandtextbooks', 'SY' => 'syllabi', 'PR' => 'projects',
];
$typeDir = $typeDirs[$resourceType] ?? 'pastpapers';

// Destination
$destDir = PDFS_DIR . '/' . $typeDir . '/' . $level;
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

$destination = $destDir . '/' . $filename;

// Handle duplicates
$counter = 1;
$base = pathinfo($filename, PATHINFO_FILENAME);
while (file_exists($destination)) {
    $destination = $destDir . '/' . $base . '_' . $counter . '.pdf';
    $counter++;
}

// Move file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    flashSet('upload_error', 'Failed to save file. Please try again.');
    Helpers::redirect('../uploadindex.php');
}

// Save metadata
$hash = md5_file($destination);
$subject = Config::findSubject($subjectCode);
$subjectName = $subject['name'] ?? 'Subject ' . $subjectCode;

$metadata = [
    'filename'             => basename($destination),
    'original_name'        => $file['name'],
    'subject_code'         => $subjectCode,
    'subject_name'         => $subjectName,
    'year'                 => $year,
    'level'                => $level,
    'resource_type'        => RESOURCE_TYPES[$resourceType] ?? $resourceType,
    'resource_type_code'   => $resourceType,
    'resource_type_display'=> RESOURCE_TYPE_DISPLAY[RESOURCE_TYPES[$resourceType] ?? ''] ?? $resourceType,
    'subtype'              => strtoupper($subtype),
    'file_size'            => filesize($destination),
    'file_size_formatted'  => Helpers::formatFileSize(filesize($destination)),
    'hash'                 => $hash,
    'file_path'            => $destination,
    'status'               => 'pending',
    'modified'             => date('F d, Y'),
    'modified_timestamp'   => time(),
    'is_parsed'            => true,
];

$parsed = Parser::parseFilename(basename($destination));
$metadata = array_merge($metadata, $parsed);

Helpers::writeJson(METADATA_DIR . '/' . $hash . '.json', $metadata);
Helpers::writeJson(VOTES_DIR . '/' . $hash . '.json', ['approvals' => [], 'rejections' => []]);

Cache::clearAll();

flashSet('upload_success', 'File uploaded successfully! It now needs 3 community approvals.');
Helpers::redirect('../uploadindex.php');