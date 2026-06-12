<?php
/**
 * ZimsecExamMate — Subject Card Component
 */

$subject = $subject ?? [];
if (empty($subject)) return;

$code = $subject['code'] ?? '';
$name = $subject['name'] ?? 'Unknown';
$level = $subject['level'] ?? 'olevel';
$category = $subject['category'] ?? '';
$firstLetter = mb_substr($name, 0, 1);

$levelColors = [
    'grade7' => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
    'zjc'    => ['bg' => '#fff3e0', 'text' => '#e65100'],
    'olevel' => ['bg' => '#e3f2fd', 'text' => '#1565c0'],
    'alevel' => ['bg' => '#f3e5f5', 'text' => '#7b1fa2'],
];
$colors = $levelColors[$level] ?? ['bg' => '#f5f5f5', 'text' => '#333'];
?>

<div class="subject-card" 
     onclick="window.location.href='subject.php?code=<?= urlencode($code) ?>&level=<?= urlencode($level) ?>'"
     role="button"
     tabindex="0"
     onkeydown="if(event.key==='Enter') window.location.href='subject.php?code=<?= urlencode($code) ?>&level=<?= urlencode($level) ?>'">
    
    <div class="subject-icon" style="background: <?= $colors['bg'] ?>; color: <?= $colors['text'] ?>;">
        <?= Helpers::h($firstLetter) ?>
    </div>
    
    <h4><?= Helpers::h($name) ?></h4>
    
    <p class="subject-code"><?= Helpers::h($code) ?></p>
    
    <?php if ($category): ?>
        <p class="subject-category"><?= Helpers::h($category) ?></p>
    <?php endif; ?>
    
    <span class="level-badge <?= $level ?>" style="background: <?= $colors['bg'] ?>; color: <?= $colors['text'] ?>;">
        <?= Helpers::levelDisplay($level) ?>
    </span>
</div>