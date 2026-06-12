<?php
/**
 * ZimsecExamMate — Reusable Filter Bar
 * 
 * Usage: Set $filters array before including.
 * Each filter: ['name' => 'level', 'label' => 'Level', 'options' => [...], 'selected' => 'all']
 */

$filters = $filters ?? [];
$currentParams = $_GET;
if (empty($filters)) return;
?>
<section class="filter-section">
    <div class="container">
        <form method="GET" action="<?= Helpers::h($filterAction ?? '') ?>" class="filter-form" id="filterForm">
            <div class="filter-grid">
                <?php foreach ($filters as $filter): 
                    $name = $filter['name'];
                    $label = $filter['label'];
                    $options = $filter['options'] ?? [];
                    $selected = $filter['selected'] ?? 'all';
                ?>
                <div class="filter-group">
                    <label for="filter_<?= Helpers::h($name) ?>"><?= Helpers::h($label) ?></label>
                    <select id="filter_<?= Helpers::h($name) ?>" 
                            name="<?= Helpers::h($name) ?>" 
                            onchange="document.getElementById('filterForm').submit()">
                        <?php foreach ($options as $value => $text): ?>
                            <option value="<?= Helpers::h($value) ?>" <?= $selected == $value ? 'selected' : '' ?>>
                                <?= Helpers::h($text) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>
                <div class="filter-group filter-actions">
                    <label>&nbsp;</label>
                    <a href="<?= Helpers::h($filterAction ?? '') ?>" class="btn btn-outline btn-sm">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>