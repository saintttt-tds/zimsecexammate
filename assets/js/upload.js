/**
 * ZimsecExamMate — Upload Page Scripts
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        var dropZone = document.getElementById('dropZone');
        var fileInput = document.getElementById('fileInput');
        var fileInfo = document.getElementById('fileInfo');
        var fileName = document.getElementById('fileName');
        var fileSize = document.getElementById('fileSize');
        var removeBtn = document.getElementById('removeFile');
        var submitBtn = document.getElementById('submitBtn');
        var resetBtn = document.getElementById('resetBtn');
        var messageDiv = document.getElementById('uploadMessage');

        if (!dropZone || !fileInput) return;

        // ─── Click to Browse ──────────────────────
        dropZone.addEventListener('click', function () {
            fileInput.click();
        });

        // ─── Drag and Drop ────────────────────────
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                handleFile(e.dataTransfer.files[0]);
            }
        });

        // ─── File Input Change ────────────────────
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                handleFile(this.files[0]);
            }
        });

        // ─── Handle Selected File ─────────────────
        function handleFile(file) {
            // Check type
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                showMessage('Only PDF files are accepted.', 'error');
                return;
            }

            // Check size (250MB)
            if (file.size > 250 * 1024 * 1024) {
                showMessage('File is too large. Maximum size is 250MB.', 'error');
                return;
            }

            // Update UI
            if (fileName) fileName.textContent = file.name;
            if (fileSize) fileSize.textContent = formatSize(file.size);
            if (fileInfo) fileInfo.style.display = 'flex';
            if (dropZone) dropZone.style.display = 'none';
            if (submitBtn) submitBtn.disabled = false;
            if (messageDiv) messageDiv.style.display = 'none';
        }

        // ─── Remove File ──────────────────────────
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                if (fileInput) fileInput.value = '';
                if (fileInfo) fileInfo.style.display = 'none';
                if (dropZone) dropZone.style.display = 'block';
                if (submitBtn) submitBtn.disabled = true;
                if (messageDiv) messageDiv.style.display = 'none';
            });
        }

        // ─── Reset Button ─────────────────────────
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                setTimeout(function () {
                    if (fileInput) fileInput.value = '';
                    if (fileInfo) fileInfo.style.display = 'none';
                    if (dropZone) dropZone.style.display = 'block';
                    if (submitBtn) submitBtn.disabled = true;
                    if (messageDiv) messageDiv.style.display = 'none';
                }, 100);
            });
        }

        // ─── Format File Size ─────────────────────
        function formatSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return bytes + ' bytes';
        }

        // ─── Show Message ─────────────────────────
        function showMessage(text, type) {
            if (!messageDiv) return;
            messageDiv.textContent = text;
            messageDiv.className = 'upload-message';
            if (type === 'error') {
                messageDiv.classList.add('error-message');
            } else {
                messageDiv.classList.add('success-message');
            }
            messageDiv.style.display = 'block';
        }

    });

})();