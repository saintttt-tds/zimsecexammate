<?php
/**
 * ZimsecExamMate — Notes & Textbooks Browser
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Study Notes & Textbooks - ' . SITE_NAME;
$pageDescription = 'Download free ZIMSEC study notes, textbooks, revision guides, and summaries for all subjects and levels. Comprehensive exam preparation materials.';
$pageKeywords = 'ZIMSEC study notes, ZIMSEC textbooks, revision guides, study materials Zimbabwe, O Level notes, A Level notes, exam revision, summarized notes';
$currentPage = 'notes.php';

$filters = Validator::validateFilters($_GET);

//Use level
$level = $filters['level'] !== 'all' ? $filters['level'] : null;
$allNotes = Scanner::scanAllByType('NT', $level);
// Get notes
$allNotes = Search::allFiles([
    'level' => $filters['level'],
    'type' => 'notes',
    'subject' => $filters['subject'],
]);

if (!empty($filters['search'])) {
    $allNotes = Search::search($filters['search'], [
        'level' => $filters['level'],
        'type' => 'notes',
        'subject' => $filters['subject'],
    ]);
}

// Sort by modification date (newest first)
usort($allNotes, fn($a, $b) => ($b['modified_timestamp'] ?? 0) <=> ($a['modified_timestamp'] ?? 0));

$availableSubjects = [];
foreach (Scanner::scanApproved($filters['level'] !== 'all' ? $filters['level'] : null) as $file) {
    if (($file['resource_type'] ?? '') === 'notes' && isset($file['subject_name'])) {
        $availableSubjects[$file['subject_name']] = $file['subject_name'];
    }
}
ksort($availableSubjects);

$totalNotes = count($allNotes);

// Note subtypes (from original design)
$notesSubtypes = [
    'notes'    => ['name' => 'Study Notes',    'icon' => '📝', 'color' => '#e3f2fd', 'text_color' => '#1976d2'],
    'textbook' => ['name' => 'Textbook',       'icon' => '📚', 'color' => '#f3e5f5', 'text_color' => '#7b1fa2'],
    'guide'    => ['name' => 'Study Guide',    'icon' => '📖', 'color' => '#e8f5e8', 'text_color' => '#2e7d32'],
    'summary'  => ['name' => 'Summary Notes',  'icon' => '📋', 'color' => '#fff3e0', 'text_color' => '#ef6c00'],
    'revision' => ['name' => 'Revision Guide', 'icon' => '🔄', 'color' => '#fce4ec', 'text_color' => '#c2185b'],
    'workbook' => ['name' => 'Workbook',       'icon' => '📒', 'color' => '#e0f2f1', 'text_color' => '#00695c'],
];

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Notes & Textbooks', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Study Materials</h1>
        <p class="level-description">
            Comprehensive notes, textbooks, and study guides for all subjects
        </p>
    </div>
</section>

<?php
$stats = [
    ['number' => $totalNotes, 'label' => 'Total Resources'],
    ['number' => count($allNotes), 'label' => 'Showing'],
    ['number' => count($notesSubtypes), 'label' => 'Resource Types'],
    ['number' => count(LEVELS), 'label' => 'Education Levels'],
];
include TEMPLATES_DIR . '/stats-bar.php';

$searchPlaceholder = 'Search notes, textbooks, or study guides...';
$searchValue = $filters['search'];
$searchAction = 'notes.php';
$hiddenFields = ['level' => $filters['level'], 'subject' => $filters['subject']];
include TEMPLATES_DIR . '/search-bar.php';

$filterOptions = [
    [
        'name' => 'level',
        'label' => 'Education Level',
        'options' => array_merge(['all' => 'All Levels'], LEVEL_DISPLAY),
        'selected' => $filters['level'],
    ],
    [
        'name' => 'subject',
        'label' => 'Subject',
        'options' => array_merge(['all' => 'All Subjects'], $availableSubjects),
        'selected' => $filters['subject'],
    ],
];
$filters = $filterOptions;
$filterAction = 'notes.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Notes Grid -->
<section class="papers-section">
    <div class="container">
        <?php if (empty($allNotes)): ?>
            <div class="no-results">
                <h3>No study materials found</h3>
                <p>Try adjusting your filters or check back later for new resources.</p>
                <a href="notes.php" class="btn btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <div class="papers-grid">
                <?php foreach ($allNotes as $note): 
                    $paper = $note;
                    include TEMPLATES_DIR . '/paper-card.php';
                endforeach; ?>
            </div>
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
                <p>Previous examination papers with solutions</p>
            </a>
            <a href="topical-papers.php" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="syllabi.php" class="resource-card">
                <div class="resource-icon">📚</div>
                <h4>Syllabi</h4>
                <p>Official subject syllabi and outlines</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';