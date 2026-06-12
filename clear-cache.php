<?php
/**
 * ZimsecExamMate — Clear Cache
 * Run once to refresh stats and search indexes.
 */

require_once __DIR__ . '/core/App.php';
appInit();

// Clear all caches via Cache class
Cache::clearAll();

// Also manually delete specific cache files
$cacheFiles = [
    CACHE_DIR . '/stats.json',
    CACHE_DIR . '/homepage_popular.json',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

// Clear search cache directory
$searchDir = CACHE_DIR . '/search';
if (is_dir($searchDir)) {
    $searchFiles = glob($searchDir . '/*.json');
    if ($searchFiles) {
        foreach ($searchFiles as $file) {
            unlink($file);
        }
    }
}

// Clear subject cache directory
$subjectsDir = CACHE_DIR . '/subjects';
if (is_dir($subjectsDir)) {
    $subjectFiles = glob($subjectsDir . '/*.json');
    if ($subjectFiles) {
        foreach ($subjectFiles as $file) {
            unlink($file);
        }
    }
}

// Clear homepage cache directory
$homepageDir = CACHE_DIR . '/homepage';
if (is_dir($homepageDir)) {
    $homepageFiles = glob($homepageDir . '/*.json');
    if ($homepageFiles) {
        foreach ($homepageFiles as $file) {
            unlink($file);
        }
    }
}

// Count remaining cache files
$remainingFiles = 0;
$cacheDirs = ['', 'search', 'subjects', 'homepage'];
foreach ($cacheDirs as $dir) {
    $path = CACHE_DIR . ($dir ? '/' . $dir : '');
    if (is_dir($path)) {
        $files = glob($path . '/*.json');
        $remainingFiles += count($files ?: []);
    }
}

// Count actual PDFs
$totalPDFs = 0;
foreach (TYPE_DIR_MAP as $typeDir) {
    foreach (LEVELS as $lvl) {
        $dir = PDFS_DIR . '/' . $typeDir . '/' . $lvl;
        if (is_dir($dir)) {
            $pdfs = glob($dir . '/*.pdf');
            $totalPDFs += count($pdfs ?: []);
        }
    }
}
foreach (LEVELS as $lvl) {
    $dir = APPROVED_DIR . '/' . $lvl;
    if (is_dir($dir)) {
        $pdfs = glob($dir . '/*.pdf');
        $totalPDFs += count($pdfs ?: []);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clear Cache - ZIMSEC ExamMate</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); text-align: center; }
        h1 { color: #1e3c72; }
        .stat { font-size: 2rem; font-weight: bold; color: #1e3c72; }
        .label { color: #666; font-size: 0.9rem; }
        .btn { display: inline-block; margin: 10px; padding: 12px 24px; background: #1e3c72; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .btn:hover { background: #15294f; }
        .success { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✅ Cache Cleared</h1>
        <p class="success">All caches have been cleared successfully.</p>
        
        <div style="margin: 20px 0; padding: 20px; background: #f0f4ff; border-radius: 8px;">
            <div class="stat"><?= $totalPDFs ?></div>
            <div class="label">Total PDFs Found</div>
        </div>
        
        <p style="color: #666; font-size: 0.85rem;">Remaining cache files: <?= $remainingFiles ?></p>
        <p style="color: #666; font-size: 0.85rem;">Stats will rebuild on next page load.</p>
        
        <a href="index.php" class="btn">Go to Homepage</a>
        <a href="pastpapers.php" class="btn">Browse Past Papers</a>
    </div>
</body>
</html>