<?php
/**
 * ZimsecExamMate — Upload Form Component
 * 
 * Drag-and-drop file upload form.
 */


$levels = LEVELS;
$resourceTypes = RESOURCE_TYPES;
$subjects = Config::getAllSubjects();
$csrfField = Security::csrfField();
?>

<div class="upload-container">
    <form action="handler.php" method="POST" enctype="multipart/form-data" id="uploadForm" class="upload-form">
        <?= $csrfField ?>

        <!-- Drag & Drop Zone -->
        <div class="upload-dropzone" id="dropZone">
            <div class="dropzone-content">
                <div class="dropzone-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Drop your PDF file here</h3>
                <p>or click to browse</p>
                <p class="dropzone-hint">Maximum file size: <?= Helpers::formatFileSize(MAX_UPLOAD_SIZE) ?></p>
            </div>
            <input type="file" 
                   name="pdf_file" 
                   id="fileInput" 
                   accept=".pdf,application/pdf" 
                   required
                   hidden>
        </div>

        <!-- Selected file info -->
        <div class="file-selected" id="fileInfo" style="display: none;">
            <i class="fas fa-file-pdf"></i>
            <span id="fileName"></span>
            <span class="file-size" id="fileSize"></span>
            <button type="button" class="btn-remove" id="removeFile" title="Remove file">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Fields -->
        <div class="upload-fields">
            <div class="form-row">
                <!-- Education Level -->
                <div class="form-group">
                    <label for="level">Education Level *</label>
                    <select name="level" id="level" required>
                        <option value="">Select Level</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level ?>"><?= Helpers::levelDisplay($level) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label for="subject_code">Subject *</label>
                    <select name="subject_code" id="subject_code" required>
                        <option value="">Select Subject</option>
                        <?php 
                        // Group subjects by level for easier selection
                        $grouped = [];
                        foreach ($subjects as $s) {
                            $grouped[$s['level']][] = $s;
                        }
                        foreach ($grouped as $lvl => $subjs): 
                        ?>
                            <optgroup label="<?= Helpers::levelDisplay($lvl) ?>">
                                <?php foreach ($subjs as $s): ?>
                                    <option value="<?= $s['code'] ?>" data-level="<?= $lvl ?>">
                                        <?= Helpers::h($s['name']) ?> (<?= $s['code'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <!-- Year -->
                <div class="form-group">
                    <label for="year">Year *</label>
                    <input type="number" 
                           name="year" 
                           id="year" 
                           min="2000" 
                           max="<?= date('Y') + 1 ?>" 
                           value="<?= date('Y') ?>" 
                           required>
                </div>

                <!-- Resource Type -->
                <div class="form-group">
                    <label for="resource_type">Resource Type *</label>
                    <select name="resource_type" id="resource_type" required>
                        <option value="">Select Type</option>
                        <?php foreach ($resourceTypes as $code => $type): ?>
                            <option value="<?= $code ?>"><?= Helpers::resourceTypeDisplay($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="form-group">
                <label for="additional_info">Additional Info (optional)</label>
                <input type="text" 
                       name="additional_info" 
                       id="additional_info" 
                       placeholder="e.g., Paper 2, Topic: Algebra"
                       maxlength="100">
                <small class="form-hint">
                    Helps others identify the file. Examples: "Paper 2", "Topic: Algebra", "Revision Guide"
                </small>
            </div>
        </div>

        <!-- Upload notice -->
        <div class="upload-notice">
            <i class="fas fa-info-circle"></i>
            <p>
                <strong>Community Verification:</strong> Uploaded files require 3 approvals from other 
                community members before becoming publicly available. Files with 3 rejections are removed. 
                Please only upload relevant ZIMSEC exam resources.
            </p>
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                <i class="fas fa-upload"></i> Upload for Review
            </button>
            <button type="reset" class="btn btn-outline" id="resetBtn">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>

        <!-- Progress -->
        <div class="upload-progress" id="uploadProgress" style="display: none;">
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <span id="progressText">Uploading...</span>
        </div>

        <!-- Error/Success Messages -->
        <div class="upload-message" id="uploadMessage" style="display: none;"></div>
    </form>
</div>

<script src="assets/js/upload.js"></script>