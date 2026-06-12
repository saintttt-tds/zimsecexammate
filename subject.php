<?php
/**
 * ZimsecExamMate — Single Subject View
 * 
 * Displays all resources for a specific subject.
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = $subjectName . ' ZIMSEC Past Papers & Resources - ' . Helpers::levelDisplay($subjectLevel) . ' - ' . SITE_NAME;
$pageDescription = 'Download free ' . $subjectName . ' ZIMSEC past papers, marking schemes, syllabi, and study notes for ' . Helpers::levelDisplay($subjectLevel) . '. Community-verified exam resources.';
$pageKeywords = $subjectName . ' past papers, ' . $subjectName . ' ZIMSEC, ' . $subjectName . ' marking schemes, ' . $subjectName . ' notes, ' . Helpers::levelDisplay($subjectLevel) . ' ' . $subjectName;

$code = Helpers::getParam('code', '');
$level = Helpers::getParam('level', '');

// Find the subject
$subject = Config::findSubject($code, $level ?: null);

if (!$subject) {
    http_response_code(404);
    $pageTitle = 'Subject Not Found - ' . SITE_NAME;
    ob_start();
    include __DIR__ . '/templates/error.php';
    $pageContent = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

$subjectName = $subject['name'];
$subjectLevel = $subject['level'];
$category = $subject['category'] ?? '';

$pageTitle = $subjectName . ' - ' . SITE_NAME;
$currentPage = 'subject.php';

// Get files for this subject from ALL type directories
$allFiles = [];

// Scan each type directory for this subject
$typeCodes = ['PP', 'MS', 'TP', 'NT', 'SY', 'PR'];
foreach ($typeCodes as $typeCode) {
    $typeFiles = Scanner::scanByType($typeCode, $subjectLevel);
    foreach ($typeFiles as $file) {
        if (($file['subject_code'] ?? '') === $code || ($file['subject_name'] ?? '') === $subjectName) {
            $allFiles[] = $file;
        }
    }
}

// Also scan approved directory for this subject
$approvedFiles = Scanner::scanApproved($subjectLevel);
foreach ($approvedFiles as $file) {
    if (($file['subject_code'] ?? '') === $code || ($file['subject_name'] ?? '') === $subjectName) {
        $allFiles[] = $file;
    }
}

// Remove duplicates by hash
$unique = [];
foreach ($allFiles as $file) {
    $hash = $file['hash'] ?? '';
    if ($hash && !isset($unique[$hash])) {
        $unique[$hash] = $file;
    }
}
$allFiles = array_values($unique);

// Group by type
$filesByType = [];
foreach ($allFiles as $file) {
    $type = $file['resource_type_display'] ?? 'Other';
    if (!isset($filesByType[$type])) {
        $filesByType[$type] = [];
    }
    $filesByType[$type][] = $file;
}

// Sort types alphabetically
ksort($filesByType);

$totalResources = count($allFiles);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => Helpers::levelDisplay($subjectLevel), 'url' => $subjectLevel . '.php'],
    ['label' => $subjectName, 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="subject-hero">
    <div class="container">
        <h1><?= Helpers::h($subjectName) ?></h1>
        <p class="subject-meta">
            <span class="subject-code-badge"><?= Helpers::h($code) ?></span>
            <span class="level-badge <?= $subjectLevel ?>"><?= Helpers::levelDisplay($subjectLevel) ?></span>
            <?php if ($category): ?>
                <span class="category-badge"><?= Helpers::h($category) ?></span>
            <?php endif; ?>
        </p>
        <p class="subject-total"><?= $totalResources ?> resource<?= $totalResources !== 1 ? 's' : '' ?> available</p>
    </div>
</section>

<!-- Quick Links -->
<?php if (!empty($filesByType)): ?>
<section class="subject-quick-links">
    <div class="container">
        <div class="quick-links-row">
            <?php foreach ($filesByType as $type => $files): ?>
            <a href="#<?= Helpers::slug($type) ?>" class="quick-link-chip">
                <?= Helpers::h($type) ?> (<?= count($files) ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Resources by Type -->
<section class="subject-resources">
    <div class="container">
        <?php if (empty($filesByType)): ?>
            <div class="no-results">
                <div class="empty-icon">📂</div>
                <h3>No resources yet</h3>
                <p>There are no resources for <strong><?= Helpers::h($subjectName) ?></strong> yet. Be the first to upload!</p>
                <div class="no-results-actions">
                    <a href="upload/index.php?subject_code=<?= urlencode($code) ?>&level=<?= urlencode($subjectLevel) ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Resources
                    </a>
                    <a href="<?= $subjectLevel ?>.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to <?= Helpers::levelDisplay($subjectLevel) ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($filesByType as $type => $files): ?>
            <div class="subject-type-section" id="<?= Helpers::slug($type) ?>">
                <div class="section-header">
                    <h2><?= Helpers::h($type) ?></h2>
                    <span class="papers-count"><?= count($files) ?> file<?= count($files) !== 1 ? 's' : '' ?></span>
                </div>
                
                <div class="papers-grid">
                    <?php foreach ($files as $file): 
                        $paper = $file;
                        include __DIR__ . '/templates/paper-card.php';
                    endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Upload CTA -->
<section class="upload-cta-section">
    <div class="container">
        <div class="cta-card">
            <h3>Have resources for <?= Helpers::h($subjectName) ?>?</h3>
            <p>Share past papers, notes, or syllabi with the community.</p>
            <a href="upload/index.php?subject_code=<?= urlencode($code) ?>&level=<?= urlencode($subjectLevel) ?>" 
               class="btn btn-primary">
                <i class="fas fa-upload"></i> Upload for <?= Helpers::h($subjectName) ?>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';