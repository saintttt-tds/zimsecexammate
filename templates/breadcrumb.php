<?php
/**
 * ZimsecExamMate — Breadcrumb Component
 * 
 * Usage: Set $breadcrumbs array before including.
 * [
 *   ['label' => 'Home', 'url' => 'index.php'],
 *   ['label' => 'Past Papers', 'url' => null],
 * ]
 */


$breadcrumbs = $breadcrumbs ?? [];
if (empty($breadcrumbs)) return;
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <div class="container">
        <?php foreach ($breadcrumbs as $i => $crumb): 
            $isLast = ($i === count($breadcrumbs) - 1);
        ?>
            <?php if (!$isLast && isset($crumb['url'])): ?>
                <a href="<?= Helpers::h($crumb['url']) ?>"><?= Helpers::h($crumb['label']) ?></a>
                <span class="separator">›</span>
            <?php else: ?>
                <span class="current"><?= Helpers::h($crumb['label']) ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</nav>