<?php
/**
 * ZimsecExamMate — Pending File Cleaner
 * 
 * Removes pending files older than PENDING_EXPIRY_DAYS.
 * Run daily: 0 4 * * * php /path/to/cron/clean_pending.php
 */

require_once __DIR__ . '/../core/App.php';
appInit();

echo "[" . date('Y-m-d H:i:s') . "] Starting pending file cleanup...\n";

$expirySeconds = PENDING_EXPIRY_DAYS * 86400;
$now = time();
$deletedCount = 0;

foreach (LEVELS as $level) {
    $dir = PENDING_DIR . '/' . strtoupper($level);
    if (!is_dir($dir)) continue;

    $files = glob($dir . '/*.pdf');
    foreach ($files as $file) {
        $age = $now - filemtime($file);
        if ($age > $expirySeconds) {
            $filename = basename($file);
            $hash = md5_file($file);

            // Remove the file
            if (@unlink($file)) {
                $deletedCount++;
                echo "  Deleted: {$filename} (expired, {$age} seconds old)\n";

                // Remove associated data
                @unlink(VOTES_DIR . '/' . $hash . '.json');
                @unlink(METADATA_DIR . '/' . $hash . '.json');
                
                // Remove from hash index
                $hashIndex = HASHES_DIR . '/index.json';
                $hashes = Helpers::readJson($hashIndex, []);
                $shaHash = hash_file('sha256', $file);
                unset($hashes[$shaHash]);
                Helpers::writeJson($hashIndex, $hashes);
            }
        }
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Cleanup complete. Deleted {$deletedCount} expired pending files.\n";