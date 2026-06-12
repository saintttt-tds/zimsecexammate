<?php
require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Upload Files - ' . SITE_NAME;
$currentPage = 'uploadindex.php';
$success = flashGet('upload_success');
$error = flashGet('upload_error');
$subjects = Config::getAllSubjects();

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Upload', 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Upload Resources</h1>
        <p class="level-description">Share past papers, notes, and study materials with the community</p>
    </div>
</section>

<section class="upload-section">
    <div class="container">
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✅</div>
                <h3>Upload Successful!</h3>
                <p><?= Helpers::h($success) ?></p>
                <div style="display:flex;gap:1rem;justify-content:center;margin-top:1rem;">
                    <a href="uploadindex.php" class="btn btn-outline">Upload Another File</a>
                    <a href="moderate/index.php" class="btn btn-primary">View Moderation Queue</a>
                </div>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error-message"><p><?= Helpers::h($error) ?></p></div>
            <?php endif; ?>
            
            <form action="upload/handler.php" method="POST" enctype="multipart/form-data" id="uploadForm" class="upload-form">
                <?= Security::csrfField() ?>
                
                <!-- File Drop Zone -->
                <div class="upload-dropzone" id="dropZone">
                    <div class="dropzone-content">
                        <div class="dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <h3>Drop your PDF file here</h3>
                        <p>or click to browse</p>
                        <p class="dropzone-hint">Maximum file size: 10MB</p>
                    </div>
                    <input type="file" name="pdf_file" id="fileInput" accept=".pdf,application/pdf" required hidden>
                </div>

                <!-- Selected File Info -->
                <div class="file-selected" id="fileInfo" style="display:none;">
                    <i class="fas fa-file-pdf"></i>
                    <span id="fileName"></span>
                    <span class="file-size" id="fileSize"></span>
                    <button type="button" class="btn-remove" id="removeFile"><i class="fas fa-times"></i></button>
                </div>

                <!-- Form Fields -->
                <div class="upload-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="level">Education Level *</label>
                            <select name="level" id="level" required>
                                <option value="">Select Level</option>
                                <?php foreach (LEVELS as $lvl): ?>
                                    <option value="<?= $lvl ?>"><?= Helpers::levelDisplay($lvl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subject_code">Subject *</label>
                            <select name="subject_code" id="subject_code" required>
                                <option value="">Select Subject</option>
                                <?php foreach (LEVELS as $lvl): ?>
                                    <optgroup label="<?= Helpers::levelDisplay($lvl) ?>">
                                        <?php foreach (Config::getSubjects($lvl) as $s): ?>
                                            <option value="<?= $s['code'] ?>"><?= Helpers::h($s['name']) ?> (<?= $s['code'] ?>)</option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="year">Year *</label>
                            <input type="number" name="year" id="year" min="2000" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="resource_type">Resource Type *</label>
                            <select name="resource_type" id="resource_type" required>
                                <option value="">Select Type</option>
                                <option value="PP">Past Paper</option>
                                <option value="MS">Marking Scheme</option>
                                <option value="TP">Topical Paper</option>
                                <option value="NT">Notes & Textbooks</option>
                                <option value="SY">Syllabus</option>
                                <option value="PR">Project</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subtype">Subtype (optional)</label>
                        <input type="text" name="subtype" id="subtype" placeholder="e.g., PAPER2, NOTES, TEXTBOOK, TOPIC3, GUIDE" maxlength="50">
                        <small class="form-hint">Examples: PAPER1, PAPER2, NOTES, TEXTBOOK, GUIDE, REVISION, TOPIC3, COMBINED</small>
                    </div>
                </div>

                <!-- Upload Notice -->
                <div class="upload-notice">
                    <i class="fas fa-info-circle"></i>
                    <p><strong>Community Verification:</strong> Files need 3 approvals before becoming publicly available. Files with 3 rejections are removed. Only upload relevant ZIMSEC exam resources.</p>
                </div>

                <!-- Submit -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-upload"></i> Upload for Review
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<script src="assets/js/upload.js"></script>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';