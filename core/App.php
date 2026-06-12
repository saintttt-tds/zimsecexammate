<?php
/**
 * ZimsecExamMate — Application Bootstrap
 * No namespaces, direct requires.
 */

define('APP_LOADED', true);

// Load constants first
require_once __DIR__ . '/../config/constants.php';

// Load session
require_once __DIR__ . '/../config/session.php';

// Load all core files in dependency order
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Scanner.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Moderation.php';
require_once __DIR__ . '/Search.php';

/**
 * Initialize the application
 */
function appInit(): void
{
    // Load configuration
    Config::load();

    // Set up error handling
    set_error_handler(function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) return false;
        $error = date('[Y-m-d H:i:s]') . " {$message} in {$file}:{$line}\n";
        @error_log($error, 3, LOGS_DIR . '/errors.log');
        return true;
    });

    // Create type directories for each level
    $types = ['pastpapers', 'markingschemes', 'topicalpapers', 'notesandtextbooks', 'syllabi', 'projects'];
    $statuses = ['approved', 'pending'];
    $systemDirs = [REJECTED_DIR, VOTES_DIR, HASHES_DIR, METADATA_DIR, CACHE_DIR, DOWNLOADS_DIR, LOGS_DIR];

    foreach ($types as $type) {
        foreach (LEVELS as $level) {
            $dir = PDFS_DIR . '/' . $type . '/' . $level;
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
        }
    }

    foreach ($statuses as $status) {
        foreach (LEVELS as $level) {
            $dir = PDFS_DIR . '/' . $status . '/' . $level;
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
        }
    }

    foreach ($systemDirs as $dir) {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
    }
}