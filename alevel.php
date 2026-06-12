<?php
/**
 * ZimsecExamMate — A Level Subject Listing
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'A Level ZIMSEC Past Papers & Resources - ' . SITE_NAME;
$pageDescription = 'Download free A Level ZIMSEC past papers, marking schemes, syllabi, and study notes. Pure Mathematics, Biology, Chemistry, Physics, History, Geography, Business Studies and more.';
$pageKeywords = 'A Level past papers, A Level ZIMSEC, Form 6 exams, A Level Mathematics, A Level Biology, A Level Chemistry, A Level Physics, A Level Business Studies';

$level = 'alevel';
$pageTitle = 'A Level - ' . SITE_NAME;
$currentPage = 'alevel.php';

$subjects = Config::getSubjects($level);

$categories = [];
foreach ($subjects as $subject) {
    $cat = $subject['category'] ?? 'Other';
    $categories[$cat][] = $subject;
}

$subjectFilter = Helpers::getParam('subject', '');
$categoryFilter = Helpers::getParam('category', 'all');

// Count approved resources for this level
$totalResources = Scanner::countByLevel($level);
$subjectCount = count($subjects);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'A Level', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>A Level Resources</h1>
        <p class="level-description">
            Complete collection of A Level past papers, syllabi, and study materials
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

$searchPlaceholder = 'Search A Level subjects...';
$searchValue = $subjectFilter;
$searchAction = 'alevel.php';
$hiddenFields = ['category' => $categoryFilter];
include TEMPLATES_DIR . '/search-bar.php';

$filterOptions = [
    [
        'name' => 'category',
        'label' => 'Category',
        'options' => array_merge(['all' => 'All Categories'], array_combine(array_keys($categories), array_keys($categories))),
        'selected' => $categoryFilter,
    ],
];
$filters = $filterOptions;
$filterAction = 'alevel.php';
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
                <div class="category-icon"><?= Helpers::h(mb_substr($categoryName, 0, 1)) ?></div>
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
                <h3>No subjects found</h3>
                <p>Try adjusting your filters.</p>
                <a href="alevel.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>Browse by Resource Type</h3>
        <div class="resources-grid">
            <a href="pastpapers.php?level=alevel" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers with marking schemes</p>
            </a>
            <a href="topical-papers.php?level=alevel" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="notes.php?level=alevel" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Comprehensive study materials</p>
            </a>
            <a href="syllabi.php?level=alevel" class="resource-card">
                <div class="resource-icon">📚</div>
                <h4>Syllabi</h4>
                <p>Official subject syllabi</p>
            </a>
            <a href="marking-schemes.php?level=alevel" class="resource-card">
                <div class="resource-icon">✅</div>
                <h4>Marking Schemes</h4>
                <p>Official marking schemes</p>
            </a>
            <a href="timetables.php" class="resource-card">
                <div class="resource-icon">📅</div>
                <h4>Timetables</h4>
                <p>A Level exam schedules</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';