<?php

class Search
{
    public static function search(string $query, array $filters = []): array
    {
        $query = Validator::validateSearch($query);
        if (empty($query)) {
            return self::allFiles($filters);
        }

        $filters = self::applyAutoFilters($filters);

        $cacheKey = md5($query . serialize($filters));
        $cachePath = CACHE_DIR . '/search/' . $cacheKey . '.json';
        if (file_exists($cachePath) && (time() - filemtime($cachePath)) < SEARCH_CACHE_TTL) {
            return Helpers::readJson($cachePath);
        }

        $level = ($filters['level'] ?? 'all') !== 'all' ? $filters['level'] : null;
        $files = self::getAllFiles($level);

        $results = [];
        $queryLower = strtolower($query);
        $queryWords = array_filter(explode(' ', $queryLower), function ($w) {
            return strlen($w) >= 2;
        });

        foreach ($files as $file) {
            if (!self::passesTypeFilter($file, $filters)) continue;
            if (!self::passesSubjectFilter($file, $filters)) continue;
            if (!self::passesYearFilter($file, $filters)) continue;

            $score = self::scoreFile($file, $queryLower, $queryWords);
            if ($score > 0) {
                $file['search_score'] = $score;
                $results[] = $file;
            }
        }

        usort($results, function ($a, $b) {
            $scoreDiff = ($b['search_score'] ?? 0) - ($a['search_score'] ?? 0);
            if ($scoreDiff !== 0) return $scoreDiff;
            return ($b['year'] ?? '0') <=> ($a['year'] ?? '0');
        });

        $results = array_slice($results, 0, 200);
        Helpers::ensureDir(CACHE_DIR . '/search');
        Helpers::writeJson($cachePath, $results);
        return $results;
    }

    public static function allFiles(array $filters = []): array
    {
        $filters = self::applyAutoFilters($filters);
        $level = ($filters['level'] ?? 'all') !== 'all' ? $filters['level'] : null;
        $files = self::getAllFiles($level);

        $files = array_filter($files, function ($file) use ($filters) {
            return self::passesTypeFilter($file, $filters)
                && self::passesSubjectFilter($file, $filters)
                && self::passesYearFilter($file, $filters);
        });

        usort($files, function ($a, $b) {
            return ($b['year'] ?? '0') <=> ($a['year'] ?? '0');
        });

        return array_values($files);
    }

    private static function getAllFiles(?string $level): array
    {
        $files = [];
        $levels = $level ? [$level] : LEVELS;

        foreach (TYPE_DIR_MAP as $typeDir) {
            foreach ($levels as $lvl) {
                $dir = PDFS_DIR . '/' . $typeDir . '/' . $lvl;
                if (is_dir($dir)) {
                    $files = array_merge($files, Scanner::scanDirectory($dir, $lvl));
                }
            }
        }

        foreach ($levels as $lvl) {
            $dir = APPROVED_DIR . '/' . $lvl;
            if (is_dir($dir)) {
                $files = array_merge($files, Scanner::scanDirectory($dir, $lvl));
            }
        }

        $unique = [];
        foreach ($files as $file) {
            $h = $file['hash'] ?? '';
            if ($h && !isset($unique[$h])) {
                $unique[$h] = $file;
            }
        }

        return array_values($unique);
    }

    private static function scoreFile(array $file, string $queryLower, array $queryWords): int
    {
        $score = 0;

        $searchableText = strtolower(implode(' ', array_filter([
            $file['filename'] ?? '',
            $file['subject_name'] ?? '',
            $file['subject_code'] ?? '',
            $file['resource_type_display'] ?? '',
            $file['paper_display'] ?? '',
            $file['subtype_display'] ?? '',
            $file['year'] ?? '',
            $file['level'] ?? '',
            $file['additional_info'] ?? '',
        ])));

        if (strpos($searchableText, $queryLower) !== false) {
            $score += 20;
        }

        foreach ($queryWords as $word) {
            if (strpos(strtolower($file['subject_name'] ?? ''), $word) !== false) $score += 10;
            if (strpos(strtolower($file['subject_code'] ?? ''), $word) !== false) $score += 8;
            if (($file['year'] ?? '') === $word) $score += 6;
            if (strpos(strtolower($file['resource_type_display'] ?? ''), $word) !== false) $score += 5;
            if (strpos(strtolower($file['paper_display'] ?? ''), $word) !== false) $score += 4;
            if (strpos(strtolower($file['subtype_display'] ?? ''), $word) !== false) $score += 4;
            if (strpos($searchableText, $word) !== false) $score += 2;
        }

        return $score;
    }

    private static function applyAutoFilters(array $filters): array
    {
        $currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');

        $autoLevel = self::detectLevel($currentPage);
        if ($autoLevel && ($filters['level'] ?? 'all') === 'all') {
            $filters['level'] = $autoLevel;
        }

        $autoType = self::detectType($currentPage);
        if ($autoType && ($filters['type'] ?? 'all') === 'all') {
            $filters['type'] = $autoType;
        }

        return $filters;
    }

    private static function detectLevel(string $page): ?string
    {
        $map = [
            'grade7.php' => 'grade7',
            'zjc.php'    => 'zjc',
            'olevel.php' => 'olevel',
            'alevel.php' => 'alevel',
        ];

        if (isset($map[$page])) {
            return $map[$page];
        }

        $urlLevel = $_GET['level'] ?? '';
        if (in_array($urlLevel, LEVELS)) {
            return $urlLevel;
        }

        return null;
    }

    private static function detectType(string $page): ?string
    {
        $map = [
            'pastpapers.php'            => 'past_paper',
            'marking-schemes.php'       => 'marking_scheme',
            'topical-papers.php'        => 'topical_paper',
            'notes.php'                 => 'notes',
            'revision-notes.php'        => 'notes',
            'syllabi.php'               => 'syllabus',
            'projects.php'              => 'project',
            'project-writing-guide.php' => 'project',
        ];

        if (isset($map[$page])) {
            return $map[$page];
        }

        $urlType = $_GET['type'] ?? '';
        $validTypes = array_unique(array_values(RESOURCE_TYPES));
        if (in_array($urlType, $validTypes)) {
            return $urlType;
        }

        return null;
    }

    private static function passesTypeFilter(array $file, array $filters): bool
    {
        if (empty($filters['type']) || $filters['type'] === 'all') {
            return true;
        }
        return ($file['resource_type'] ?? '') === $filters['type'];
    }

    private static function passesSubjectFilter(array $file, array $filters): bool
    {
        if (empty($filters['subject']) || $filters['subject'] === 'all') {
            return true;
        }
        return ($file['subject_name'] ?? '') === $filters['subject'];
    }

    private static function passesYearFilter(array $file, array $filters): bool
    {
        if (empty($filters['year']) || $filters['year'] === 'all') {
            return true;
        }
        return ($file['year'] ?? '') == $filters['year'];
    }
}