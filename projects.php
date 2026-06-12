<?php
/**
 * ZimsecExamMate — Projects Page
 * 
 * Project resources and links to writing guides.
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Projects - ' . SITE_NAME;
$currentPage = 'projects.php';

//Use level
$level = $filters['level'] !== 'all' ? $filters['level'] : null;
$allProjects = Scanner::scanAllByType('PR', $level);

// Get project-type files
$projectFiles = Search::allFiles(['type' => 'project']);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Projects', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Project Resources</h1>
        <p class="level-description">
            Project guides, templates, and examples for ZIMSEC coursework
        </p>
    </div>
</section>

<!-- Project Resources Grid -->
<section class="resources-section">
    <div class="container">
        <div class="resources-grid">
            <a href="project-writing-guide.php" class="resource-card featured">
                <div class="resource-icon">📝</div>
                <h4>Project Writing Guide</h4>
                <p>Comprehensive guides for writing excellent project reports and documentation</p>
                <span class="card-link">View Guides →</span>
            </a>
            
            <?php if (!empty($projectFiles)): ?>
                <?php foreach (array_slice($projectFiles, 0, 5) as $file): ?>
                <a href="download/index.php?hash=<?= urlencode($file['hash'] ?? '') ?>" class="resource-card">
                    <div class="resource-icon">📄</div>
                    <h4><?= Helpers::h($file['subject_name'] ?? 'Project Resource') ?></h4>
                    <p><?= Helpers::h($file['year'] ?? '') ?> — <?= Helpers::levelDisplay($file['level'] ?? 'olevel') ?></p>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <a href="uploadindex.php" class="resource-card upload-cta">
                <div class="resource-icon">⬆️</div>
                <h4>Upload Project Resources</h4>
                <p>Share your project guides and templates with the community</p>
            </a>
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="resources-section">
    <div class="container">
        <h3>Related Resources</h3>
        <div class="resources-grid">
            <a href="pastpapers.php" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Previous examination papers</p>
            </a>
            <a href="notes.php" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Comprehensive study materials</p>
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