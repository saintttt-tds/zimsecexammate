<?php
/**
 * ZimsecExamMate — Cache Rebuilder
 * 
 * Cron job: Rebuilds all JSON caches.
 * Run daily: 0 3 * * * php /path/to/cron/rebuild_cache.php
 */

require_once __DIR__ . '/../core/App.php';
appInit();

echo "[" . date('Y-m-d H:i:s') . "] Starting cache rebuild...\n";

// Clear all existing caches
Cache::clearAll();
echo "  Cleared existing caches.\n";

// Rebuild stats cache
$stats = Scanner::getStats();
Cache::set('stats', $stats);
echo "  Stats cache rebuilt: {$stats['total_resources']} resources, {$stats['total_subjects']} subjects.\n";

// Rebuild homepage popular cache
$approvedFiles = Scanner::scanApproved();
$popular = [];
foreach ($approvedFiles as $file) {
    $downloadPath = DOWNLOADS_DIR . '/' . $file['hash'] . '.json';
    $downloadData = json_decode(@file_get_contents($downloadPath), true) ?: ['count' => 0];
    $file['downloads'] = $downloadData['count'];
    $popular[] = $file;
}
usort($popular, fn($a, $b) => ($b['downloads'] ?? 0) <=> ($a['downloads'] ?? 0));
Cache::set('homepage_popular', array_slice($popular, 0, 12));
echo "  Homepage popular cache rebuilt: " . min(12, count($popular)) . " items.\n";

// Clean old search caches
$searchDir = CACHE_DIR . '/search';
if (is_dir($searchDir)) {
    $files = glob($searchDir . '/*.json');
    $deleted = 0;
    foreach ($files as $file) {
        if (time() - filemtime($file) > 86400) {
            @unlink($file);
            $deleted++;
        }
    }
    echo "  Cleaned {$deleted} old search caches.\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Cache rebuild complete.\n";