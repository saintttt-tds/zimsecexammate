<?php
/**
 * ZimsecExamMate — Navigation Bar Component
 * 
 * Reusable secondary navigation (e.g., for resource sub-pages).
 */


$links = $links ?? [];
$currentPage = $currentPage ?? Helpers::currentPage();
?>
<nav class="sub-nav" aria-label="Secondary navigation">
    <div class="container">
        <ul class="sub-nav-list">
            <?php foreach ($links as $link): 
                $isActive = ($currentPage === ($link['page'] ?? ''));
                $icon = $link['icon'] ?? '';
            ?>
            <li>
                <a href="<?= Helpers::h($link['url']) ?>" 
                   class="sub-nav-link <?= $isActive ? 'active' : '' ?>">
                    <?php if ($icon): ?>
                        <i class="<?= Helpers::h($icon) ?>"></i>
                    <?php endif; ?>
                    <?= Helpers::h($link['label']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>