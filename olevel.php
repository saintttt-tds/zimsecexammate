<?php
/**
 * ZimsecExamMate — O Level Subject Listing
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'O Level ZIMSEC Past Papers & Resources - ' . SITE_NAME;
$pageDescription = 'Download free O Level ZIMSEC past papers, marking schemes, syllabi, and study notes. Mathematics, English, Biology, Chemistry, Physics, History, Geography and more.';
$pageKeywords = 'O Level past papers, O Level ZIMSEC, Form 4 exams, O Level Mathematics, O Level English, O Level Biology, O Level Chemistry, O Level Physics, O Level History, O Level Geography';

$level = 'olevel';
$pageTitle = 'O Level - ' . SITE_NAME;
$currentPage = 'olevel.php';

$subjects = Config::getSubjects($level);

$categories = [];
foreach ($subjects as $subject) {
    $cat = $subject['category'] ?? 'Other';
    $categories[$cat][] = $subject;
}

$subjectFilter = Helpers::getParam('subject', '');
$categoryFilter = Helpers::getParam('category', 'all');

$totalResources = Scanner::countByLevel($level);
$subjectCount = count($subjects);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'O Level', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>O Level Resources</h1>
        <p class="level-description">
            Complete collection of O Level past papers, syllabi, and study materials
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

// Search
$searchPlaceholder = 'Search O Level subjects...';
$searchValue = $subjectFilter;
$searchAction = 'olevel.php';
$hiddenFields = ['category' => $categoryFilter];
include TEMPLATES_DIR . '/search-bar.php';

$filters = [
    [
        'name' => 'category',
        'label' => 'Category',
        'options' => array_merge(['all' => 'All Categories'], array_combine(array_keys($categories), array_keys($categories))),
        'selected' => $categoryFilter,
    ],
];
$filterAction = 'olevel.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<section class="subject-categories">
    <div class="container">
        <?php foreach ($categories as $categoryName => $categorySubjects): 
            if ($categoryFilter !== 'all' && $categoryFilter !== $categoryName) continue;
            
            if ($subjectFilter) {
                $categorySubjects = array_filter($categorySubjects, function($s) use ($subjectFilter) {
                    return stripos($s['name'], $subjectFilter) !== false || 
                           stripos($s['code'], $subjectFilter) !== false;
                });
            }
            if (empty($categorySubjects)) continue;
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
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="pastpapers.php?level=olevel" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers with marking schemes</p>
            </a>
            <a href="topical-papers.php?level=olevel" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="notes.php?level=olevel" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Comprehensive study materials</p>
            </a>
            <a href="timetables.php" class="resource-card">
                <div class="resource-icon">📅</div>
                <h4>Timetables</h4>
                <p>O Level exam schedules</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';