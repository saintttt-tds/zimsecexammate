<?php
/**
 * ZimsecExamMate — Project Writing Guide
 * 
 * Detailed guides from data/writing-guides.json
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Project Writing Guide - ' . SITE_NAME;
$currentPage = 'project-writing-guide.php';

$writingGuides = Config::get('writing_guides', []);

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Projects', 'url' => 'projects.php'],
    ['label' => 'Writing Guide', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Project Writing Guide</h1>
        <p class="level-description">
            Comprehensive guides for writing excellent project reports and documentation
        </p>
    </div>
</section>

<!-- Writing Tips -->
<section class="writing-tips">
    <div class="container">
        <h2>Essential Writing Tips</h2>
        <p>Key principles for successful project writing</p>
        
        <div class="tips-grid">
            <div class="tip-card">
                <h4>Clear Structure</h4>
                <p>Organize your project with clear sections and logical flow</p>
            </div>
            <div class="tip-card">
                <h4>Proper Formatting</h4>
                <p>Use consistent formatting, headings, and citation style</p>
            </div>
            <div class="tip-card">
                <h4>Evidence-Based</h4>
                <p>Support your arguments with data and references</p>
            </div>
            <div class="tip-card">
                <h4>Concise Language</h4>
                <p>Use clear, precise language and avoid unnecessary jargon</p>
            </div>
        </div>
    </div>
</section>

<!-- Writing Guides -->
<section class="writing-section">
    <div class="container">
        <h2>Project Writing Guides</h2>
        
        <div class="writing-guides">
            <?php foreach ($writingGuides as $guide): ?>
            <div class="writing-card">
                <h3><?= Helpers::h($guide['title'] ?? '') ?></h3>
                <p class="writing-description"><?= Helpers::h($guide['description'] ?? '') ?></p>
                
                <div class="sections-list">
                    <strong>Key Sections:</strong>
                    <?php foreach ($guide['sections'] ?? [] as $section): ?>
                    <div class="section-item">📋 <?= Helpers::h($section) ?></div>
                    <?php endforeach; ?>
                </div>
                
                <div class="writing-actions">
                    <a href="assets/project-guides/<?= Helpers::h($guide['file'] ?? '#') ?>" 
                       class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download Guide
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($writingGuides)): ?>
            <div class="no-results">
                <h3>Guides Coming Soon</h3>
                <p>We're working on comprehensive project writing guides. Check back soon!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="resources-section">
    <div class="container">
        <h3>More Resources</h3>
        <div class="resources-grid">
            <a href="projects.php" class="resource-card">
                <div class="resource-icon">📁</div>
                <h4>All Projects</h4>
                <p>Browse all project resources</p>
            </a>
            <a href="notes.php" class="resource-card">
                <div class="resource-icon">📖</div>
                <h4>Notes & Textbooks</h4>
                <p>Comprehensive study materials</p>
            </a>
            <a href="pastpapers.php" class="resource-card">
                <div class="resource-icon">📄</div>
                <h4>Past Papers</h4>
                <p>Practice with real exam papers</p>
            </a>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';