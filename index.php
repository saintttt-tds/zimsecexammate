<?php
require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = SITE_NAME . ' - Free ZIMSEC Past Papers & Study Resources';
$pageDescription = 'Download free ZIMSEC past papers, marking schemes, syllabi, and study notes for Grade 7, ZJC, O Level, and A Level. Community-verified exam preparation resources for Zimbabwean students.';
$pageKeywords = 'ZIMSEC past papers, ZIMSEC marking schemes, ZIMSEC syllabi, Grade 7 past papers, O Level past papers, A Level past papers, ZJC past papers, Zimbabwe exam preparation, free ZIMSEC resources, ZIMSEC study notes, ZIMSEC revision';
$canonicalUrl = SITE_URL;
$currentPage = 'index.php';

$stats = Cache::get('stats', CACHE_TTL);
if ($stats === null) {
    $stats = Scanner::getStats();
    Cache::set('stats', $stats);
}

$popularItems = Cache::get('homepage_popular', HOMEPAGE_CACHE_TTL);
if ($popularItems === null) {
    $allFiles = [];
    foreach (TYPE_DIR_MAP as $typeDir) {
        foreach (LEVELS as $lvl) {
            $dir = PDFS_DIR . '/' . $typeDir . '/' . $lvl;
            if (is_dir($dir)) {
                $allFiles = array_merge($allFiles, Scanner::scanDirectory($dir, $lvl));
            }
        }
    }
    foreach ($allFiles as &$file) {
        $dp = DOWNLOADS_DIR . '/' . $file['hash'] . '.json';
        $dd = Helpers::readJson($dp, ['count' => 0]);
        $file['downloads'] = $dd['count'];
    }
    usort($allFiles, fn($a, $b) => ($b['downloads'] ?? 0) <=> ($a['downloads'] ?? 0));
    $popularItems = array_slice($allFiles, 0, 12);
    Cache::set('homepage_popular', $popularItems);
}

ob_start();
?>

<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-inner">
        <div class="eyebrow">
            <span class="eyebrow-dot"></span>
            Zimbabwe School Examinations
        </div>
        <h1>Ace your <em>ZIMSEC</em> exams</h1>
        <p class="hero-sub">
            Past papers, marking schemes, syllabi, and notes — 
            everything for Grade 7, ZJC, O Level, and A Level. 
            <strong>Community-verified. Always free.</strong>
        </p>
        
        <div class="hero-actions">
            <a href="grade7.php" class="btn-hero-ghost">Grade 7</a>
            <a href="zjc.php" class="btn-hero-ghost">ZJC</a>
            <a href="olevel.php" class="btn-hero-primary">O Level</a>
            <a href="alevel.php" class="btn-hero-ghost">A Level</a>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-val"><?= $stats['total_resources'] ?? 0 ?></div>
                <div class="stat-label">Resources</div>
            </div>
            <div class="stat-item">
                <div class="stat-val"><?= count(LEVELS) ?></div>
                <div class="stat-label">Exam Levels</div>
            </div>
            <div class="stat-item">
                <div class="stat-val"><?= $stats['total_subjects'] ?? 0 ?></div>
                <div class="stat-label">Subjects</div>
            </div>
            <div class="stat-item">
                <div class="stat-val">Free</div>
                <div class="stat-label">Always</div>
            </div>
        </div>
    </div>
</section>

<div class="search-strip">
    <form action="search.php" method="GET" class="search-row">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" placeholder="Search subjects, papers, notes…" autocomplete="off">
        <button type="submit">Search →</button>
    </form>
</div>

<section class="quick-links-section">
    <div class="container">
        <div class="quick-links-grid">
            <a href="pastpapers.php" class="quick-link-card">
                <div class="ql-icon">📄</div>
                <h3>Past Papers</h3>
                <p>Yearly examination papers with marking schemes</p>
            </a>
            <a href="topical-papers.php" class="quick-link-card">
                <div class="ql-icon">📑</div>
                <h3>Topical Papers</h3>
                <p>Topic-specific practice questions</p>
            </a>
            <a href="notes.php" class="quick-link-card">
                <div class="ql-icon">📖</div>
                <h3>Notes & Textbooks</h3>
                <p>Comprehensive study materials</p>
            </a>
            <a href="syllabi.php" class="quick-link-card">
                <div class="ql-icon">📚</div>
                <h3>Syllabi</h3>
                <p>Official subject syllabi for all levels</p>
            </a>
            <a href="timetables.php" class="quick-link-card">
                <div class="ql-icon">📅</div>
                <h3>Timetables</h3>
                <p>Exam schedules & countdown timers</p>
            </a>
            <a href="uploadindex.php" class="quick-link-card upload-cta">
                <div class="ql-icon">⬆️</div>
                <h3>Upload Resources</h3>
                <p>Share files with the community</p>
            </a>
        </div>
    </div>
</section>

<?php if (!empty($popularItems)): ?>
<section class="popular-section">
    <div class="container">
        <div class="section-header">
            <h2>🔥 Popular Resources</h2>
            <a href="search.php" class="see-all">Browse All →</a>
        </div>
        <div class="hex-wrap">
            <?php foreach (array_chunk($popularItems, 3) as $row): ?>
            <div class="hex-row">
                <?php foreach ($row as $item): ?>
                <a href="download/index.php?hash=<?= urlencode($item['hash'] ?? '') ?>" class="ai-hex-cell">
                    <div class="ai-hex-outer">
                        <div class="ai-hex-border"></div>
                        <div class="ai-hex-card">
                            <span class="ai-hex-subj"><?= Helpers::h($item['resource_type_display'] ?? 'Resource') ?></span>
                            <span class="ai-hex-title"><?= Helpers::h($item['subject_name'] ?? 'Unknown') ?></span>
                            <span class="ai-hex-link"><?= Helpers::h($item['year'] ?? '') ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="community-section">
    <div class="container">
        <div class="community-content">
            <h2>🤝 Powered by the Community</h2>
            <p>All resources are uploaded and verified by students, teachers, and community members. No accounts needed — just upload, verify, and download.</p>
            <div class="community-steps">
                <div class="step"><div class="step-number">1</div><h4>Upload</h4><p>Share past papers, notes, and resources</p></div>
                <div class="step-arrow">→</div>
                <div class="step"><div class="step-number">2</div><h4>Verify</h4><p>Community reviews and approves files</p></div>
                <div class="step-arrow">→</div>
                <div class="step"><div class="step-number">3</div><h4>Download</h4><p>Access verified resources for free</p></div>
            </div>
            <div class="community-actions">
                <a href="uploadindex.php" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Files</a>
                <a href="moderateindex.php" class="btn btn-outline"><i class="fas fa-check-circle"></i> Moderate Files</a>
            </div>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';