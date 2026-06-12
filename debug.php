<?php
/**
 * Debug script — helps identify initialization errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>ZimsecExamMate Debug</h2>";

// Step 1: Check PHP version
echo "<p>PHP Version: " . phpversion() . " (need 7.4+)</p>";

// Step 2: Check if config files exist
$files = [
    'config/constants.php',
    'config/session.php',
    'core/App.php',
    'core/Config.php',
    'core/Helpers.php',
];

foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<p>" . ($exists ? '✅' : '❌') . " {$file}: " . ($exists ? 'Found' : 'MISSING') . "</p>";
    if (!$exists) {
        echo "<p style='color:red'>Please create this file!</p>";
    }
}

// Step 3: Try loading constants
echo "<h3>Loading Constants...</h3>";
try {
    require_once __DIR__ . '/config/constants.php';
    echo "<p>✅ Constants loaded. ROOT_DIR = " . ROOT_DIR . "</p>";
    echo "<p>STORAGE_DIR = " . STORAGE_DIR . "</p>";
    echo "<p>LEVELS = " . implode(', ', LEVELS) . "</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Constants error: " . $e->getMessage() . "</p>";
}

// Step 4: Try loading session
echo "<h3>Loading Session...</h3>";
try {
    require_once __DIR__ . '/config/session.php';
    echo "<p>✅ Session loaded. Session ID: " . session_id() . "</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Session error: " . $e->getMessage() . "</p>";
}

// Step 5: Try loading core files directly (no namespaces)
echo "<h3>Loading Core Files...</h3>";

$coreFiles = ['Config.php', 'Helpers.php', 'Security.php', 'Cache.php', 'Parser.php', 'Scanner.php', 'Validator.php', 'Moderation.php', 'Search.php'];

foreach ($coreFiles as $file) {
    $path = __DIR__ . '/core/' . $file;
    if (file_exists($path)) {
        try {
            require_once $path;
            echo "<p>✅ core/{$file} loaded</p>";
        } catch (Throwable $e) {
            echo "<p style='color:red'>❌ core/{$file}: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        echo "<p style='color:red'>❌ core/{$file}: File not found at {$path}</p>";
    }
}

// Step 6: Check storage directories
echo "<h3>Storage Directories...</h3>";
$dirs = [
    STORAGE_DIR ?? 'storage',
    APPROVED_DIR ?? 'storage/approved',
    PENDING_DIR ?? 'storage/pending',
];

foreach ($dirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    $exists = is_dir($fullPath);
    $writable = $exists && is_writable($fullPath);
    echo "<p>" . ($exists ? ($writable ? '✅' : '⚠️') : '❌') . " {$dir}: " . 
         ($exists ? ($writable ? 'Exists and writable' : 'Exists but NOT WRITABLE') : 'MISSING') . "</p>";
}

// Step 7: Test a simple page render
echo "<h3>Test Output...</h3>";
echo "<p>If you can see this, PHP is working. The 500 error is likely in the template or page logic.</p>";
echo "<p>Try removing the namespace 'use' statements from the page files and using require_once directly.</p>";