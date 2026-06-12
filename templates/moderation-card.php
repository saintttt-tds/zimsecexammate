<?php
/**
 * ZimsecExamMate — Moderation Card Component
 */

$file = $file ?? [];
if (empty($file)) return;

$hash = $file['hash'] ?? '';
$filename = $file['filename'] ?? 'Unknown';
$subjectName = $file['subject_name'] ?? 'Unknown';
$level = $file['level'] ?? 'olevel';
$year = $file['year'] ?? 'N/A';
$typeDisplay = $file['resource_type_display'] ?? 'Unknown';
$subtypeDisplay = $file['subtype_display'] ?? '';
$fileSize = $file['file_size_formatted'] ?? 'Unknown';
$uploadDate = $file['modified'] ?? 'Unknown';

$approvals = $file['approvals'] ?? 0;
$rejections = $file['rejections'] ?? 0;
$approvalsNeeded = $file['approvals_needed'] ?? VERIFICATION_THRESHOLD;
$rejectionsNeeded = $file['rejections_needed'] ?? REJECTION_THRESHOLD;
$hasVoted = $file['has_voted'] ?? null;

$approvalPercent = (VERIFICATION_THRESHOLD > 0) ? ($approvals / VERIFICATION_THRESHOLD) * 100 : 0;
$rejectionPercent = (REJECTION_THRESHOLD > 0) ? ($rejections / REJECTION_THRESHOLD) * 100 : 0;
?>

<div class="moderation-card" data-hash="<?= Helpers::h($hash) ?>">
    <div class="moderation-header">
        <span class="file-type-badge"><?= Helpers::h($typeDisplay) ?></span>
        <span class="level-badge <?= $level ?>"><?= Helpers::levelDisplay($level) ?></span>
    </div>

    <div class="moderation-body">
        <h4><?= Helpers::h($filename) ?></h4>
        <p class="moderation-subject">
            <strong><?= Helpers::h($subjectName) ?></strong> — <?= Helpers::h($year) ?>
            <?php if ($subtypeDisplay): ?>
                (<?= Helpers::h($subtypeDisplay) ?>)
            <?php endif; ?>
        </p>
        
        <div class="moderation-meta">
            <span><i class="far fa-file-pdf"></i> <?= Helpers::h($fileSize) ?></span>
            <span><i class="far fa-calendar"></i> <?= Helpers::h($uploadDate) ?></span>
        </div>
        
        <!-- Preview link -->
        <div class="moderation-meta" style="margin-top:0.5rem;">
            <a href="../download/index.php?hash=<?= urlencode($hash) ?>" target="_blank" style="font-size:0.8rem;color:#1e3c72;">
                <i class="fas fa-eye"></i> Preview File Details
            </a>
        </div>
    </div>

    <div class="vote-progress">
        <div class="vote-bar">
            <div class="vote-fill approvals" style="width: <?= $approvalPercent ?>%">
                <?= $approvals > 0 ? $approvals : '' ?>
            </div>
            <div class="vote-fill rejections" style="width: <?= $rejectionPercent ?>%">
                <?= $rejections > 0 ? $rejections : '' ?>
            </div>
        </div>
        <div class="vote-labels">
            <span><i class="fas fa-check"></i> <?= $approvals ?>/<?= VERIFICATION_THRESHOLD ?> approvals</span>
            <span><i class="fas fa-times"></i> <?= $rejections ?>/<?= REJECTION_THRESHOLD ?> rejections</span>
        </div>
    </div>

    <div class="moderation-actions">
        <?php if ($hasVoted): ?>
            <p class="already-voted">You voted to <strong><?= Helpers::h($hasVoted) ?></strong> this file.</p>
        <?php else: ?>
            <button class="btn btn-approve" onclick="castVote('<?= Helpers::h($hash) ?>', 'approve')">
                <i class="fas fa-check-circle"></i> Approve
            </button>
            <button class="btn btn-reject" onclick="castVote('<?= Helpers::h($hash) ?>', 'reject')">
                <i class="fas fa-times-circle"></i> Reject
            </button>
        <?php endif; ?>
    </div>

    <div class="vote-message" id="msg_<?= Helpers::h($hash) ?>" style="display: none;"></div>
</div>