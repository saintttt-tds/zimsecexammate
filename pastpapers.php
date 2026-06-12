<?php
/**
 * ZimsecExamMate — Past Papers Browser
 * 
 * Displays all approved past papers with filtering and search.
 * File system driven — no database.
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Past Papers - All Levels & Subjects - ' . SITE_NAME;
$pageDescription = 'Browse and download ZIMSEC past examination papers for Grade 7, ZJC, O Level, and A Level. All subjects available with marking schemes. Free downloads.';
$pageKeywords = 'ZIMSEC past papers, past exam papers Zimbabwe, ZIMSEC question papers, O Level past papers, A Level past papers, Grade 7 past papers, exam papers download';
$currentPage = 'pastpapers.php';

//Use level
$level = $filters['level'] !== 'all' ? $filters['level'] : null;
$allPapers = Scanner::scanAllByType('PP', $level);

// Validate filters
$filters = Validator::validateFilters($_GET);

// Get files grouped by year
$allPapers = Search::allFiles([
    'level' => $filters['level'],
    'type' => 'past_paper',
    'year' => $filters['year'],
    'subject' => $filters['subject'],
]);

// Apply search if present
if (!empty($filters['search'])) {
    $allPapers = Search::search($filters['search'], [
        'level' => $filters['level'],
        'type' => 'past_paper',
        'year' => $filters['year'],
        'subject' => $filters['subject'],
    ]);
}

// Group by year
$papersByYear = [];
foreach ($allPapers as $paper) {
    $year = $paper['year'] ?? 'Unknown';
    $papersByYear[$year][] = $paper;
}
krsort($papersByYear);

// Get available years and subjects for filters
$availableYears = [];
$availableSubjects = [];
foreach (Scanner::scanApproved($filters['level'] !== 'all' ? $filters['level'] : null) as $file) {
    if (($file['resource_type'] ?? '') === 'past_paper') {
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

// Stats
$totalPapers = count($allPapers);
$totalYears = count($papersByYear);
$filteredCount = $totalPapers;

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Past Papers', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Past Examination Papers</h1>
        <p class="level-description">
            Access past papers from previous years for comprehensive exam preparation
        </p>
    </div>
</section>

<!-- Stats -->
<?php
$stats = [
    ['number' => $totalPapers, 'label' => 'Total Papers'],
    ['number' => $totalYears, 'label' => 'Years Available'],
    ['number' => $filteredCount, 'label' => 'Showing'],
    ['number' => count(LEVELS), 'label' => 'Education Levels'],
];
include TEMPLATES_DIR . '/stats-bar.php';
?>

<!-- Search -->
<?php
$searchPlaceholder = 'Search past papers by subject, year, or paper type...';
$searchValue = $filters['search'];
$searchAction = 'pastpapers.php';
$hiddenFields = ['level' => $filters['level'], 'year' => $filters['year'], 'type' => $filters['type']];
include TEMPLATES_DIR . '/search-bar.php';
?>

<!-- Filters -->
<?php
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
$filterAction = 'pastpapers.php';
$filters = $filterOptions;
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Papers -->
<section class="papers-section">
    <div class="container">
        <?php if (empty($papersByYear)): ?>
            <div class="no-results">
                <h3>No past papers found</h3>
                <p>Try adjusting your filters or browse by subject.</p>
                <a href="pastpapers.php" class="btn btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($papersByYear as $year => $papers): ?>
            <div class="paper-year-section">
                <div class="section-header">
                    <h2><?= Helpers::h($year) ?> Examination Papers</h2>
                    <span class="papers-count"><?= count($papers) ?> papers</span>
                </div>
                
                <div class="papers-grid">
                    <?php foreach ($papers as $paper): ?>
                        <?php include TEMPLATES_DIR . '/paper-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Quick Links -->
<section class="resources-section">
    <div class="container">
        <h3>Related Resources</h3>
        <div class="resources-grid">
            <a href="marking-schemes.php" class="resource-card">
                <div class="resource-icon">✓</div>
                <h4>Marking Schemes</h4>
                <p>Official marking schemes for all papers</p>
            </a>
            <a href="topical-papers.php" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="upload/index.php" class="resource-card">
                <div class="resource-icon">⬆️</div>
                <h4>Upload Papers</h4>
                <p>Share past papers with the community</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';