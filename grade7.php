<?php
/**
 * ZimsecExamMate — Grade 7 Subject Listing
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Grade 7 ZIMSEC Past Papers & Resources - ' . SITE_NAME;
$pageDescription = 'Download free Grade 7 ZIMSEC past papers, marking schemes, syllabi, and study notes. Mathematics, English, Shona, Science and more. Community-verified resources.';
$pageKeywords = 'Grade 7 past papers, Grade 7 ZIMSEC, Grade 7 exam preparation, Grade 7 Mathematics, Grade 7 English, Grade 7 Science, Grade 7 Shona, primary school exams Zimbabwe';

$level = 'grade7';
$pageTitle = 'Grade 7 - ' . SITE_NAME;
$currentPage = 'grade7.php';

$subjects = Config::getSubjects($level);

// Group by category
$categories = [];
foreach ($subjects as $subject) {
    $cat = $subject['category'] ?? 'Other';
    $categories[$cat][] = $subject;
}

// Filter params
$subjectFilter = Helpers::getParam('subject', '');
$categoryFilter = Helpers::getParam('category', 'all');

// Get stats
$totalResources = Scanner::countByLevel($level);
$subjectCount = count($subjects);

// Breadcrumbs
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Grade 7', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Grade 7 Resources</h1>
        <p class="level-description">
            Comprehensive study materials and past papers for Grade 7 ZIMSEC examinations
        </p>
    </div>
</section>

<!-- Stats -->
<?php
$stats = [
    ['number' => $subjectCount, 'label' => 'Subjects'],
    ['number' => $totalResources, 'label' => 'Resources'],
    ['number' => 'Free', 'label' => 'Always'],
];
include TEMPLATES_DIR . '/stats-bar.php';
?>

<!-- Filter -->
<?php
$filters = [
    [
        'name' => 'category',
        'label' => 'Category',
        'options' => array_merge(['all' => 'All Categories'], array_combine(array_keys($categories), array_keys($categories))),
        'selected' => $categoryFilter,
    ],
];
$filterAction = 'grade7.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Subjects -->
<section class="subject-categories">
    <div class="container">
        <?php foreach ($categories as $categoryName => $categorySubjects): 
            if ($categoryFilter !== 'all' && $categoryFilter !== $categoryName) continue;
            
            // Filter by search
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

<!-- Quick Links -->
<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="pastpapers.php?level=grade7" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers</p>
            </a>
            <a href="syllabi.php?level=grade7" class="resource-card">
                <div class="resource-icon">📚</div>
                <h4>Syllabi</h4>
                <p>Official subject syllabi</p>
            </a>
            <a href="notes.php?level=grade7" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Comprehensive study materials</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';