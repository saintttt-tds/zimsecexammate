<?php
/**
 * ZimsecExamMate — Helper Functions
 * 
 * Utility functions used throughout the application.
 */

class Helpers
{
    /**
     * Format file size into human-readable string
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get display name for an education level
     */
    public static function levelDisplay(string $level): string
    {
        return LEVEL_DISPLAY[$level] ?? ucfirst($level);
    }

    /**
     * Get display name for a resource type
     */
    public static function resourceTypeDisplay(string $type): string
    {
        return RESOURCE_TYPE_DISPLAY[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Generate a random hash for file naming
     */
    public static function randomHash(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Sanitize a string for safe output in HTML
     */
    public static function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize a filename — remove dangerous characters
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path traversal
        $filename = basename($filename);
        // Remove anything that isn't alphanumeric, dot, dash, or underscore
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        // Collapse multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        return $filename;
    }

    /**
     * Check if a string starts with a given prefix
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    /**
     * Truncate a string to a given length with ellipsis
     */
    public static function truncate(string $text, int $length = 100): string
    {
        if (mb_strlen($text) <= $length) return $text;
        return mb_substr($text, 0, $length) . '…';
    }

    /**
     * Get current page name from URL
     */
    public static function currentPage(): string
    {
        return basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: 'index.php');
    }

    /**
     * Check if a page is active for navigation highlighting
     */
    public static function isActive(string $page): bool
    {
        return self::currentPage() === $page;
    }

    /**
     * Get the client's IP address (respecting proxies)
     */
    public static function clientIp(): string
    {
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    /**
     * Check if the request is an AJAX request
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Send a JSON response and exit
     */
    public static function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to another URL
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Get a GET parameter with a default
     */
    public static function getParam(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /**
     * Build a query string preserving existing params plus new ones
     */
    public static function buildQuery(array $overrides = [], array $remove = []): string
    {
        $params = $_GET;
        foreach ($overrides as $key => $value) {
            $params[$key] = $value;
        }
        foreach ($remove as $key) {
            unset($params[$key]);
        }
        // Remove empty values
        $params = array_filter($params, fn($v) => $v !== '' && $v !== 'all');
        return http_build_query($params);
    }

    /**
     * Ensure a directory exists, create if not
     */
    public static function ensureDir(string $path, int $permissions = 0755): bool
    {
        if (is_dir($path)) return true;
        return mkdir($path, $permissions, true);
    }

    /**
     * Write JSON to a file atomically
     */
    public static function writeJson(string $path, array $data): bool
    {
        $dir = dirname($path);
        self::ensureDir($dir);
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $tmp = $path . '.tmp.' . uniqid();
        
        if (file_put_contents($tmp, $json, LOCK_EX) === false) {
            return false;
        }
        
        return rename($tmp, $path);
    }

    /**
     * Read JSON from a file, return default if not found
     */
    public static function readJson(string $path, array $default = []): array
    {
        if (!file_exists($path)) return $default;
        $json = file_get_contents($path);
        if ($json === false) return $default;
        $data = json_decode($json, true);
        return is_array($data) ? $data : $default;
    }

    /**
     * Generate a slug from a string
     */
    public static function slug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}