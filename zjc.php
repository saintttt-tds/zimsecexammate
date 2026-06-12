<?php
/**
 * ZimsecExamMate — ZJC Subject Listing
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZJC ZIMSEC Past Papers & Resources - ' . SITE_NAME;
$pageDescription = 'Download free ZJC ZIMSEC past papers, marking schemes, syllabi, and study notes. Mathematics, English, Science, History, Geography and more.';
$pageKeywords = 'ZJC past papers, ZJC ZIMSEC, Form 2 exams, ZJC Mathematics, ZJC English, ZJC Science, Zimbabwe Junior Certificate';

$level = 'zjc';
$pageTitle = 'ZJC - ' . SITE_NAME;
$currentPage = 'zjc.php';

$subjects = Config::getSubjects($level);

// Group by category
$categories = [];
foreach ($subjects as $subject) {
    $cat = $subject['category'] ?? 'Other';
    $categories[$cat][] = $subject;
}

$categoryFilter = Helpers::getParam('category', 'all');
$totalResources = Scanner::countByLevel($level);
$subjectCount = count($subjects);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'ZJC', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>ZJC Resources</h1>
        <p class="level-description">
            Zimbabwe Junior Certificate examination resources and study materials
        </p>
    </div>
</section>

<?php
$stats = [
    ['number' => $subjectCount, 'label' => 'Subjects'],
    ['number' => $totalResources, 'label' => 'Resources'],
    ['number' => 'Free', 'label' => 'Always'],
];
include TEMPLATES_DIR . '/stats-bar.php';
?>

<?php
$filters = [
    [
        'name' => 'category',
        'label' => 'Category',
        'options' => array_merge(['all' => 'All Categories'], array_combine(array_keys($categories), array_keys($categories))),
        'selected' => $categoryFilter,
    ],
];
$filterAction = 'zjc.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<section class="subject-categories">
    <div class="container">
        <?php foreach ($categories as $categoryName => $categorySubjects): 
            if ($categoryFilter !== 'all' && $categoryFilter !== $categoryName) continue;
        ?>
        <div class="category-section">
            <div class="category-header">
                <div class="category-icon"><?= mb_substr($categoryName, 0, 1) ?></div>
                <h3><?= Helpers::h($categoryName) ?></h3>
            </div>
            
            <div class="subjects-grid">
                <?php foreach ($categorySubjects as $subject): 
                    $subject['level'] = $level;
                    include TEMPLATES_DIR . '/subject-card.php';
                endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($categories)): ?>
        <div class="no-results">
            <h3>Resources Coming Soon</h3>
            <p>We're building our ZJC collection. Check back soon or contribute by uploading resources.</p>
            <a href="upload/index.php" class="btn btn-primary">Upload ZJC Resources</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="pastpapers.php?level=zjc" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers</p>
            </a>
            <a href="syllabi.php?level=zjc" class="resource-card">
                <div class="resource-icon">📚</div>
                <h4>Syllabi</h4>
                <p>Official subject syllabi</p>
            </a>
            <a href="upload/index.php" class="resource-card">
                <div class="resource-icon">⬆️</div>
                <h4>Upload</h4>
                <p>Share ZJC resources</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';