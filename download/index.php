<?php
/**
 * ZimsecExamMate — Download Page
 * Shows file info before download. Pending files can be previewed but not downloaded.
 */

require_once __DIR__ . '/../core/App.php';
appInit();

$hash = Helpers::getParam('hash', '');
$directDownload = isset($_GET['direct']) && $_GET['direct'] === '1';
$viewMode = isset($_GET['view']) && $_GET['view'] === '1';

if (empty($hash) || !preg_match('/^[a-f0-9]{32}$/i', $hash)) {
    showError('Invalid Request', 'The file hash is missing or invalid.');
}

$fileData = findFileByHash($hash);

if (!$fileData) {
    showError('File Not Found', 'The requested file could not be found in any directory.');
}

$filePath = $fileData['file_path'] ?? '';

if (!file_exists($filePath)) {
    showError('File Missing', 'File record exists but the PDF was not found on the server.');
}

// Check file status
$isPending = false;
$pendingRemaining = 0;
$metadataPath = METADATA_DIR . '/' . $hash . '.json';

if (file_exists($metadataPath)) {
    $metadata = Helpers::readJson($metadataPath);
    
    if (($metadata['status'] ?? '') === 'rejected') {
        showError('File Rejected', 'This file was rejected by the community and is no longer available.');
    }
    
    if (($metadata['status'] ?? '') === 'pending') {
        $isPending = true;
        $pendingRemaining = VERIFICATION_THRESHOLD - ($metadata['approvals'] ?? 0);
    }
}

// Rate limit for downloads only (not previews)
if ($directDownload && !Security::checkRateLimit('download', DOWNLOAD_RATE_LIMIT, RATE_LIMIT_WINDOW)) {
    showError('Rate Limit Reached', 'Please wait before downloading more files.', 'warning');
}

// Block direct download of pending files
if ($directDownload && $isPending) {
    showError('Pending Verification', 'This file cannot be downloaded until it is approved.', 'warning');
}

$filename = $fileData['filename'] ?? 'download.pdf';
$subjectName = $fileData['subject_name'] ?? 'Unknown Subject';
$year = $fileData['year'] ?? 'N/A';
$level = $fileData['level'] ?? 'olevel';
$typeDisplay = $fileData['resource_type_display'] ?? 'Resource';
$subtypeDisplay = $fileData['subtype_display'] ?? '';
$paperDisplay = $fileData['paper_display'] ?? '';
$fileSize = $fileData['file_size_formatted'] ?? Helpers::formatFileSize(filesize($filePath));

$downloadPath = DOWNLOADS_DIR . '/' . $hash . '.json';
$downloadData = Helpers::readJson($downloadPath, ['count' => 0, 'last' => '']);
$downloadCount = $downloadData['count'];

// Serve the file directly
if ($directDownload || $viewMode) {
    if ($directDownload) {
        $downloadData['count']++;
        $downloadData['last'] = date('Y-m-d H:i:s');
        Helpers::writeJson($downloadPath, $downloadData);
        @error_log(date('[Y-m-d H:i:s]') . " Download: {$filename} - " . Helpers::clientIp() . "\n", 3, LOGS_DIR . '/downloads.log');
    }
    
    $size = filesize($filePath);
    header('Content-Type: application/pdf');
    header('Content-Disposition: ' . ($viewMode ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
    header('Content-Length: ' . $size);
    header('Accept-Ranges: bytes');
    header('Cache-Control: public, max-age=3600');
    readfile($filePath);
    exit;
}

// Build page title
$pageTitle = $subjectName;
if ($paperDisplay) $pageTitle .= ' - ' . $paperDisplay;
if ($subtypeDisplay && $subtypeDisplay !== $paperDisplay) $pageTitle .= ' (' . $subtypeDisplay . ')';
$pageTitle .= ' - ' . $year . ' | ' . SITE_NAME;

ob_start();
?>

<style>
.download-page { padding: 2rem 0; min-height: 60vh; }
.download-card { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); overflow: hidden; }
.download-card-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #fff; padding: 2.5rem 2rem; text-align: center; }
.download-card-header .file-type-icon { font-size: 3rem; margin-bottom: 0.8rem; }
.download-card-header h1 { font-size: 1.3rem; font-weight: 600; margin-bottom: 0.3rem; }
.download-card-header .header-meta { display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }
.download-card-header .header-meta span { background: rgba(255,255,255,0.15); padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; }
.download-card-body { padding: 2rem; }
.file-details { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.5rem; }
.file-detail { padding: 0.8rem 1rem; background: #f5f6fa; border-radius: 8px; }
.file-detail-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.06em; color: #888; margin-bottom: 0.15rem; font-weight: 600; }
.file-detail-value { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; word-break: break-all; }
.file-notice { border-radius: 8px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; font-size: 0.85rem; display: flex; align-items: flex-start; gap: 0.6rem; line-height: 1.5; }
.file-notice i { font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
.file-notice.approved { background: #f0f7ff; border: 1px solid #c5d5f0; border-left: 4px solid #1e3c72; color: #2a3f66; }
.file-notice.approved i { color: #1e3c72; }
.file-notice.pending { background: #fff3e0; border: 1px solid #ffcc80; border-left: 4px solid #e65100; color: #7d3c00; }
.file-notice.pending i { color: #e65100; }
.download-actions { display: flex; gap: 0.8rem; flex-wrap: wrap; }
.btn-download-lg { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.85rem 2rem; background: #1e3c72; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; flex: 1; justify-content: center; min-width: 200px; }
.btn-download-lg:hover { background: #15294f; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,60,114,0.3); color: #fff; }
.btn-outline-lg { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.85rem 1.5rem; background: transparent; color: #1e3c72; border: 2px solid #1e3c72; border-radius: 8px; font-size: 0.9rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; }
.btn-outline-lg:hover { background: #1e3c72; color: #fff; }
.btn-preview { background: #e65100; }
.btn-preview:hover { background: #bf360c; }
.download-stats { display: flex; gap: 1.5rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee; font-size: 0.78rem; color: #888; flex-wrap: wrap; }
.download-stat { display: flex; align-items: center; gap: 0.4rem; }
@media (max-width: 600px) { .file-details { grid-template-columns: 1fr; } .download-actions { flex-direction: column; } }
</style>

<section class="download-page">
    <div class="container">
        <div class="download-card">
            <div class="download-card-header">
                <div class="file-type-icon"><?= $isPending ? '⏳' : '📄' ?></div>
                <h1><?= Helpers::h($subjectName) ?></h1>
                <p style="font-size:0.95rem;opacity:0.9;">
                    <?= Helpers::h($typeDisplay) ?>
                    <?php if ($paperDisplay): ?> — <?= Helpers::h($paperDisplay) ?><?php endif; ?>
                    <?php if ($subtypeDisplay && $subtypeDisplay !== $paperDisplay): ?>(<?= Helpers::h($subtypeDisplay) ?>)<?php endif; ?>
                </p>
                <div class="header-meta">
                    <span><?= Helpers::h($year) ?></span>
                    <span><?= Helpers::levelDisplay($level) ?></span>
                    <span><?= Helpers::h($fileSize) ?></span>
                    <?php if ($isPending): ?><span style="background:rgba(255,152,0,0.3);">Pending</span><?php endif; ?>
                </div>
            </div>
            
            <div class="download-card-body">
                <div class="file-details">
                    <div class="file-detail">
                        <div class="file-detail-label">Filename</div>
                        <div class="file-detail-value" style="font-size:0.78rem;"><?= Helpers::h($filename) ?></div>
                    </div>
                    <div class="file-detail">
                        <div class="file-detail-label">File Size</div>
                        <div class="file-detail-value"><?= Helpers::h($fileSize) ?></div>
                    </div>
                    <div class="file-detail">
                        <div class="file-detail-label">Subject Code</div>
                        <div class="file-detail-value"><?= Helpers::h($fileData['subject_code'] ?? 'N/A') ?></div>
                    </div>
                    <div class="file-detail">
                        <div class="file-detail-label">Level</div>
                        <div class="file-detail-value">
                            <span class="level-badge <?= $level ?>"><?= Helpers::levelDisplay($level) ?></span>
                        </div>
                    </div>
                    <?php if ($subtypeDisplay): ?>
                    <div class="file-detail">
                        <div class="file-detail-label">Type</div>
                        <div class="file-detail-value"><?= Helpers::h($subtypeDisplay) ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="file-detail">
                        <div class="file-detail-label">Downloads</div>
                        <div class="file-detail-value"><?= number_format($downloadCount) ?></div>
                    </div>
                </div>
                
                <?php if ($isPending): ?>
                <div class="file-notice pending">
                    <i class="fas fa-hourglass-half"></i>
                    <div>
                        <strong>Pending Verification</strong><br>
                        This file is awaiting community approval. You can preview it, but downloads will be available once verified.<br>
                        <strong>Needs <?= $pendingRemaining ?> more approval(s)</strong> (<?= VERIFICATION_THRESHOLD - $pendingRemaining ?>/<?= VERIFICATION_THRESHOLD ?> received)
                    </div>
                </div>
                <?php else: ?>
                <div class="file-notice approved">
                    <i class="fas fa-check-circle"></i>
                    <div><strong>Community-Verified Resource</strong><br>This file has been approved by the community and is ready for download.</div>
                </div>
                <?php endif; ?>
                
                <div class="download-actions">
                    <?php if ($isPending): ?>
                    <a href="?hash=<?= urlencode($hash) ?>&view=1" class="btn-download-lg btn-preview" target="_blank">
                        <i class="fas fa-eye"></i> Preview File
                    </a>
                    <a href="?hash=<?= urlencode($hash) ?>&view=1" class="btn-outline-lg" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Open in New Tab
                    </a>
                    <?php else: ?>
                    <a href="?hash=<?= urlencode($hash) ?>&direct=1" class="btn-download-lg">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <a href="?hash=<?= urlencode($hash) ?>&view=1" class="btn-outline-lg" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Open in New Tab
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="download-stats">
                    <div class="download-stat">
                        <i class="fas fa-<?= $isPending ? 'hourglass-half' : 'check-circle' ?>" style="color:<?= $isPending ? '#e65100' : '#2e7d32' ?>;"></i>
                        <?= $isPending ? 'Pending Review' : 'Verified' ?>
                    </div>
                    <div class="download-stat">
                        <i class="fas fa-download"></i> <?= number_format($downloadCount) ?> downloads
                    </div>
                    <div class="download-stat">
                        <i class="fas fa-shield-alt"></i> Safe PDF
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/layout.php';

// ============================================================
// FUNCTIONS
// ============================================================

function findFileByHash(string $hash): ?array
{
    $types = ['pastpapers', 'markingschemes', 'topicalpapers', 'notesandtextbooks', 'syllabi', 'projects'];
    
    foreach ($types as $type) {
        foreach (LEVELS as $level) {
            $dir = PDFS_DIR . '/' . $type . '/' . $level;
            if (!is_dir($dir)) continue;
            $files = glob($dir . '/*.pdf');
            if (!$files) continue;
            foreach ($files as $file) {
                if (md5_file($file) === $hash) return buildFileData($file, $level);
            }
        }
    }
    
    foreach (LEVELS as $level) {
        $dir = APPROVED_DIR . '/' . $level;
        if (!is_dir($dir)) continue;
        $files = glob($dir . '/*.pdf');
        if (!$files) continue;
        foreach ($files as $file) {
            if (md5_file($file) === $hash) return buildFileData($file, $level);
        }
    }
    
    foreach (LEVELS as $level) {
        $dir = PENDING_DIR . '/' . $level;
        if (!is_dir($dir)) continue;
        $files = glob($dir . '/*.pdf');
        if (!$files) continue;
        foreach ($files as $file) {
            if (md5_file($file) === $hash) return buildFileData($file, $level);
        }
    }
    
    return null;
}

function buildFileData(string $filePath, string $level): array
{
    $filename = basename($filePath);
    $parsed = Parser::parseFilename($filename);
    $fileSize = filesize($filePath);
    $hash = md5_file($filePath);
    $metadataPath = METADATA_DIR . '/' . $hash . '.json';
    $metadata = file_exists($metadataPath) ? Helpers::readJson($metadataPath) : [];
    
    return array_merge([
        'filename'           => $filename,
        'hash'               => $hash,
        'file_path'          => $filePath,
        'resolved_path'      => $filePath,
        'file_size'          => $fileSize,
        'file_size_formatted' => Helpers::formatFileSize($fileSize),
        'level'              => $level,
        'status'             => $metadata['status'] ?? 'approved',
    ], $parsed, $metadata);
}

function showError(string $title, string $message, string $type = 'error'): void
{
    $pageTitle = $title . ' - ' . SITE_NAME;
    $icons = ['error' => '❌', 'warning' => '⚠️', 'info' => 'ℹ️'];
    $icon = $icons[$type] ?? '❌';
    $colors = [
        'error'   => ['bg' => '#ffebee', 'border' => '#c62828', 'text' => '#c62828'],
        'warning' => ['bg' => '#fff3e0', 'border' => '#e65100', 'text' => '#e65100'],
        'info'    => ['bg' => '#e3f2fd', 'border' => '#1565c0', 'text' => '#1565c0'],
    ];
    $c = $colors[$type] ?? $colors['error'];
    
    ob_start();
    ?>
    <style>
    .error-page-container { padding: 4rem 0; min-height: 60vh; display: flex; align-items: center; justify-content: center; }
    .error-card { max-width: 550px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); overflow: hidden; text-align: center; }
    .error-card-header { padding: 2.5rem 2rem 1rem; }
    .error-card-header .error-icon { font-size: 3.5rem; margin-bottom: 1rem; }
    .error-card-header h1 { font-size: 1.3rem; color: #1a1a1a; margin-bottom: 0.5rem; }
    .error-card-body { padding: 0 2rem 1.5rem; }
    .error-card-body .error-message { background: <?= $c['bg'] ?>; border: 1px solid <?= $c['border'] ?>; color: <?= $c['text'] ?>; padding: 1rem 1.2rem; border-radius: 8px; font-size: 0.9rem; line-height: 1.6; }
    .error-card-footer { padding: 1rem 2rem 2rem; display: flex; gap: 0.8rem; justify-content: center; flex-wrap: wrap; }
    .btn-err-primary { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; background: #1e3c72; color: #fff; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
    .btn-err-primary:hover { background: #15294f; color: #fff; }
    .btn-err-outline { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; background: transparent; color: #1e3c72; border: 2px solid #1e3c72; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
    .btn-err-outline:hover { background: #1e3c72; color: #fff; }
    </style>
    <section class="error-page-container"><div class="container"><div class="error-card">
        <div class="error-card-header"><div class="error-icon"><?= $icon ?></div><h1><?= Helpers::h($title) ?></h1></div>
        <div class="error-card-body"><div class="error-message"><?= Helpers::h($message) ?></div></div>
        <div class="error-card-footer">
            <a href="../index.php" class="btn-err-primary"><i class="fas fa-home"></i> Go Home</a>
            <a href="../search.php" class="btn-err-outline"><i class="fas fa-search"></i> Search</a>
            <a href="../contact.php" class="btn-err-outline"><i class="fas fa-envelope"></i> Contact</a>
        </div>
    </div></div></section>
    <?php
    $pageContent = ob_get_clean();
    include __DIR__ . '/../templates/layout.php';
    exit;
}