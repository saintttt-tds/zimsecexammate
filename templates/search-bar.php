<?php
$searchPlaceholder = $searchPlaceholder ?? 'Filter resources...';
$searchValue = $searchValue ?? '';
$filterAction = $filterAction ?? basename($_SERVER['SCRIPT_NAME']);
$hiddenFields = $hiddenFields ?? [];
?>
<div class="search-section">
    <div class="container">
        <form method="GET" action="<?= Helpers::h($filterAction) ?>" class="search-box-enhanced">
            <span class="search-icon">🔍</span>
            <input type="text" 
                   name="search" 
                   value="<?= Helpers::h($searchValue) ?>" 
                   placeholder="<?= Helpers::h($searchPlaceholder) ?>"
                   autocomplete="off">
            <?php foreach ($hiddenFields as $name => $value): ?>
                <?php if (!empty($value) && $value !== 'all'): ?>
                    <input type="hidden" name="<?= Helpers::h($name) ?>" value="<?= Helpers::h($value) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit">
                <i class="fas fa-filter"></i> Filter
            </button>
            <?php if (!empty($searchValue)): ?>
                <a href="<?= Helpers::h($filterAction) ?>" class="btn btn-outline btn-sm" style="margin-left:8px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>