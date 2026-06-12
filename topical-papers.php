<?php
/**
 * ZimsecExamMate — Topical Papers Browser
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Topical Papers - Practice by Topic - ' . SITE_NAME;
$pageDescription = 'Practice ZIMSEC exam questions by topic. Master individual concepts with topic-specific past paper questions for all subjects and levels.';
$pageKeywords = 'ZIMSEC topical papers, topic questions, exam practice by topic, ZIMSEC topic tests, O Level topical, A Level topical';
$currentPage = 'topical-papers.php';

$filters = Validator::validateFilters($_GET);

// Get topical papers
$allPapers = Search::allFiles([
    'level' => $filters['level'],
    'type' => 'topical_paper',
    'year' => $filters['year'],
    'subject' => $filters['subject'],
    'topic' => $filters['topic'],
]);

if (!empty($filters['search'])) {
    $allPapers = Search::search($filters['search'], [
        'level' => $filters['level'],
        'type' => 'topical_paper',
        'year' => $filters['year'],
        'subject' => $filters['subject'],
    ]);
}

// Group by subject
$papersBySubject = [];
foreach ($allPapers as $paper) {
    $subjectName = $paper['subject_name'] ?? 'Unknown';
    $papersBySubject[$subjectName][] = $paper;
}
ksort($papersBySubject);

// Sort papers within each subject by topic number
foreach ($papersBySubject as &$papers) {
    usort($papers, fn($a, $b) => ($a['paper_number'] ?? 0) <=> ($b['paper_number'] ?? 0));
}

$availableYears = [];
$availableSubjects = [];
$availableTopics = [];
foreach (Scanner::scanApproved($filters['level'] !== 'all' ? $filters['level'] : null) as $file) {
    if (($file['resource_type'] ?? '') === 'topical_paper') {
        if (isset($file['year']) && is_numeric($file['year'])) {
            $availableYears[] = $file['year'];
        }
        if (isset($file['subject_name'])) {
            $availableSubjects[$file['subject_name']] = $file['subject_name'];
        }
        if (!empty($file['topic_display'])) {
            $availableTopics[$file['topic_display']] = $file['topic_display'];
        }
    }
}
$availableYears = array_unique($availableYears);
rsort($availableYears);
ksort($availableSubjects);

$totalPapers = count($allPapers);
$totalSubjects = count($papersBySubject);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Topical Papers', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Topical Examination Papers</h1>
        <p class="level-description">
            Practice with topic-specific questions to master individual concepts
        </p>
    </div>
</section>

<?php
$stats = [
    ['number' => $totalPapers, 'label' => 'Topical Papers'],
    ['number' => $totalSubjects, 'label' => 'Subjects'],
    ['number' => count($availableTopics), 'label' => 'Topics'],
    ['number' => count($allPapers), 'label' => 'Showing'],
];
include TEMPLATES_DIR . '/stats-bar.php';

$searchPlaceholder = 'Search topical papers by subject, topic, or year...';
$searchValue = $filters['search'];
$searchAction = 'topical-papers.php';
$hiddenFields = ['level' => $filters['level'], 'year' => $filters['year'], 'subject' => $filters['subject']];
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
    [
        'name' => 'year',
        'label' => 'Year',
        'options' => array_merge(['all' => 'All Years'], array_combine($availableYears, $availableYears)),
        'selected' => $filters['year'],
    ],
];
$filters = $filterOptions;
$filterAction = 'topical-papers.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Study Tips -->
<section class="tips-section">
    <div class="container">
        <h2>How to Use Topical Papers Effectively</h2>
        <div class="tips-grid">
            <div class="tip-card">
                <div class="tip-number">1</div>
                <h4>Master One Topic at a Time</h4>
                <p>Focus on a single topic until you're confident before moving to the next.</p>
            </div>
            <div class="tip-card">
                <div class="tip-number">2</div>
                <h4>Timed Practice</h4>
                <p>Set a timer when practicing to simulate exam conditions.</p>
            </div>
            <div class="tip-card">
                <div class="tip-number">3</div>
                <h4>Review Marking Schemes</h4>
                <p>Study the marking schemes to understand what examiners look for.</p>
            </div>
        </div>
    </div>
</section>

<!-- Papers -->
<section class="papers-section">
    <div class="container">
        <?php if (empty($papersBySubject)): ?>
            <div class="no-results">
                <h3>No topical papers found</h3>
                <p>Try adjusting your filters or browse by subject.</p>
                <a href="topical-papers.php" class="btn btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($papersBySubject as $subject => $papers): ?>
            <div class="paper-subject-section">
                <div class="section-header">
                    <h2><?= Helpers::h($subject) ?> — Topical Papers</h2>
                    <span class="papers-count"><?= count($papers) ?> papers</span>
                </div>
                
                <div class="papers-grid">
                    <?php foreach ($papers as $paper): 
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
                <p>Complete yearly examination papers</p>
            </a>
            <a href="marking-schemes.php" class="resource-card">
                <div class="resource-icon">✓</div>
                <h4>Marking Schemes</h4>
                <p>Official marking schemes</p>
            </a>
            <a href="syllabi.php" class="resource-card">
                <div class="resource-icon">📚</div>
                <h4>Syllabi</h4>
                <p>Official subject syllabi</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';