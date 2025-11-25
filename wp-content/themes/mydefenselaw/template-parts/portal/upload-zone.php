<?php
/**
 * Template Part: Upload Zone
 * Drag-and-drop file upload component
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$user_id = get_current_user_id();
$user_cases = function_exists('mydefenselaw_portal_get_user_cases')
    ? mydefenselaw_portal_get_user_cases($user_id)
    : array();
?>

<form id="documentUploadForm" class="upload-form" enctype="multipart/form-data">
    <?php wp_nonce_field('upload_document', 'upload_nonce'); ?>
    <input type="hidden" name="action" value="mydefenselaw_portal_upload_document">

    <div class="form-group">
        <label for="uploadCase">
            <i class="fas fa-briefcase"></i>
            <?php esc_html_e('Select Case (Optional)', 'mydefenselaw'); ?>
        </label>
        <select name="case_id" id="uploadCase" class="form-control">
            <option value=""><?php esc_html_e('General / Not case-specific', 'mydefenselaw'); ?></option>
            <?php foreach ($user_cases as $case) : ?>
                <option value="<?php echo esc_attr($case['id']); ?>">
                    <?php echo esc_html($case['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="uploadFolder">
            <i class="fas fa-folder"></i>
            <?php esc_html_e('Folder', 'mydefenselaw'); ?>
        </label>
        <select name="folder_id" id="uploadFolder" class="form-control">
            <option value=""><?php esc_html_e('General Documents', 'mydefenselaw'); ?></option>
            <option value="court_filings"><?php esc_html_e('Court Filings', 'mydefenselaw'); ?></option>
            <option value="evidence"><?php esc_html_e('Evidence', 'mydefenselaw'); ?></option>
            <option value="correspondence"><?php esc_html_e('Correspondence', 'mydefenselaw'); ?></option>
            <option value="financial"><?php esc_html_e('Financial Documents', 'mydefenselaw'); ?></option>
            <option value="personal"><?php esc_html_e('Personal Documents', 'mydefenselaw'); ?></option>
        </select>
    </div>

    <!-- Drag & Drop Zone -->
    <div class="upload-drop-zone" id="uploadDropZone">
        <input type="file" name="documents[]" id="uploadFiles" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip" style="display: none;">

        <div class="upload-drop-content">
            <i class="fas fa-cloud-upload-alt"></i>
            <h3><?php esc_html_e('Drag & Drop Files Here', 'mydefenselaw'); ?></h3>
            <p><?php esc_html_e('or click to browse', 'mydefenselaw'); ?></p>
            <button type="button" class="btn btn-secondary" id="browseFilesBtn">
                <i class="fas fa-folder-open"></i>
                <?php esc_html_e('Browse Files', 'mydefenselaw'); ?>
            </button>
            <p class="upload-help-text">
                <?php esc_html_e('Max 10MB per file. Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP', 'mydefenselaw'); ?>
            </p>
        </div>

        <div class="upload-drop-active" style="display: none;">
            <i class="fas fa-file-import"></i>
            <h3><?php esc_html_e('Drop Files to Upload', 'mydefenselaw'); ?></h3>
        </div>
    </div>

    <!-- Selected Files List -->
    <div id="uploadFilesList" class="upload-files-list" style="display: none;"></div>

    <!-- Upload Progress -->
    <div id="uploadProgress" class="upload-progress" style="display: none;">
        <div class="progress-bar">
            <div class="progress-fill" id="uploadProgressFill"></div>
        </div>
        <p class="progress-text" id="uploadProgressText">0%</p>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-secondary" id="cancelUpload">
            <?php esc_html_e('Cancel', 'mydefenselaw'); ?>
        </button>
        <button type="submit" class="btn btn-primary" id="submitUpload" disabled>
            <i class="fas fa-upload"></i>
            <?php esc_html_e('Upload Documents', 'mydefenselaw'); ?>
        </button>
    </div>

    <div id="uploadResponse" class="form-response"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('uploadDropZone');
    const fileInput = document.getElementById('uploadFiles');
    const browseBtn = document.getElementById('browseFilesBtn');
    const filesList = document.getElementById('uploadFilesList');
    const submitBtn = document.getElementById('submitUpload');
    const uploadForm = document.getElementById('documentUploadForm');

    let selectedFiles = [];

    // Browse files button
    if (browseBtn) {
        browseBtn.addEventListener('click', () => {
            fileInput.click();
        });
    }

    // Click on drop zone to browse
    if (dropZone) {
        dropZone.addEventListener('click', (e) => {
            if (e.target === dropZone || e.target.closest('.upload-drop-content')) {
                fileInput.click();
            }
        });
    }

    // File input change
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    // Drag and drop
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-active');
                dropZone.querySelector('.upload-drop-content').style.display = 'none';
                dropZone.querySelector('.upload-drop-active').style.display = 'block';
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-active');
                dropZone.querySelector('.upload-drop-content').style.display = 'block';
                dropZone.querySelector('.upload-drop-active').style.display = 'none';
            });
        });

        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });
    }

    // Handle selected files
    function handleFiles(files) {
        selectedFiles = Array.from(files);

        if (selectedFiles.length > 0) {
            filesList.style.display = 'block';
            submitBtn.disabled = false;
            displayFiles();
        }
    }

    // Display selected files
    function displayFiles() {
        filesList.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'upload-file-item';

            const icon = getFileIcon(file.name);
            const size = formatFileSize(file.size);

            fileItem.innerHTML = `
                <div class="file-item-icon">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div class="file-item-info">
                    <h4>${file.name}</h4>
                    <p>${size}</p>
                </div>
                <button type="button" class="btn-icon btn-danger" data-index="${index}" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            `;

            filesList.appendChild(fileItem);
        });

        // Remove file buttons
        filesList.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                selectedFiles.splice(index, 1);

                if (selectedFiles.length === 0) {
                    filesList.style.display = 'none';
                    submitBtn.disabled = true;
                } else {
                    displayFiles();
                }
            });
        });
    }

    // Get file icon
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            'pdf': 'file-pdf',
            'doc': 'file-word',
            'docx': 'file-word',
            'xls': 'file-excel',
            'xlsx': 'file-excel',
            'jpg': 'file-image',
            'jpeg': 'file-image',
            'png': 'file-image',
            'gif': 'file-image',
            'zip': 'file-archive'
        };
        return icons[ext] || 'file';
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Form submission
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('action', 'mydefenselaw_portal_upload_document');
            formData.append('upload_nonce', document.querySelector('[name="upload_nonce"]').value);
            formData.append('case_id', document.getElementById('uploadCase').value);
            formData.append('folder_id', document.getElementById('uploadFolder').value);

            selectedFiles.forEach(file => {
                formData.append('documents[]', file);
            });

            // Show progress
            document.getElementById('uploadProgress').style.display = 'block';
            submitBtn.disabled = true;

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    document.getElementById('uploadProgressFill').style.width = percentComplete + '%';
                    document.getElementById('uploadProgressText').textContent = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', function() {
                const response = JSON.parse(xhr.responseText);
                const responseDiv = document.getElementById('uploadResponse');

                if (response.success) {
                    responseDiv.innerHTML = response.data.message;
                    responseDiv.className = 'form-response success';
                    uploadForm.reset();
                    selectedFiles = [];
                    filesList.style.display = 'none';
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    responseDiv.innerHTML = response.data.message || '<?php esc_html_e('Upload failed', 'mydefenselaw'); ?>';
                    responseDiv.className = 'form-response error';
                }

                document.getElementById('uploadProgress').style.display = 'none';
                submitBtn.disabled = false;
            });

            xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>');
            xhr.send(formData);
        });
    }
});
</script>
