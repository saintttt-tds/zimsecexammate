<?php
/**
 * ZimsecExamMate — Base HTML Layout
 */

$pageTitle = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? 'Free ZIMSEC past papers, marking schemes, syllabi, and study notes for Grade 7, ZJC, O Level, and A Level. Community-verified resources.';
$pageKeywords = $pageKeywords ?? 'ZIMSEC past exam papers, ZIMSEC grade 7 past papers, ZIMSEC O Level past papers, ZIMSEC A Level past papers, ZIMSEC marking schemes, ZIMSEC syllabi, ZIMSEC study notes, Zimbabwe exams, eLearning Zimbabwe';
$canonicalUrl = $canonicalUrl ?? null;
$noIndex = $noIndex ?? false;
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= Helpers::h($pageTitle) ?></title>
    <meta name="description" content="<?= Helpers::h($pageDescription) ?>">
    <meta name="keywords" content="<?= Helpers::h($pageKeywords) ?>">
    <meta name="robots" content="<?= $noIndex ? 'noindex, nofollow' : 'index, follow' ?>">
    <meta name="author" content="ZIMSEC ExamMate">
    <meta name="theme-color" content="#1e3c72">
    
    <?php if ($canonicalUrl): ?>
    <link rel="canonical" href="<?= Helpers::h($canonicalUrl) ?>">
    <?php endif; ?>
    
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:title" content="<?= Helpers::h($pageTitle) ?>">
    <meta property="og:description" content="<?= Helpers::h($pageDescription) ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/../favicon.ico">
    
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <style>
    <?php
    $cssFile = __DIR__ . '/../assets/css/style.css';
    if (file_exists($cssFile)) {
        echo file_get_contents($cssFile);
    }
    ?>
    </style>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <?php if (isset($extraHead)): ?>
        <?= $extraHead ?>
    <?php endif; ?>
</head>
<body>

    <?php 
    // Original disclaimer modal
    include __DIR__ . '/disclaimer-modal.php';
    
    // Header
    include __DIR__ . '/header.php';
    ?>

    <main id="main-content">
        <?= $pageContent ?? '' ?>
    </main>

    <?php 
    // Footer
    include __DIR__ . '/footer.php';
    ?>

    <script>
    <?php
    $jsFile = __DIR__ . '/../assets/js/script.js';
    if (file_exists($jsFile)) {
        echo file_get_contents($jsFile);
    }
    ?>
    </script>

</body>
</html>