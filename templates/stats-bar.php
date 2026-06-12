<?php
/**
 * ZimsecExamMate — Statistics Bar
 * 
 * Usage: Set $stats array before including.
 * Each stat: ['number' => '50+', 'label' => 'Subjects']
 */


$stats = $stats ?? [];
if (empty($stats)) return;
?>
<div class="stats-bar">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-item">
        <span class="stat-number"><?= Helpers::h($stat['number']) ?></span>
        <span class="stat-label"><?= Helpers::h($stat['label']) ?></span>
    </div>
    <?php endforeach; ?>
</div>