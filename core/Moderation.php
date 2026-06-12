<?php
/**
 * ZimsecExamMate — Community Moderation System
 * 
 * File-based, anonymous vote tracking.
 * 3 approvals  → file copied to approved/
 * 3 rejections → file moved to rejected/
 */

class Moderation
{
    /**
     * Cast a vote on a pending file
     */
    public static function vote(string $fileHash, string $voteType): array
    {
        $voteType = strtolower($voteType);
        if (!in_array($voteType, ['approve', 'reject'])) {
            return ['success' => false, 'error' => 'Invalid vote type.'];
        }

        if (!Validator::validateHash($fileHash)) {
            return ['success' => false, 'error' => 'Invalid file hash.'];
        }

        $pendingFile = self::findPendingFile($fileHash);
        if (!$pendingFile) {
            return ['success' => false, 'error' => 'File not found in pending queue.'];
        }

        $fingerprint = voterFingerprint($fileHash);
        $votesPath = VOTES_DIR . '/' . $fileHash . '.json';
        $votes = Helpers::readJson($votesPath, [
            'approvals'  => [],
            'rejections' => [],
        ]);

        if (in_array($fingerprint, $votes['approvals'])) {
            return ['success' => false, 'error' => 'You have already approved this file.'];
        }
        if (in_array($fingerprint, $votes['rejections'])) {
            return ['success' => false, 'error' => 'You have already rejected this file.'];
        }

        if ($voteType === 'approve') {
            $votes['approvals'][] = $fingerprint;
        } else {
            $votes['rejections'][] = $fingerprint;
        }

        Helpers::writeJson($votesPath, $votes);

        $logMessage = date('[Y-m-d H:i:s]') . " {$voteType} on {$fileHash} from " . Helpers::clientIp() . "\n";
        @error_log($logMessage, 3, LOGS_DIR . '/moderation.log');

        $approvalCount = count($votes['approvals']);
        $rejectionCount = count($votes['rejections']);

        if ($approvalCount >= VERIFICATION_THRESHOLD) {
            self::promoteToApproved($fileHash, $pendingFile);
            return [
                'success'    => true,
                'action'     => 'approved',
                'message'    => 'File has been approved and is now publicly available.',
                'approvals'  => $approvalCount,
                'rejections' => $rejectionCount,
            ];
        }

        if ($rejectionCount >= REJECTION_THRESHOLD) {
            self::moveToRejected($fileHash, $pendingFile);
            return [
                'success'    => true,
                'action'     => 'rejected',
                'message'    => 'File has been rejected and will be removed.',
                'approvals'  => $approvalCount,
                'rejections' => $rejectionCount,
            ];
        }

        return [
            'success'             => true,
            'action'              => 'pending',
            'message'             => 'Vote recorded.',
            'approvals'           => $approvalCount,
            'rejections'          => $rejectionCount,
            'remaining_approvals' => VERIFICATION_THRESHOLD - $approvalCount,
            'remaining_rejections'=> REJECTION_THRESHOLD - $rejectionCount,
        ];
    }

    /**
     * Get vote counts for a file
     */
    public static function getVotes(string $fileHash): array
    {
        $votesPath = VOTES_DIR . '/' . $fileHash . '.json';
        $votes = Helpers::readJson($votesPath, [
            'approvals'  => [],
            'rejections' => [],
        ]);

        $approvals = count($votes['approvals']);
        $rejections = count($votes['rejections']);

        return [
            'approvals'        => $approvals,
            'rejections'       => $rejections,
            'approvals_needed' => max(0, VERIFICATION_THRESHOLD - $approvals),
            'rejections_needed'=> max(0, REJECTION_THRESHOLD - $rejections),
            'has_voted'        => self::hasVoted($fileHash),
        ];
    }

    /**
     * Check if current visitor has already voted
     */
    public static function hasVoted(string $fileHash): ?string
    {
        $fingerprint = voterFingerprint($fileHash);
        $votesPath = VOTES_DIR . '/' . $fileHash . '.json';
        $votes = Helpers::readJson($votesPath, [
            'approvals'  => [],
            'rejections' => [],
        ]);

        if (in_array($fingerprint, $votes['approvals'])) return 'approve';
        if (in_array($fingerprint, $votes['rejections'])) return 'reject';
        return null;
    }

    /**
     * Get all pending files with their vote counts
     */
    public static function getModerationQueue(): array
    {
        $pendingFiles = Scanner::scanPending();
        $queue = [];

        foreach ($pendingFiles as $file) {
            $hash = $file['hash'] ?? '';
            if (empty($hash)) continue;
            
            $votes = self::getVotes($hash);
            $queue[] = array_merge($file, $votes);
        }

        usort($queue, function ($a, $b) {
            return ($b['modified_timestamp'] ?? 0) <=> ($a['modified_timestamp'] ?? 0);
        });

        return $queue;
    }

    /**
     * Copy file to approved directory
     */
    private static function promoteToApproved(string $fileHash, string $pendingFile): void
    {
        $filename = basename($pendingFile);
        $level = self::detectLevelFromPath($pendingFile);

        $metadataPath = METADATA_DIR . '/' . $fileHash . '.json';
        $metadata = Helpers::readJson($metadataPath);

        $approvedDir = APPROVED_DIR . '/' . $level;
        Helpers::ensureDir($approvedDir);
        $destination = $approvedDir . '/' . $filename;

        if (file_exists($destination)) {
            $destination = $approvedDir . '/' . pathinfo($filename, PATHINFO_FILENAME)
                         . '_' . substr($fileHash, 0, 8) . '.pdf';
        }

        copy($pendingFile, $destination);

        $metadata['status'] = 'approved';
        $metadata['approved_date'] = date('Y-m-d H:i:s');
        $metadata['file_path'] = $destination;
        $metadata['approved_path'] = $destination;
        Helpers::writeJson($metadataPath, $metadata);

        Cache::clearAll();
    }

    /**
     * Move file to rejected directory
     */
    private static function moveToRejected(string $fileHash, string $pendingFile): void
    {
        $filename = basename($pendingFile);
        Helpers::ensureDir(REJECTED_DIR);
        $destination = REJECTED_DIR . '/' . $filename;

        if (file_exists($destination)) {
            $destination = REJECTED_DIR . '/' . pathinfo($filename, PATHINFO_FILENAME)
                         . '_rejected_' . substr($fileHash, 0, 8) . '.pdf';
        }

        rename($pendingFile, $destination);

        $metadataPath = METADATA_DIR . '/' . $fileHash . '.json';
        if (file_exists($metadataPath)) {
            $metadata = Helpers::readJson($metadataPath);
            $metadata['status'] = 'rejected';
            $metadata['rejected_date'] = date('Y-m-d H:i:s');
            $metadata['file_path'] = $destination;
            Helpers::writeJson($metadataPath, $metadata);
        }

        Cache::clearAll();
    }

    /**
     * Find a pending file by its hash
     */
    private static function findPendingFile(string $fileHash): ?string
{
    // Search all type directories
    foreach (TYPE_DIR_MAP as $typeDir) {
        foreach (LEVELS as $level) {
            $dir = PDFS_DIR . '/' . $typeDir . '/' . $level;
            if (!is_dir($dir)) continue;

            $files = glob($dir . '/*.pdf');
            if (!$files) continue;

            foreach ($files as $file) {
                if (md5_file($file) === $fileHash) {
                    return $file;
                }
            }
        }
    }

    // Search pending directory
    foreach (LEVELS as $level) {
        $dir = PENDING_DIR . '/' . $level;
        if (!is_dir($dir)) continue;

        $files = glob($dir . '/*.pdf');
        if (!$files) continue;

        foreach ($files as $file) {
            if (md5_file($file) === $fileHash) {
                return $file;
            }
        }
    }

    // Search approved directory (in case file was moved)
    foreach (LEVELS as $level) {
        $dir = APPROVED_DIR . '/' . $level;
        if (!is_dir($dir)) continue;

        $files = glob($dir . '/*.pdf');
        if (!$files) continue;

        foreach ($files as $file) {
            if (md5_file($file) === $fileHash) {
                return $file;
            }
        }
    }

    return null;
}
    /**
     * Detect level from a file path
     */
    private static function detectLevelFromPath(string $path): string
    {
        foreach (LEVELS as $level) {
            if (stripos($path, '/' . $level . '/') !== false || stripos($path, '/' . $level) !== false) {
                return $level;
            }
        }
        return 'olevel';
    }
}