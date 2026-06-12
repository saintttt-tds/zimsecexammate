<?php
require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Upload Files - ' . SITE_NAME;
$currentPage = 'uploadindex.php';
$success = flashGet('upload_success');
$error = flashGet('upload_error');
$subjects = Config::getAllSubjects();

$breadcrumbs = [
    ['label' => 'Home', 'url' => '/index.php'],
    ['label' => 'Upload', 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Upload Resources</h1>
        <p class="level-description">Share past papers, notes, and study materials with the community</p>
    </div>
</section>

<section class="upload-section">
    <div class="container">
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✅</div>
                <h3>Upload Successful!</h3>
                <p><?= Helpers::h($success) ?></p>
                <a href="uploadindex.php" class="btn btn-outline">Upload Another File</a>
                <a href="moderateindex.php" class="btn btn-primary">View Moderation Queue</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error-message"><p><?= Helpers::h($error) ?></p></div>
            <?php endif; ?>
            
            <?php include __DIR__ . '/templates/upload-form.php'; ?>
        <?php endif; ?>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';
