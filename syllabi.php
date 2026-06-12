<?php
/**
 * ZimsecExamMate — Syllabi Browser
 * 
 * Displays syllabus availability by subject.
 * Builds from subjects.json and checks for matching files.
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Subject Syllabi - ' . SITE_NAME;
$pageDescription = 'Access official ZIMSEC subject syllabi for Grade 7, ZJC, O Level, and A Level. Know exactly what to study for your examinations.';
$pageKeywords = 'ZIMSEC syllabi, ZIMSEC syllabus, subject syllabus Zimbabwe, O Level syllabus, A Level syllabus, exam syllabus, ZIMSEC curriculum';
$currentPage = 'syllabi.php';

$filters = Validator::validateFilters($_GET);

//Use level
$level = $filters['level'] !== 'all' ? $filters['level'] : null;
$allSyllabi = Scanner::scanAllByType('SY', $level);

// Get all syllabi files
$allSyllabi = Search::allFiles([
    'level' => $filters['level'],
    'type' => 'syllabus',
]);

if (!empty($filters['search'])) {
    $allSyllabi = Search::search($filters['search'], [
        'level' => $filters['level'],
        'type' => 'syllabus',
    ]);
}

// Index syllabi by subject code for quick lookup
$syllabiIndex = [];
foreach ($allSyllabi as $s) {
    $code = $s['subject_code'] ?? '';
    if ($code) {
        $syllabiIndex[$code][] = $s;
    }
}

// Build structure from subjects data
$levelsToShow = $filters['level'] !== 'all' ? [$filters['level']] : LEVELS;
$categories = Config::getCategories();

// Filter by category
$selectedCategory = $filters['category'] !== 'all' ? $filters['category'] : null;

// Stats
$totalSubjects = count(Config::getAllSubjects());
$totalSyllabi = count($allSyllabi);
$subjectsWithSyllabi = count($syllabiIndex);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Syllabi', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Subject Syllabi</h1>
        <p class="level-description">
            Official ZIMSEC syllabi for all subjects and levels
        </p>
    </div>
</section>

<?php
$stats = [
    ['number' => $subjectsWithSyllabi, 'label' => 'Subjects with Syllabi'],
    ['number' => $totalSyllabi, 'label' => 'Available Syllabi'],
    ['number' => $totalSubjects, 'label' => 'Total Subjects'],
    ['number' => count(LEVELS), 'label' => 'Education Levels'],
];
include TEMPLATES_DIR . '/stats-bar.php';

$searchPlaceholder = 'Search syllabi by subject name or code...';
$searchValue = $filters['search'];
$searchAction = 'syllabi.php';
$hiddenFields = ['level' => $filters['level'], 'category' => $filters['category']];
include TEMPLATES_DIR . '/search-bar.php';

$filterOptions = [
    [
        'name' => 'level',
        'label' => 'Education Level',
        'options' => array_merge(['all' => 'All Levels'], LEVEL_DISPLAY),
        'selected' => $filters['level'],
    ],
    [
        'name' => 'category',
        'label' => 'Category',
        'options' => array_merge(['all' => 'All Categories'], array_combine($categories, $categories)),
        'selected' => $filters['category'],
    ],
];
$filters = $filterOptions;
$filterAction = 'syllabi.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Syllabi by Level -->
<section class="syllabus-section">
    <div class="container">
        <?php foreach ($levelsToShow as $level): 
            $levelSubjects = Config::getSubjects($level);
            if (empty($levelSubjects)) continue;
            
            // Group by category
            $grouped = [];
            foreach ($levelSubjects as $subject) {
                $cat = $subject['category'] ?? 'Other';
                if ($selectedCategory && $cat !== $selectedCategory) continue;
                $grouped[$cat][] = $subject;
            }
            
            if (empty($grouped)) continue;
        ?>
        <div class="level-section">
            <div class="level-header">
                <h2><?= Helpers::levelDisplay($level) ?> Syllabi</h2>
                <p>Official syllabi for <?= Helpers::levelDisplay($level) ?> subjects</p>
            </div>
            
            <?php foreach ($grouped as $categoryName => $subjects): 
                $syllabiCount = 0;
                foreach ($subjects as $s) {
                    if (isset($syllabiIndex[$s['code']])) $syllabiCount++;
                }
            ?>
            <div class="syllabus-category">
                <div class="category-header">
                    <h3><?= Helpers::h($categoryName) ?></h3>
                    <div class="category-stats">
                        <span class="subjects-count"><?= count($subjects) ?> subjects</span>
                        <span class="syllabi-count"><?= $syllabiCount ?> syllabi</span>
                    </div>
                </div>
                
                <div class="syllabus-list">
                    <?php foreach ($subjects as $subject): 
                        $code = $subject['code'];
                        $hasSyllabus = isset($syllabiIndex[$code]);
                        $syllabusFiles = $hasSyllabus ? $syllabiIndex[$code] : [];
                        $latestSyllabus = $hasSyllabus ? $syllabusFiles[0] : null;
                    ?>
                    <div class="syllabus-item <?= $hasSyllabus ? 'has-syllabus' : 'no-syllabus' ?>">
                        <div class="syllabus-info">
                            <h4><?= Helpers::h($subject['name']) ?></h4>
                            <p>Subject Code: <?= Helpers::h($code) ?></p>
                            
                            <?php if ($latestSyllabus): ?>
                            <div class="syllabus-meta">
                                <span><i class="far fa-calendar"></i> <?= Helpers::h($latestSyllabus['year'] ?? '') ?></span>
                                <span><i class="far fa-file-pdf"></i> <?= Helpers::h($latestSyllabus['file_size_formatted'] ?? '') ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="syllabus-actions">
                            <?php if ($hasSyllabus && $latestSyllabus): ?>
                                <a href="download/index.php?hash=<?= urlencode($latestSyllabus['hash']) ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            <?php else: ?>
                                <span class="no-syllabus-badge">Not Available</span>
                            <?php endif; ?>
                            <a href="subject.php?code=<?= urlencode($code) ?>&level=<?= $level ?>" 
                               class="btn btn-outline">
                                <i class="fas fa-book"></i> View Subject
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
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