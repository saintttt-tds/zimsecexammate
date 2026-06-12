<?php
/**
 * ZimsecExamMate — Global Search
 */
require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Search - ' . SITE_NAME;
$currentPage = 'search.php';

$query = Helpers::getParam('q', '');
$query = trim($query);
$results = [];
$resultCount = 0;
$searched = false;
$rateLimitError = '';

if (!empty($query)) {
    if (Security::checkRateLimit('search', SEARCH_RATE_LIMIT, 60)) {
        $results = Search::search($query);
        $resultCount = count($results);
        $searched = true;
    } else {
        $rateLimitError = 'Too many searches. Please wait a moment before searching again.';
    }
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Search', 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="search-hero">
    <div class="container">
        <h1>Search Resources</h1>
        <p>Find past papers, marking schemes, notes, and more</p>
        
        <form action="search.php" method="GET" class="search-box-enhanced large">
            <span class="search-icon">🔍</span>
            <input type="text" 
                   name="q" 
                   value="<?= Helpers::h($query) ?>" 
                   placeholder="Search by subject name, code, year, or topic..."
                   autocomplete="off"
                   autofocus>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
</section>

<section class="search-results">
    <div class="container">
        <?php if ($rateLimitError): ?>
            <div class="error-message">
                <p><?= Helpers::h($rateLimitError) ?></p>
            </div>
        <?php elseif ($searched): ?>
            <div class="results-header">
                <h2><?= $resultCount ?> result<?= $resultCount !== 1 ? 's' : '' ?> for "<?= Helpers::h($query) ?>"</h2>
            </div>
            
            <?php if ($resultCount > 0): ?>
                <?php
                // Group results by type
                $grouped = [];
                foreach ($results as $result) {
                    $type = $result['resource_type_display'] ?? 'Other';
                    $grouped[$type][] = $result;
                }
                ?>
                
                <?php foreach ($grouped as $type => $files): ?>
                <div class="result-group">
                    <h3><?= Helpers::h($type) ?> (<?= count($files) ?>)</h3>
                    <div class="papers-grid">
                        <?php foreach ($files as $file): 
                            $paper = $file;
                            include __DIR__ . '/templates/paper-card.php';
                        endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="empty-icon">🔍</div>
                    <h3>No results found for "<?= Helpers::h($query) ?>"</h3>
                    <p>Try different keywords or browse by category.</p>
                    <div class="no-results-actions" style="margin-top:1.5rem;">
                        <a href="pastpapers.php" class="btn btn-primary">Browse Past Papers</a>
                        <a href="notes.php" class="btn btn-outline">Browse Notes</a>
                        <a href="search.php" class="btn btn-outline">Clear Search</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="search-empty">
                <div class="empty-icon">🔍</div>
                <h3>Search for ZIMSEC Resources</h3>
                <p>Enter a subject name, code, year, or keyword above to find resources.</p>
                <div class="quick-searches" style="margin-top:1.5rem;">
                    <p style="font-size:0.85rem;color:#888;margin-bottom:0.8rem;">Try searching for:</p>
                    <a href="search.php?q=Mathematics" class="btn btn-outline btn-sm">Mathematics</a>
                    <a href="search.php?q=English" class="btn btn-outline btn-sm">English Language</a>
                    <a href="search.php?q=Biology" class="btn btn-outline btn-sm">Biology</a>
                    <a href="search.php?q=2024" class="btn btn-outline btn-sm">2024 Papers</a>
                    <a href="search.php?q=marking scheme" class="btn btn-outline btn-sm">Marking Schemes</a>
                    <a href="search.php?q=notes" class="btn btn-outline btn-sm">Study Notes</a>
                    <a href="search.php?q=Paper 2" class="btn btn-outline btn-sm">Paper 2</a>
                    <a href="search.php?q=syllabus" class="btn btn-outline btn-sm">Syllabi</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';