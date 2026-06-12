<?php
/**
 * ZimsecExamMate — Paper Card Component
 */

$paper = $paper ?? [];
if (empty($paper)) return;

$isPending = ($paper['status'] ?? '') === 'pending';
$level = $paper['level'] ?? 'olevel';
$hash = $paper['hash'] ?? '';
$typeInfo = $paper['resource_type'] ?? 'past_paper';
$subjectName = $paper['subject_name'] ?? 'Unknown Subject';
$year = $paper['year'] ?? 'N/A';

$borderColors = [
    'past_paper'      => '#1976d2',
    'marking_scheme'  => '#2e7d32',
    'topical_paper'   => '#e65100',
    'notes'           => '#7b1fa2',
    'syllabus'        => '#00838f',
    'project'         => '#c62828',
];
$borderColor = $borderColors[$typeInfo] ?? '#1e3c72';
?>

<div class="paper-card" style="border-top: 4px solid <?= $borderColor ?>">
    <div class="paper-header">
        <span class="paper-type-badge <?= $typeInfo ?>">
            <?= Helpers::h($paper['resource_type_display'] ?? 'Resource') ?>
        </span>
        
        <?php if ($isPending): ?>
            <span class="status-badge pending">Pending Review</span>
        <?php else: ?>
            <span class="level-badge <?= $level ?>">
                <?= Helpers::levelDisplay($level) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="paper-content">
        <h4>
            <?= Helpers::h($subjectName) ?>
            <?php if (!empty($paper['paper_display'])): ?>
                — <?= Helpers::h($paper['paper_display']) ?>
            <?php endif; ?>
        </h4>
        
        <p class="paper-subject">
            Code: <?= Helpers::h($paper['subject_code'] ?? 'N/A') ?>
            <?php if (!empty($paper['subtype_display'])): ?>
                | <?= Helpers::h($paper['subtype_display']) ?>
            <?php endif; ?>
        </p>
        
        <p class="paper-year">Year: <?= Helpers::h($year) ?></p>

        <div class="file-meta">
            <span class="file-meta-item">
                <i class="far fa-file-pdf"></i>
                <?= Helpers::h($paper['file_size_formatted'] ?? 'Unknown') ?>
            </span>
            <span class="file-meta-item">
                <i class="far fa-calendar"></i>
                <?= Helpers::h($paper['modified'] ?? 'Unknown') ?>
            </span>
        </div>
    </div>

    <div class="paper-actions">
        <?php if ($isPending): ?>
            <span class="btn btn-disabled">
                <i class="fas fa-hourglass-half"></i> Awaiting Verification
            </span>
        <?php else: ?>
            <!-- Download Button - goes to download page which auto-starts download -->
            <a href="download/index.php?hash=<?= urlencode($hash) ?>&direct=1" 
               class="btn btn-primary">
                <i class="fas fa-download"></i> Download
            </a>
            
            <!-- Preview Button - goes to download info page (no auto-download) -->
            <a href="download/index.php?hash=<?= urlencode($hash) ?>" 
               class="btn btn-outline" 
               target="_blank">
                <i class="fas fa-eye"></i> Preview
            </a>
            
            <!-- View Button - opens PDF directly in browser -->
            <a href="download/index.php?hash=<?= urlencode($hash) ?>&view=1" 
               class="btn btn-outline" 
               target="_blank">
                <i class="fas fa-file-pdf"></i> View
            </a>
        <?php endif; ?>
    </div>

    <?php if ($isPending && isset($paper['approvals'])): ?>
    <div class="verification-progress">
        <div class="progress-bar-container">
            <div class="progress-bar approvals" style="width: <?= max(5, (($paper['approvals'] ?? 0) / VERIFICATION_THRESHOLD) * 100) ?>%"></div>
        </div>
        <span class="progress-label">
            <?= $paper['approvals'] ?? 0 ?>/<?= VERIFICATION_THRESHOLD ?> approvals needed
        </span>
    </div>
    <?php endif; ?>
</div>