<?php
/**
 * ZimsecExamMate — Security Module
 * 
 * Rate limiting, CSRF protection, input sanitization,
 * and file upload security.
 */

class Security
{
    /**
     * Check rate limit for an action
     */
    public static function checkRateLimit(string $action, int $maxRequests, int $windowSeconds = 3600): bool
    {
        $ip = Helpers::clientIp();
        $rateFile = CACHE_DIR . '/ratelimit_' . md5($ip . '_' . $action) . '.json';
        
        $data = Helpers::readJson($rateFile, [
            'requests' => [],
            'blocked_until' => 0,
        ]);

        $now = time();

        // Check if currently blocked
        if ($data['blocked_until'] > $now) {
            return false;
        }

        // Remove old requests outside the window
        $data['requests'] = array_filter($data['requests'], fn($t) => $t > ($now - $windowSeconds));

        // Check if limit exceeded
        if (count($data['requests']) >= $maxRequests) {
            $data['blocked_until'] = $now + $windowSeconds;
            Helpers::writeJson($rateFile, $data);
            return false;
        }

        // Record this request
        $data['requests'][] = $now;
        Helpers::writeJson($rateFile, $data);

        return true;
    }

    /**
     * Get remaining requests for rate limit
     */
    public static function rateLimitRemaining(string $action, int $maxRequests): int
    {
        $ip = Helpers::clientIp();
        $rateFile = CACHE_DIR . '/ratelimit_' . md5($ip . '_' . $action) . '.json';
        $data = Helpers::readJson($rateFile, ['requests' => []]);
        
        $now = time();
        $data['requests'] = array_filter($data['requests'], fn($t) => $t > ($now - RATE_LIMIT_WINDOW));
        
        return max(0, $maxRequests - count($data['requests']));
    }

    /**
     * Clean up old rate limit files
     */
    public static function cleanRateLimits(): void
    {
        $now = time();
        $files = glob(CACHE_DIR . '/ratelimit_*.json');
        
        foreach ($files as $file) {
            $age = $now - filemtime($file);
            if ($age > 86400) { // Older than 24 hours
                @unlink($file);
            }
        }
    }

    /**
     * Generate a CSRF token input field
     */
    public static function csrfField(): string
    {
        $token = csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . Helpers::h($token) . '">';
    }

    /**
     * Verify CSRF token from POST request
     */
    public static function verifyCsrfRequest(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
        
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token)) {
            $json = json_decode(file_get_contents('php://input'), true);
            $token = $json['csrf_token'] ?? '';
        }

        return verifyCsrf($token);
    }

    /**
     * Sanitize a string for output
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Generate a secure random token
     */
    public static function randomToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Check for duplicate file by SHA256 hash
     */
    public static function checkDuplicate(string $filePath): ?string
    {
        $hash = hash_file('sha256', $filePath);
        $hashIndex = HASHES_DIR . '/index.json';
        $hashes = Helpers::readJson($hashIndex, []);

        if (isset($hashes[$hash])) {
            return $hashes[$hash]; // Return existing filename
        }

        return null; // No duplicate
    }

    /**
     * Register a file hash in the duplicate index
     */
    public static function registerHash(string $filePath, string $filename): void
    {
        $hash = hash_file('sha256', $filePath);
        $hashIndex = HASHES_DIR . '/index.json';
        $hashes = Helpers::readJson($hashIndex, []);
        $hashes[$hash] = $filename;
        Helpers::writeJson($hashIndex, $hashes);
    }

    /**
     * Check if request appears to be a bot
     */
    public static function isBot(): bool
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        $botPatterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java/'];
        
        foreach ($botPatterns as $pattern) {
            if (strpos($ua, $pattern) !== false) return true;
        }

        return false;
    }
}