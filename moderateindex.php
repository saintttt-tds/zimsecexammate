<?php
require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Moderate Files - ' . SITE_NAME;
$currentPage = 'moderateindex.php';
$queue = Moderation::getModerationQueue();
$voteMessage = flashGet('vote_message');
$voteError = flashGet('vote_error');

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Moderate', 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Moderation Queue</h1>
        <p class="level-description">Help verify community uploads. Approve good resources or reject inappropriate ones.</p>
    </div>
</section>

<section class="moderation-section">
    <div class="container">
        <div class="moderation-info">
            <h3>How Moderation Works</h3>
            <div class="info-steps">
                <div class="info-step"><span class="step-badge approve">✅</span><span><strong>Approve</strong> — File is relevant and appropriate</span></div>
                <div class="info-step"><span class="step-badge reject">❌</span><span><strong>Reject</strong> — File is irrelevant, inappropriate, or corrupted</span></div>
                <div class="info-step"><span class="step-badge threshold">3️⃣</span><span><strong>3 votes</strong> of either type determine the outcome</span></div>
            </div>
        </div>

        <?php if ($voteMessage): ?>
            <div class="success-message"><?= Helpers::h($voteMessage) ?></div>
        <?php endif; ?>
        <?php if ($voteError): ?>
            <div class="error-message"><?= Helpers::h($voteError) ?></div>
        <?php endif; ?>

        <?php if (empty($queue)): ?>
            <div class="no-results">
                <div class="empty-icon">🎉</div>
                <h3>No Files to Moderate</h3>
                <p>All pending files have been reviewed. Check back later!</p>
                <a href="uploadindex.php" class="btn btn-primary">Upload a File</a>
            </div>
        <?php else: ?>
            <div class="moderation-count">
                <strong><?= count($queue) ?></strong> file<?= count($queue) !== 1 ? 's' : '' ?> awaiting review
            </div>
            <div class="moderation-grid">
                <?php foreach ($queue as $file): ?>
                    <div class="moderation-card" data-hash="<?= Helpers::h($file['hash']) ?>">
                        <div class="moderation-header">
                            <span class="file-type-badge"><?= Helpers::h($file['resource_type_display'] ?? 'Unknown') ?></span>
                            <span class="level-badge <?= $file['level'] ?? 'olevel' ?>"><?= Helpers::levelDisplay($file['level'] ?? 'olevel') ?></span>
                        </div>
                        <div class="moderation-body">
                            <h4><?= Helpers::h($file['filename'] ?? 'Unknown') ?></h4>
                            <p class="moderation-subject">
                                <strong><?= Helpers::h($file['subject_name'] ?? 'Unknown') ?></strong> — <?= Helpers::h($file['year'] ?? 'N/A') ?>
                            </p>
                            <div class="moderation-meta">
                                <span><i class="far fa-file-pdf"></i> <?= Helpers::h($file['file_size_formatted'] ?? 'Unknown') ?></span>
                                <span><i class="far fa-calendar"></i> <?= Helpers::h($file['modified'] ?? 'Unknown') ?></span>
                            </div>
                        </div>
                        <div class="vote-progress">
                            <div class="vote-bar">
                                <div class="vote-fill approvals" style="width:<?= max(5, (($file['approvals']??0)/VERIFICATION_THRESHOLD)*100) ?>%"><?= $file['approvals']??0 ?></div>
                                <div class="vote-fill rejections" style="width:<?= max(5, (($file['rejections']??0)/REJECTION_THRESHOLD)*100) ?>%"><?= $file['rejections']??0 ?></div>
                            </div>
                            <div class="vote-labels">
                                <span><?= $file['approvals']??0 ?>/<?= VERIFICATION_THRESHOLD ?> approvals</span>
                                <span><?= $file['rejections']??0 ?>/<?= REJECTION_THRESHOLD ?> rejections</span>
                            </div>
                        </div>
                        <div class="moderation-actions">
                            <?php if (($file['has_voted'] ?? null)): ?>
                                <p class="already-voted">You voted to <strong><?= $file['has_voted'] ?></strong> this file.</p>
                            <?php else: ?>
                                <button class="btn btn-approve" onclick="castVote('<?= $file['hash'] ?>','approve')">✅ Approve</button>
                                <button class="btn btn-reject" onclick="castVote('<?= $file['hash'] ?>','reject')">❌ Reject</button>
                            <?php endif; ?>
                        </div>
                        <div class="vote-message" id="msg_<?= $file['hash'] ?>" style="display:none;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function castVote(fileHash, voteType) {
    var card = document.querySelector('[data-hash="' + fileHash + '"]');
    var msgEl = document.getElementById('msg_' + fileHash);
    
    // Use separate endpoints for approve and reject
    var url = voteType === 'approve' ? 'moderate/approve.php' : 'moderate/reject.php';
    
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ hash: fileHash })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        msgEl.style.display = 'block';
        if (data.success) {
            msgEl.className = 'vote-message success';
            msgEl.textContent = data.message || 'Vote recorded!';
            if (data.action === 'approved' || data.action === 'rejected') {
                setTimeout(function() {
                    card.style.opacity = '0';
                    card.style.transition = 'all 0.5s ease';
                    setTimeout(function() { if (card.parentNode) card.remove(); }, 500);
                }, 1500);
            } else {
                var btns = card.querySelectorAll('button');
                for (var i = 0; i < btns.length; i++) btns[i].disabled = true;
                card.querySelector('.moderation-actions').innerHTML = '<p class="already-voted">You voted to <strong>' + voteType + '</strong> this file.</p>';
            }
        } else {
            msgEl.className = 'vote-message error';
            msgEl.textContent = data.error || 'Something went wrong.';
        }
    })
    .catch(function() {
        msgEl.style.display = 'block';
        msgEl.className = 'vote-message error';
        msgEl.textContent = 'Network error.';
    });
}
</script>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';