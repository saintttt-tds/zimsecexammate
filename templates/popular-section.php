<?php
/**
 * ZimsecExamMate — Popular Resources Section
 * 
 * Replaces the old AI recommendations section.
 * Shows most downloaded or recently added files.
 * 
 * Usage: Set $popularItems before including.
 */


$popularItems = $popularItems ?? [];
$sectionTitle = $sectionTitle ?? 'Popular This Week';

if (empty($popularItems)) {
    // Fallback: show a few subjects
    $allSubjects = Config::getAllSubjects();
    shuffle($allSubjects);
    $popularItems = array_slice($allSubjects, 0, 6);
    $isSubjects = true;
} else {
    $isSubjects = false;
}
?>

<section class="popular-section">
    <div class="container">
        <div class="section-header">
            <h2>
                <span class="section-icon">🔥</span>
                <?= Helpers::h($sectionTitle) ?>
            </h2>
            <a href="search.php" class="see-all">Browse All →</a>
        </div>

        <div class="popular-grid">
            <?php foreach ($popularItems as $item): ?>
                <?php if ($isSubjects): 
                    $code = $item['code'] ?? '';
                    $name = $item['name'] ?? 'Unknown';
                    $level = $item['level'] ?? 'olevel';
                    $category = $item['category'] ?? '';
                ?>
                <a href="subject.php?code=<?= urlencode($code) ?>&level=<?= urlencode($level) ?>" 
                   class="popular-card">
                    <span class="popular-icon">📚</span>
                    <h4><?= Helpers::h($name) ?></h4>
                    <span class="level-badge <?= $level ?>"><?= Helpers::levelDisplay($level) ?></span>
                </a>
                <?php else: 
                    $hash = $item['hash'] ?? '';
                    $subjectName = $item['subject_name'] ?? 'Unknown';
                    $year = $item['year'] ?? '';
                    $downloads = $item['downloads'] ?? 0;
                ?>
                <a href="download/index.php?hash=<?= urlencode($hash) ?>" 
                   class="popular-card">
                    <span class="popular-icon">📄</span>
                    <h4><?= Helpers::h($subjectName) ?></h4>
                    <p class="popular-year"><?= Helpers::h($year) ?></p>
                    <?php if ($downloads > 0): ?>
                        <span class="download-count">
                            <i class="fas fa-download"></i> <?= $downloads ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>