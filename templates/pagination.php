<?php
/**
 * ZimsecExamMate — Pagination Component
 * 
 * Usage: Set $pagination array before including:
 * ['current' => 1, 'total' => 10, 'url_pattern' => '?page={page}']
 */

namespace Core;

$pagination = $pagination ?? [];
$current = max(1, (int) ($pagination['current'] ?? 1));
$total = max(1, (int) ($pagination['total'] ?? 1));
$urlPattern = $pagination['url_pattern'] ?? '?page={page}';

if ($total <= 1) return;

// Calculate visible pages
$maxVisible = 7;
$pages = [];
$pages[] = 1;

$start = max(2, $current - 2);
$end = min($total - 1, $current + 2);

if ($start > 2) {
    $pages[] = '...';
}

for ($i = $start; $i <= $end; $i++) {
    $pages[] = $i;
}

if ($end < $total - 1) {
    $pages[] = '...';
}

if ($total > 1) {
    $pages[] = $total;
}
?>

<nav class="pagination" aria-label="Page navigation">
    <ul class="pagination-list">
        <!-- Previous -->
        <?php if ($current > 1): ?>
            <li>
                <a href="<?= str_replace('{page}', $current - 1, $urlPattern) ?>" 
                   class="pagination-link" 
                   aria-label="Previous page">
                    <i class="fas fa-chevron-left"></i> Prev
                </a>
            </li>
        <?php else: ?>
            <li>
                <span class="pagination-link disabled">
                    <i class="fas fa-chevron-left"></i> Prev
                </span>
            </li>
        <?php endif; ?>

        <!-- Page numbers -->
        <?php foreach ($pages as $page): ?>
            <?php if ($page === '...'): ?>
                <li><span class="pagination-ellipsis">…</span></li>
            <?php elseif ($page === $current): ?>
                <li>
                    <span class="pagination-link active" aria-current="page">
                        <?= $page ?>
                    </span>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= str_replace('{page}', $page, $urlPattern) ?>" 
                       class="pagination-link">
                        <?= $page ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Next -->
        <?php if ($current < $total): ?>
            <li>
                <a href="<?= str_replace('{page}', $current + 1, $urlPattern) ?>" 
                   class="pagination-link" 
                   aria-label="Next page">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li>
                <span class="pagination-link disabled">
                    Next <i class="fas fa-chevron-right"></i>
                </span>
            </li>
        <?php endif; ?>
    </ul>
</nav>