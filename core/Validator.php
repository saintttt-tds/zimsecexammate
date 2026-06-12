<?php
/**
 * ZimsecExamMate — Input & Upload Validator
 * 
 * Validates all user inputs: form data, file uploads,
 * search queries, and moderation votes.
 */

class Validator
{
    private static array $errors = [];

    /**
     * Validate a file upload
     */
    public static function validateUpload(array $file, array $formData): array
    {
        self::$errors = [];

        // ─── File presence ──────────────────────────
        if (empty($file) || !isset($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            self::$errors[] = 'No file was uploaded.';
            return self::result();
        }

        // ─── File errors ────────────────────────────
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error: missing temp directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server configuration error: cannot write to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload stopped by a PHP extension.',
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            self::$errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error (code: ' . $file['error'] . ').';
            return self::result();
        }

        // ─── File size ──────────────────────────────
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            self::$errors[] = 'File is too large. Maximum size is ' . Helpers::formatFileSize(MAX_UPLOAD_SIZE) . '.';
        }

        if ($file['size'] === 0) {
            self::$errors[] = 'File is empty.';
        }

        // ─── File extension ─────────────────────────
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            self::$errors[] = 'Only PDF files are accepted.';
        }

        // ─── MIME type ──────────────────────────────
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($detectedMime, ALLOWED_MIME_TYPES)) {
            self::$errors[] = 'Invalid file type detected. Only PDF files are accepted.';
        }

        // ─── Form data validation ───────────────────
        $level = trim($formData['level'] ?? '');
        if (!in_array($level, LEVELS)) {
            self::$errors[] = 'Please select a valid education level.';
        }

        $subjectCode = trim($formData['subject_code'] ?? '');
        if (!preg_match('/^\d{4}$/', $subjectCode)) {
            self::$errors[] = 'Invalid subject code.';
        } else {
            // Verify subject exists
            $subject = Config::findSubject($subjectCode);
            if (!$subject) {
                self::$errors[] = 'Subject code not found in our registry.';
            }
        }

        $year = trim($formData['year'] ?? '');
        if (!preg_match('/^\d{4}$/', $year)) {
            self::$errors[] = 'Please enter a valid year (e.g., 2024).';
        } elseif ((int) $year < 2000 || (int) $year > (int) date('Y') + 1) {
            self::$errors[] = 'Year must be between 2000 and ' . (date('Y') + 1) . '.';
        }

        $resourceType = trim($formData['resource_type'] ?? '');
        $validTypes = array_keys(RESOURCE_TYPES);
        if (!in_array($resourceType, $validTypes)) {
            self::$errors[] = 'Please select a valid resource type.';
        }

        // ─── Filename security ──────────────────────
        $originalName = $file['name'];
        if (preg_match('/\.(php|phtml|php3|php4|php5|php7|phar|pl|py|cgi|asp|aspx|jsp|sh|bash|exe|bat|cmd|com)$/i', $originalName)) {
            self::$errors[] = 'Suspicious file extension detected.';
        }

        // Double extension attack check
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        if (preg_match('/\.(php|phtml|exe|bat)$/i', $nameWithoutExt)) {
            self::$errors[] = 'Suspicious double extension detected.';
        }

        return self::result();
    }

    /**
     * Validate a search query
     */
    public static function validateSearch(string $query): string
    {
        // Strip HTML tags
        $query = strip_tags($query);
        // Remove control characters
        $query = preg_replace('/[\x00-\x1F\x7F]/', '', $query);
        // Limit length
        $query = mb_substr(trim($query), 0, 200);
        return $query;
    }

    /**
     * Validate a moderation vote
     */
    public static function validateVote(string $fileHash): bool
    {
        // Hash should be exactly 32 hex characters (MD5) or 64 (SHA256)
        if (preg_match('/^[a-f0-9]{32}$/i', $fileHash)) return true;
        if (preg_match('/^[a-f0-9]{64}$/i', $fileHash)) return true;
        return false;
    }

    /**
     * Validate filter parameters
     */
    public static function validateFilters(array $params): array
    {
        $cleaned = [];

        // Level filter
        $level = $params['level'] ?? 'all';
        $cleaned['level'] = in_array($level, LEVELS) ? $level : 'all';

        // Year filter
        $year = $params['year'] ?? 'all';
        $cleaned['year'] = (preg_match('/^\d{4}$/', $year) && (int) $year >= 2000 && (int) $year <= (int) date('Y') + 1) 
            ? $year 
            : 'all';

        // Type filter
        $type = $params['type'] ?? 'all';
        $validTypes = array_merge(['all'], array_unique(array_values(RESOURCE_TYPES)));
        $cleaned['type'] = in_array($type, $validTypes) ? $type : 'all';

        // Subject filter
        $subject = $params['subject'] ?? 'all';
        $cleaned['subject'] = ($subject !== 'all') ? Helpers::h($subject) : 'all';

        // Search query
        $search = $params['search'] ?? '';
        $cleaned['search'] = self::validateSearch($search);

        // Category filter
        $category = $params['category'] ?? 'all';
        $categories = Config::getCategories();
        $cleaned['category'] = in_array($category, $categories) ? $category : 'all';

        // Topic filter
        $topic = $params['topic'] ?? 'all';
        $cleaned['topic'] = $topic !== 'all' ? Helpers::h($topic) : 'all';

        return $cleaned;
    }

    /**
     * Validate file hash for downloads
     */
    public static function validateHash(string $hash): bool
    {
        return (bool) preg_match('/^[a-f0-9]{32,64}$/i', $hash);
    }

    /**
     * Get validation errors
     */
    public static function getErrors(): array
    {
        return self::$errors;
    }

    /**
     * Check if validation passed
     */
    public static function passed(): bool
    {
        return empty(self::$errors);
    }

    /**
     * Build result array
     */
    private static function result(): array
    {
        return [
            'valid'  => empty(self::$errors),
            'errors' => self::$errors,
        ];
    }
}