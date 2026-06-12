<?php
/**
 * ZimsecExamMate — Configuration Loader
 * 
 * Loads and provides access to all configuration.
 * No database — everything from JSON files and constants.
 */

class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    /**
     * Load all configuration
     */
    public static function load(): void
    {
        if (self::$loaded) return;

        // Load subjects
        $subjectsPath = DATA_DIR . '/subjects.json';
        if (file_exists($subjectsPath)) {
            $json = file_get_contents($subjectsPath);
            self::$config['subjects'] = json_decode($json, true) ?: [];
        } else {
            self::$config['subjects'] = ['grade7' => [], 'zjc' => [], 'olevel' => [], 'alevel' => []];
        }

        // Load FAQ
        $faqPath = DATA_DIR . '/faq.json';
        if (file_exists($faqPath)) {
            $json = file_get_contents($faqPath);
            self::$config['faq'] = json_decode($json, true) ?: [];
        } else {
            self::$config['faq'] = [];
        }

        // Load writing guides
        $guidesPath = DATA_DIR . '/writing-guides.json';
        if (file_exists($guidesPath)) {
            $json = file_get_contents($guidesPath);
            self::$config['writing_guides'] = json_decode($json, true) ?: [];
        } else {
            self::$config['writing_guides'] = [];
        }

        // Load revision notes
        $revisionPath = DATA_DIR . '/revision-notes.json';
        if (file_exists($revisionPath)) {
            $json = file_get_contents($revisionPath);
            self::$config['revision_notes'] = json_decode($json, true) ?: [];
        } else {
            self::$config['revision_notes'] = [];
        }

        // Load levels
        $levelsPath = DATA_DIR . '/levels.json';
        if (file_exists($levelsPath)) {
            $json = file_get_contents($levelsPath);
            self::$config['levels'] = json_decode($json, true) ?: [];
        } else {
            self::$config['levels'] = LEVEL_DISPLAY;
        }

        self::$loaded = true;
    }

    /**
     * Get a config value by dot notation
     * e.g., Config::get('subjects.olevel') 
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Get all subjects for a specific level
     */
    public static function getSubjects(string $level): array
    {
        return self::get("subjects.{$level}", []);
    }

    /**
     * Get all subjects across all levels, flattened
     */
    public static function getAllSubjects(): array
    {
        self::load();
        $all = [];
        foreach (LEVELS as $level) {
            foreach (self::$config['subjects'][$level] ?? [] as $subject) {
                $all[] = $subject;
            }
        }
        return $all;
    }

    /**
     * Find a subject by code, optionally restricted to a level
     */
    public static function findSubject(string $code, ?string $level = null): ?array
    {
        self::load();
        $levels = $level ? [$level] : LEVELS;

        foreach ($levels as $lvl) {
            foreach (self::$config['subjects'][$lvl] ?? [] as $subject) {
                if ($subject['code'] === $code) {
                    return $subject;
                }
            }
        }

        return null;
    }

    /**
     * Get subject name from code
     */
    public static function getSubjectName(string $code): string
    {
        $subject = self::findSubject($code);
        return $subject['name'] ?? "Subject {$code}";
    }

    /**
     * Get all unique categories across levels
     */
    public static function getCategories(): array
    {
        self::load();
        $categories = [];
        foreach (LEVELS as $level) {
            foreach (self::$config['subjects'][$level] ?? [] as $subject) {
                $cat = $subject['category'] ?? 'Uncategorized';
                if (!in_array($cat, $categories)) {
                    $categories[] = $cat;
                }
            }
        }
        sort($categories);
        return $categories;
    }

    /**
     * Get entire config array
     */
    public static function all(): array
    {
        self::load();
        return self::$config;
    }
}