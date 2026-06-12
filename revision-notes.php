<?php
/**
 * ZimsecExamMate — Revision Notes
 * 
 * Condensed revision notes from data/revision-notes.json
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Revision Notes - ' . SITE_NAME;
$currentPage = 'revision-notes.php';

$revisionNotes = Config::get('revision_notes', []);
$subjectsData = Config::get('subjects', []);

// Filters
$selectedLevel = Helpers::getParam('level', 'all');
$selectedSubject = Helpers::getParam('subject', 'all');

// Filter notes
$filteredNotes = array_filter($revisionNotes, function ($note) use ($selectedLevel, $selectedSubject) {
    $levelMatch = $selectedLevel === 'all' || ($note['level'] ?? '') === $selectedLevel;
    $subjectMatch = $selectedSubject === 'all' || ($note['subject'] ?? '') === $selectedSubject;
    return $levelMatch && $subjectMatch;
});

// Get unique subjects from notes
$allNoteSubjects = [];
foreach ($revisionNotes as $note) {
    $code = $note['subject'] ?? '';
    $subject = Config::findSubject($code);
    $name = $subject['name'] ?? "Subject {$code}";
    $allNoteSubjects[$code] = $name;
}
asort($allNoteSubjects);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Notes', 'url' => 'notes.php'],
    ['label' => 'Revision Notes', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Condensed Revision Notes</h1>
        <p class="level-description">
            Comprehensive yet concise notes for efficient exam preparation
        </p>
    </div>
</section>

<!-- Filters -->
<?php
$filterOptions = [
    [
        'name' => 'level',
        'label' => 'Education Level',
        'options' => array_merge(['all' => 'All Levels'], LEVEL_DISPLAY),
        'selected' => $selectedLevel,
    ],
    [
        'name' => 'subject',
        'label' => 'Subject',
        'options' => array_merge(['all' => 'All Subjects'], $allNoteSubjects),
        'selected' => $selectedSubject,
    ],
];
$filters = $filterOptions;
$filterAction = 'revision-notes.php';
include TEMPLATES_DIR . '/filter-bar.php';
?>

<!-- Revision Strategies -->
<section class="revision-tips">
    <div class="container">
        <h2>Effective Revision Strategies</h2>
        <div class="tips-grid">
            <div class="tip-item">
                <div class="tip-icon">⏰</div>
                <h4>Time Management</h4>
                <p>Allocate specific time slots for each subject</p>
            </div>
            <div class="tip-item">
                <div class="tip-icon">🔁</div>
                <h4>Active Recall</h4>
                <p>Test yourself regularly on key concepts</p>
            </div>
            <div class="tip-item">
                <div class="tip-icon">📝</div>
                <h4>Practice Questions</h4>
                <p>Apply knowledge to past paper questions</p>
            </div>
            <div class="tip-item">
                <div class="tip-icon">🎯</div>
                <h4>Focus on Weak Areas</h4>
                <p>Identify and target difficult topics</p>
            </div>
        </div>
    </div>
</section>

<!-- Notes Grid -->
<section class="notes-section">
    <div class="container">
        <?php if (empty($filteredNotes)): ?>
            <div class="no-results">
                <h3>No revision notes found</h3>
                <p>Try adjusting your filters or check back later for new resources.</p>
                <a href="revision-notes.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="notes-grid">
                <?php foreach ($filteredNotes as $note): 
                    $subject = Config::findSubject($note['subject'] ?? '');
                    $subjectName = $subject['name'] ?? 'Unknown Subject';
                    $level = $note['level'] ?? 'olevel';
                ?>
                <div class="note-card">
                    <span class="resource-type" style="background: #e3f2fd; color: #1976d2;">
                        📝 Study Notes
                    </span>
                    <h3><?= Helpers::h($note['name'] ?? 'Revision Notes') ?></h3>
                    <p class="note-description"><?= Helpers::h($note['description'] ?? '') ?></p>
                    
                    <div class="note-meta">
                        <span class="subject-badge">
                            <i class="fas fa-book"></i>
                            <?= Helpers::h($subjectName) ?>
                        </span>
                        <span class="level-badge <?= $level ?>">
                            <?= Helpers::levelDisplay($level) ?>
                        </span>
                    </div>
                    
                    <div class="note-details">
                        <span class="note-detail">
                            <i class="far fa-copy"></i>
                            <?= $note['pages'] ?? '?' ?> pages
                        </span>
                        <span class="note-detail">
                            <i class="fas fa-list"></i>
                            <?= $note['topics'] ?? '?' ?> topics
                        </span>
                        <span class="note-detail">
                            <i class="far fa-calendar"></i>
                            <?= Helpers::h($note['updated'] ?? '') ?>
                        </span>
                    </div>
                    
                    <div class="note-actions">
                        <a href="assets/revision-notes/<?= Helpers::h($note['file'] ?? '#') ?>" 
                           class="btn btn-primary" download>
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="notes.php" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Full study materials</p>
            </a>
            <a href="pastpapers.php" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Practice with real exam papers</p>
            </a>
            <a href="topical-papers.php" class="resource-card">
                <div class="resource-icon">📑</div>
                <h4>Topical Papers</h4>
                <p>Topic-specific practice</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';