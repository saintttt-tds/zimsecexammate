<?php
/**
 * ZimsecExamMate — Duplicate Scanner
 * 
 * Scans for duplicate files across all storage directories.
 * Run weekly: 0 6 * * 0 php /path/to/cron/duplicate_scan.php
 */

require_once __DIR__ . '/../core/App.php';
appInit();

echo "[" . date('Y-m-d H:i:s') . "] Starting duplicate scan...\n";

$hashIndex = [];
$duplicates = [];
$scannedCount = 0;

// Scan approved
foreach (LEVELS as $level) {
    $dir = APPROVED_DIR . '/' . strtoupper($level);
    if (!is_dir($dir)) continue;
    
    $files = glob($dir . '/*.pdf');
    foreach ($files as $file) {
        $scannedCount++;
        $sha = hash_file('sha256', $file);
        
        if (isset($hashIndex[$sha])) {
            $duplicates[] = [
                'original' => $hashIndex[$sha],
                'duplicate' => $file,
                'hash' => $sha,
            ];
        } else {
            $hashIndex[$sha] = $file;
        }
    }
}

// Scan pending
foreach (LEVELS as $level) {
    $dir = PENDING_DIR . '/' . strtoupper($level);
    if (!is_dir($dir)) continue;
    
    $files = glob($dir . '/*.pdf');
    foreach ($files as $file) {
        $scannedCount++;
        $sha = hash_file('sha256', $file);
        
        if (isset($hashIndex[$sha])) {
            $duplicates[] = [
                'original' => $hashIndex[$sha],
                'duplicate' => $file,
                'hash' => $sha,
            ];
        } else {
            $hashIndex[$sha] = $file;
        }
    }
}

// Report
echo "  Scanned: {$scannedCount} files\n";
echo "  Duplicates found: " . count($duplicates) . "\n";

if (!empty($duplicates)) {
    foreach ($duplicates as $dup) {
        echo "  DUPLICATE: " . basename($dup['duplicate']) . " matches " . basename($dup['original']) . "\n";
    }
}

// Update hash index
$indexPath = HASHES_DIR . '/index.json';
Helpers::writeJson($indexPath, $hashIndex);
echo "  Hash index updated.\n";

echo "[" . date('Y-m-d H:i:s') . "] Duplicate scan complete.\n";