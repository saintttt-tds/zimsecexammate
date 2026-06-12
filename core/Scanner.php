<?php
class Scanner
{
    public static function scanDirectory(string $path, string $level = ''): array
    {
        $files = [];
        if (!is_dir($path)) return $files;

        $items = @scandir($path);
        if ($items === false) return $files;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $path . '/' . $item;

            if (is_dir($fullPath)) {
                $subFiles = self::scanDirectory($fullPath, $level);
                $files = array_merge($files, $subFiles);
            } elseif (strtolower(pathinfo($item, PATHINFO_EXTENSION)) === 'pdf') {
                $metadata = self::getFileMetadata($fullPath, $level);
                if ($metadata) {
                    $files[] = $metadata;
                }
            }
        }

        return $files;
    }

    public static function scanByType(string $typeCode, ?string $level = null): array
    {
        $allFiles = [];
        
        $typeDir = TYPE_DIR_MAP[$typeCode] ?? null;
        if (!$typeDir) return $allFiles;

        if ($level) {
            $dir = PDFS_DIR . '/' . $typeDir . '/' . $level;
            if (is_dir($dir)) {
                $files = self::scanDirectory($dir, $level);
                foreach ($files as &$file) {
                    $file['level'] = $level;
                }
                $allFiles = array_merge($allFiles, $files);
            }
        } else {
            foreach (LEVELS as $lvl) {
                $dir = PDFS_DIR . '/' . $typeDir . '/' . $lvl;
                if (is_dir($dir)) {
                    $files = self::scanDirectory($dir, $lvl);
                    foreach ($files as &$file) {
                        $file['level'] = $lvl;
                    }
                    $allFiles = array_merge($allFiles, $files);
                }
            }
        }

        return $allFiles;
    }

    public static function scanApproved(?string $level = null): array
    {
        $allFiles = [];
        $levelsToScan = $level ? [$level] : LEVELS;

        foreach ($levelsToScan as $lvl) {
            $dir = APPROVED_DIR . '/' . $lvl;
            if (is_dir($dir)) {
                $files = self::scanDirectory($dir, $lvl);
                foreach ($files as &$file) {
                    $file['level'] = $lvl;
                }
                $allFiles = array_merge($allFiles, $files);
            }
        }

        return $allFiles;
    }

    public static function scanAllByType(string $typeCode, ?string $level = null): array
    {
        $typeFiles = self::scanByType($typeCode, $level);
        $approvedFiles = self::scanApproved($level);
        
        $matchingType = RESOURCE_TYPES[$typeCode] ?? '';
        $filteredApproved = array_filter($approvedFiles, function($file) use ($matchingType) {
            return ($file['resource_type'] ?? '') === $matchingType;
        });
        
        $allFiles = array_merge($typeFiles, $filteredApproved);
        
        if ($level) {
            $allFiles = array_filter($allFiles, function($file) use ($level) {
                return ($file['level'] ?? '') === $level;
            });
        }
        
        $unique = [];
        foreach ($allFiles as $file) {
            $hash = $file['hash'] ?? '';
            if ($hash && !isset($unique[$hash])) {
                $unique[$hash] = $file;
            }
        }
        
        return array_values($unique);
    }

    public static function countByLevel(string $level): int
    {
        $allFiles = [];
        foreach (array_keys(TYPE_DIR_MAP) as $typeCode) {
            $typeFiles = self::scanByType($typeCode, $level);
            $allFiles = array_merge($allFiles, $typeFiles);
        }
        $approvedFiles = self::scanApproved($level);
        $allFiles = array_merge($allFiles, $approvedFiles);
        
        $unique = [];
        foreach ($allFiles as $file) {
            $hash = $file['hash'] ?? '';
            if ($hash && !isset($unique[$hash])) {
                $unique[$hash] = $file;
            }
        }
        return count($unique);
    }

    public static function scanPending(?string $level = null): array
    {
        $allFiles = [];
        $levelsToScan = $level ? [$level] : LEVELS;

        foreach ($levelsToScan as $lvl) {
            $dir = PENDING_DIR . '/' . $lvl;
            if (!is_dir($dir)) continue;
            
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                if (strtolower(pathinfo($item, PATHINFO_EXTENSION)) !== 'pdf') continue;
                
                $filePath = $dir . '/' . $item;
                $hash = md5_file($filePath);
                $fileSize = filesize($filePath);
                $modified = filemtime($filePath);
                $parsed = Parser::parseFilename($item);
                
                $metadataPath = METADATA_DIR . '/' . $hash . '.json';
                $metadata = file_exists($metadataPath) ? Helpers::readJson($metadataPath) : [];
                
                $fileInfo = array_merge([
                    'filename'           => $item,
                    'hash'               => $hash,
                    'file_path'          => $filePath,
                    'file_size'          => $fileSize,
                    'file_size_formatted' => Helpers::formatFileSize($fileSize),
                    'modified'           => date('F d, Y', $modified),
                    'modified_timestamp' => $modified,
                    'level'              => $lvl,
                ], $parsed, $metadata);
                
                $allFiles[] = $fileInfo;
            }
        }

        foreach (array_keys(TYPE_DIR_MAP) as $typeCode) {
            $typeFiles = self::scanByType($typeCode, $level);
            foreach ($typeFiles as $file) {
                $hash = $file['hash'] ?? '';
                if (empty($hash)) continue;
                $metadataPath = METADATA_DIR . '/' . $hash . '.json';
                if (file_exists($metadataPath)) {
                    $meta = Helpers::readJson($metadataPath);
                    if (($meta['status'] ?? '') === 'pending') {
                        $allFiles[] = $file;
                    }
                }
            }
        }

        $unique = [];
        foreach ($allFiles as $file) {
            $hash = $file['hash'] ?? '';
            if ($hash && !isset($unique[$hash])) {
                $unique[$hash] = $file;
            }
        }

        return array_values($unique);
    }

    public static function getFileMetadata(string $filePath, string $level = ''): ?array
    {
        if (!file_exists($filePath)) return null;

        $filename = basename($filePath);
        $hash = md5_file($filePath);
        $fileSize = filesize($filePath);
        $modified = filemtime($filePath);

        $metadataPath = METADATA_DIR . '/' . $hash . '.json';
        $metadata = file_exists($metadataPath) ? Helpers::readJson($metadataPath) : [];
        $parsed = Parser::parseFilename($filename);

        return array_merge([
            'filename'           => $filename,
            'hash'               => $hash,
            'file_path'          => $filePath,
            'file_size'          => $fileSize,
            'file_size_formatted' => Helpers::formatFileSize($fileSize),
            'modified'           => date('F d, Y', $modified),
            'modified_timestamp' => $modified,
            'level'              => $level,
        ], $parsed, $metadata);
    }

    public static function getStats(): array
    {
        $cachePath = CACHE_DIR . '/stats.json';
        if (file_exists($cachePath) && (time() - filemtime($cachePath)) < CACHE_TTL) {
            return Helpers::readJson($cachePath);
        }

        // Count all files from all type directories
        $totalResources = 0;
        foreach (TYPE_DIR_MAP as $typeDir) {
            foreach (LEVELS as $lvl) {
                $dir = PDFS_DIR . '/' . $typeDir . '/' . $lvl;
                if (is_dir($dir)) {
                    $pdfs = glob($dir . '/*.pdf');
                    $totalResources += count($pdfs ?: []);
                }
            }
        }

        // Also count approved directory
        foreach (LEVELS as $lvl) {
            $dir = APPROVED_DIR . '/' . $lvl;
            if (is_dir($dir)) {
                $pdfs = glob($dir . '/*.pdf');
                $totalResources += count($pdfs ?: []);
            }
        }

        $allSubjects = Config::getAllSubjects();
        $totalSubjects = count($allSubjects);

        $totalDownloads = 0;
        if (is_dir(DOWNLOADS_DIR)) {
            $files = glob(DOWNLOADS_DIR . '/*.json');
            if ($files) {
                foreach ($files as $file) {
                    $data = Helpers::readJson($file);
                    $totalDownloads += $data['count'] ?? 0;
                }
            }
        }

        $stats = [
            'total_resources'   => $totalResources,
            'total_subjects'    => $totalSubjects,
            'total_downloads'   => $totalDownloads,
            'levels'            => count(LEVELS),
            'generated'         => date('Y-m-d H:i:s'),
        ];

        Helpers::writeJson($cachePath, $stats);
        return $stats;
    }
}