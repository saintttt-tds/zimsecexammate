<?php

/**
 * ZimsecExamMate — JSON Cache Layer
 * 
 * Simple file-based caching system.
 * Stores computed data as JSON files with TTL.
 */

class Cache
{
    /**
     * Get a cached value
     */
    public static function get(string $key, int $ttl = null): ?array
    {
        $ttl = $ttl ?? CACHE_TTL;
        $path = self::path($key);

        if (!file_exists($path)) return null;

        $age = time() - filemtime($path);
        if ($age > $ttl) {
            @unlink($path);
            return null;
        }

        return Helpers::readJson($path);
    }

    /**
     * Set a cached value
     */
    public static function set(string $key, array $data): bool
    {
        $path = self::path($key);
        $dir = dirname($path);
        Helpers::ensureDir($dir);
        return Helpers::writeJson($path, $data);
    }

    /**
     * Check if a cache key exists and is valid
     */
    public static function has(string $key, int $ttl = null): bool
    {
        $ttl = $ttl ?? CACHE_TTL;
        $path = self::path($key);

        if (!file_exists($path)) return false;
        return (time() - filemtime($path)) < $ttl;
    }

    /**
     * Delete a specific cache key
     */
    public static function delete(string $key): void
    {
        $path = self::path($key);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Clear all cache for a namespace
     */
    public static function clear(string $namespace = ''): void
    {
        $dir = CACHE_DIR . '/' . $namespace;
        if (!is_dir($dir)) return;

        $files = glob($dir . '/*.json');
        foreach ($files as $file) {
            @unlink($file);
        }

        // Also clear subdirectories
        $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $files = glob($subdir . '/*.json');
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    /**
     * Clear all caches
     */
    public static function clearAll(): void
    {
        self::clear('search');
        self::clear('subjects');
        self::clear('homepage');
        self::delete('stats.json');
    }

    /**
     * Get cache statistics
     */
    public static function stats(): array
    {
        $totalFiles = 0;
        $totalSize = 0;
        $dirs = ['search', 'subjects', 'homepage', ''];

        foreach ($dirs as $dir) {
            $path = CACHE_DIR . '/' . $dir;
            if (!is_dir($path)) continue;

            $files = glob($path . '/*.json');
            foreach ($files as $file) {
                $totalFiles++;
                $totalSize += filesize($file) ?: 0;
            }
        }

        return [
            'total_files' => $totalFiles,
            'total_size'  => Helpers::formatFileSize($totalSize),
            'cache_dir'   => CACHE_DIR,
        ];
    }

    /**
     * Get the file path for a cache key
     */
    private static function path(string $key): string
    {
        $key = preg_replace('/[^a-zA-Z0-9_\/-]/', '_', $key);
        
        // If key contains a slash, treat as subdirectory
        if (strpos($key, '/') !== false) {
            return CACHE_DIR . '/' . $key . '.json';
        }

        return CACHE_DIR . '/' . $key . '.json';
    }
}