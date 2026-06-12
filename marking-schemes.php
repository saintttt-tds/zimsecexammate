<?php
/**
 * ZimsecExamMate — Marking Schemes Browser
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Marking Schemes - ' . SITE_NAME;
$pageDescription = 'Download official ZIMSEC marking schemes for all past papers. Learn exam expectations, understand grading, and improve your answering technique.';
$pageKeywords = 'ZIMSEC marking schemes, ZIMSEC answer keys, exam marking guides, how to answer ZIMSEC questions, O Level marking schemes, A Level marking schemes';
$currentPage = 'marking-schemes.php';

$filters = Validator::validateFilters($_GET);

//Use level
$level = $filters['level'] !== 'all' ? $filters['level'] : null;
$allSchemes = Scanner::scanAllByType('MS', $level);

// Get marking schemes
$allSchemes = Search::allFiles([
    'level' => $filters['level'],
    'type' => 'marking_scheme',
    'year' => $filters['year'],
    'subject' => $filters['subject'],
]);

if (!empty($filters['search'])) {
    $allSchemes = Search::search($filters['search'], [
        'level' => $filters['level'],
        'type' => 'marking_scheme',
        'year' => $filters['year'],
        'subject' => $filters['subject'],
    ]);
}

// Group by year
$schemesByYear = [];
foreach ($allSchemes as $scheme) {
    $year = $scheme['year'] ?? 'Unknown';
    $schemesByYear[$year][] = $scheme;
}
krsort($schemesByYear);

$availableYears = [];
$availableSubjects = [];
foreach (Scanner::scanApproved($filters['level'] !== 'all' ? $filters['level'] : null) as $file) {
    if (($file['resource_type'] ?? '') === 'marking_scheme') {
        if (isset($file['year']) && is_numeric($file['year'])) {
            $availableYears[] = $file['year'];
        }
        if (isset($file['subject_name'])) {
            $availableSubjects[$file['subject_name']] = $file['subject_name'];
        }
    }
}
$availableYears = array_unique($availableYears);
rsort($availableYears);
ksort($availableSubjects);

$totalSchemes = count($allSchemes);
$totalYears = count($schemesByYear);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Marking Schemes', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Marking Schemes</h1>
        <p class="level-description">
            Official marking schemes to understand exam expectations and grading
        </p>
    </div>
</section>

<?php
$stats = [
    ['number' => $totalSchemes, 'label' => 'Total Schemes'],
    ['number' => $totalYears, 'label' => 'Years Covered'],
    ['number' => count($allSchemes), 'label' => 'Showing'],
    ['number' => count(LEVELS), 'label' => 'Education Levels'],
];
include TEMPLATES_DIR . '/stats-bar.php';

$searchPlaceholder = 'Search marking schemes by subject or year...';
$searchValue = $filters['search'];
$searchAction = 'marking-schemes.php';
$hiddenFields = ['level' => $filters['level'], 'year' => $filters['year']];
include TEMPLATES_DIR . '/search-bar.php';

$filterOptions = [
    [
        'name' => 'level',
        'label' => 'Education Level',
        'options' => array_merge(['all' => 'All Levels'], LEVEL_DISPLAY),
        'selected' => $filters['level'],
    ],
    [
        'name' => 'year',
        'label' => 'Year',
        'options' => array_merge(['all' => 'All Years'], array_combine($availableYears, $availableYears)),
        'selected' => $filters['year'],
    ],
    [
        'name' => 'subject',
        'label' => 'Subject',
        'options' => array_merge(['all' => 'All Subjects'], $availableSubjects),
        'selected' => $filters['subject'],
    ],
];
$filters = $filterOptions;
$filterAction = 'marking-schemes.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- How to Use Guide -->
<section class="guide-section">
    <div class="container">
        <h2>How to Use Marking Schemes Effectively</h2>
        <p>Maximize your learning with these proven strategies</p>
        
        <div class="guide-tips">
            <div class="guide-tip">
                <div class="guide-icon">📝</div>
                <h4>Self-Assessment</h4>
                <p>Mark your own work to identify weaknesses and track progress</p>
            </div>
            <div class="guide-tip">
                <div class="guide-icon">🎯</div>
                <h4>Understand Expectations</h4>
                <p>Learn exactly what examiners look for in model answers</p>
            </div>
            <div class="guide-tip">
                <div class="guide-icon">💡</div>
                <h4>Improve Technique</h4>
                <p>Refine your answering style, structure, and timing</p>
            </div>
        </div>
    </div>
</section>

<!-- Schemes List -->
<section class="papers-section">
    <div class="container">
        <?php if (empty($schemesByYear)): ?>
            <div class="no-results">
                <h3>No marking schemes found</h3>
                <p>Try adjusting your filters or check back for updates.</p>
                <a href="marking-schemes.php" class="btn btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($schemesByYear as $year => $schemes): ?>
            <div class="paper-year-section">
                <div class="section-header">
                    <h2><?= Helpers::h($year) ?> Marking Schemes</h2>
                    <span class="papers-count"><?= count($schemes) ?> schemes</span>
                </div>
                
                <div class="papers-grid">
                    <?php foreach ($schemes as $scheme): 
                        $paper = $scheme;
                        include TEMPLATES_DIR . '/paper-card.php';
                    endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="pastpapers.php" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers</p>
            </a>
            <a href="topical-papers.php" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="notes.php" class="resource-card">
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