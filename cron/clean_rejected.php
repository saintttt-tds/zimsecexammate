<?php
/**
 * ZimsecExamMate — Rejected File Cleaner
 * 
 * Permanently deletes rejected files older than REJECTED_EXPIRY_DAYS.
 * Run weekly: 0 5 * * 0 php /path/to/cron/clean_rejected.php
 */

require_once __DIR__ . '/../core/App.php';
appInit();

echo "[" . date('Y-m-d H:i:s') . "] Starting rejected file cleanup...\n";

$expirySeconds = REJECTED_EXPIRY_DAYS * 86400;
$now = time();
$deletedCount = 0;
$freedSpace = 0;

if (!is_dir(REJECTED_DIR)) {
    echo "  Rejected directory does not exist. Nothing to clean.\n";
    exit;
}

$files = glob(REJECTED_DIR . '/*.pdf');
foreach ($files as $file) {
    $age = $now - filemtime($file);
    if ($age > $expirySeconds) {
        $filename = basename($file);
        $fileSize = filesize($file) ?: 0;
        
        if (@unlink($file)) {
            $deletedCount++;
            $freedSpace += $fileSize;
            echo "  Permanently deleted: {$filename}\n";
        }
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Cleanup complete.\n";
echo "  Deleted: {$deletedCount} files\n";
echo "  Freed space: " . Helpers::formatFileSize($freedSpace) . "\n";