<?php
/**
 * Template Name: Portal - Documents
 * Document management page with upload, preview, and organization
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Require authentication
if (function_exists('mydefenselaw_portal_require_auth')) {
    mydefenselaw_portal_require_auth();
}

// Get current client
$client = null;
if (function_exists('mydefenselaw_portal_get_current_client')) {
    $client = mydefenselaw_portal_get_current_client();
}

if (!$client) {
    wp_redirect(home_url('/portal/login/'));
    exit;
}

get_header('portal');

$user_id = get_current_user_id();

// Get user's cases for filtering
$user_cases = function_exists('mydefenselaw_portal_get_user_cases')
    ? mydefenselaw_portal_get_user_cases($user_id)
    : array();

// Get user's documents
$user_documents = function_exists('mydefenselaw_portal_get_user_documents')
    ? mydefenselaw_portal_get_user_documents($user_id)
    : array();

// Get folders/categories
$folders = function_exists('mydefenselaw_portal_get_document_folders')
    ? mydefenselaw_portal_get_document_folders($user_id)
    : array();
?>

<!-- Portal Documents Page -->
<div class="portal-content">
    <div class="container">
        <!-- Page Header -->
        <div class="portal-page-header">
            <div class="page-header-content">
                <h1><i class="fas fa-folder-open"></i> <?php esc_html_e('Documents', 'mydefenselaw'); ?></h1>
                <p><?php esc_html_e('View, upload, and manage your case documents', 'mydefenselaw'); ?></p>
            </div>
            <div class="page-header-actions">
                <button class="btn btn-primary" id="uploadDocumentBtn">
                    <i class="fas fa-upload"></i>
                    <?php esc_html_e('Upload Document', 'mydefenselaw'); ?>
                </button>
            </div>
        </div>

        <!-- Documents Layout -->
        <div class="documents-layout">
            <!-- Sidebar -->
            <aside class="documents-sidebar">
                <div class="sidebar-section">
                    <h3><?php esc_html_e('Folders', 'mydefenselaw'); ?></h3>
                    <ul class="folder-list">
                        <li class="folder-item active" data-folder="all">
                            <i class="fas fa-folder"></i>
                            <span><?php esc_html_e('All Documents', 'mydefenselaw'); ?></span>
                            <span class="folder-count"><?php echo count($user_documents); ?></span>
                        </li>
                        <?php if (!empty($folders)) : ?>
                            <?php foreach ($folders as $folder) : ?>
                                <li class="folder-item" data-folder="<?php echo esc_attr($folder['id']); ?>">
                                    <i class="fas fa-<?php echo esc_attr($folder['icon'] ?? 'folder'); ?>"></i>
                                    <span><?php echo esc_html($folder['name']); ?></span>
                                    <span class="folder-count"><?php echo esc_html($folder['count']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3><?php esc_html_e('Filter by Case', 'mydefenselaw'); ?></h3>
                    <select id="caseFilter" class="form-control">
                        <option value=""><?php esc_html_e('All Cases', 'mydefenselaw'); ?></option>
                        <?php foreach ($user_cases as $case) : ?>
                            <option value="<?php echo esc_attr($case['id']); ?>">
                                <?php echo esc_html($case['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sidebar-section">
                    <h3><?php esc_html_e('File Types', 'mydefenselaw'); ?></h3>
                    <div class="file-type-filters">
                        <label class="checkbox-label">
                            <input type="checkbox" name="file_type" value="pdf" checked>
                            <span class="checkmark"></span>
                            <i class="fas fa-file-pdf"></i> PDF
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="file_type" value="doc" checked>
                            <span class="checkmark"></span>
                            <i class="fas fa-file-word"></i> Word
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="file_type" value="img" checked>
                            <span class="checkmark"></span>
                            <i class="fas fa-file-image"></i> Images
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="file_type" value="other" checked>
                            <span class="checkmark"></span>
                            <i class="fas fa-file"></i> Other
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="documents-main">
                <!-- Toolbar -->
                <div class="documents-toolbar">
                    <div class="toolbar-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="documentSearch" placeholder="<?php esc_attr_e('Search documents...', 'mydefenselaw'); ?>">
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" aria-label="<?php esc_attr_e('Grid view', 'mydefenselaw'); ?>">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-btn" data-view="list" aria-label="<?php esc_attr_e('List view', 'mydefenselaw'); ?>">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                        <select id="sortDocuments" class="form-control">
                            <option value="date_desc"><?php esc_html_e('Date (Newest)', 'mydefenselaw'); ?></option>
                            <option value="date_asc"><?php esc_html_e('Date (Oldest)', 'mydefenselaw'); ?></option>
                            <option value="name_asc"><?php esc_html_e('Name (A-Z)', 'mydefenselaw'); ?></option>
                            <option value="name_desc"><?php esc_html_e('Name (Z-A)', 'mydefenselaw'); ?></option>
                            <option value="size_desc"><?php esc_html_e('Size (Largest)', 'mydefenselaw'); ?></option>
                            <option value="size_asc"><?php esc_html_e('Size (Smallest)', 'mydefenselaw'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Documents Grid -->
                <div class="documents-grid" id="documentsGrid">
                    <?php if (!empty($user_documents)) : ?>
                        <?php foreach ($user_documents as $doc) :
                            $file_ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));
                            $file_icon = 'file';

                            // Determine icon based on file type
                            if ($file_ext === 'pdf') {
                                $file_icon = 'file-pdf';
                            } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                $file_icon = 'file-word';
                            } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                                $file_icon = 'file-excel';
                            } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $file_icon = 'file-image';
                            } elseif ($file_ext === 'zip') {
                                $file_icon = 'file-archive';
                            }
                        ?>
                            <div class="document-card"
                                 data-doc-id="<?php echo esc_attr($doc['id']); ?>"
                                 data-case-id="<?php echo esc_attr($doc['case_id'] ?? ''); ?>"
                                 data-folder="<?php echo esc_attr($doc['folder_id'] ?? 'all'); ?>"
                                 data-type="<?php echo esc_attr($file_ext); ?>">
                                <div class="document-preview">
                                    <i class="fas fa-<?php echo esc_attr($file_icon); ?>"></i>
                                </div>
                                <div class="document-info">
                                    <h4 class="document-name"><?php echo esc_html($doc['name']); ?></h4>
                                    <div class="document-meta">
                                        <?php if (!empty($doc['case_name'])) : ?>
                                            <span class="document-case"><i class="fas fa-briefcase"></i> <?php echo esc_html($doc['case_name']); ?></span>
                                        <?php endif; ?>
                                        <span class="document-date"><i class="fas fa-calendar"></i> <?php echo esc_html($doc['date']); ?></span>
                                        <span class="document-size"><i class="fas fa-hdd"></i> <?php echo esc_html($doc['size']); ?></span>
                                    </div>
                                </div>
                                <div class="document-actions">
                                    <button class="btn-icon" data-action="preview" data-doc-id="<?php echo esc_attr($doc['id']); ?>" title="<?php esc_attr_e('Preview', 'mydefenselaw'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="<?php echo esc_url($doc['download_url']); ?>" class="btn-icon" download title="<?php esc_attr_e('Download', 'mydefenselaw'); ?>">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn-icon btn-danger" data-action="delete" data-doc-id="<?php echo esc_attr($doc['id']); ?>" title="<?php esc_attr_e('Delete', 'mydefenselaw'); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="empty-state-large">
                            <i class="fas fa-folder-open"></i>
                            <h3><?php esc_html_e('No Documents Yet', 'mydefenselaw'); ?></h3>
                            <p><?php esc_html_e('Upload your first document to get started', 'mydefenselaw'); ?></p>
                            <button class="btn btn-primary" id="uploadDocumentBtnEmpty">
                                <i class="fas fa-upload"></i>
                                <?php esc_html_e('Upload Document', 'mydefenselaw'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="portal-modal">
    <div class="portal-modal-content modal-large">
        <div class="portal-modal-header">
            <h3><i class="fas fa-upload"></i> <?php esc_html_e('Upload Document', 'mydefenselaw'); ?></h3>
            <button class="modal-close" id="closeUploadModal" aria-label="<?php esc_attr_e('Close', 'mydefenselaw'); ?>">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="portal-modal-body">
            <?php get_template_part('template-parts/portal/upload-zone'); ?>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="portal-modal">
    <div class="portal-modal-content modal-full">
        <div class="portal-modal-header">
            <h3 id="previewTitle"><?php esc_html_e('Document Preview', 'mydefenselaw'); ?></h3>
            <button class="modal-close" id="closePreviewModal" aria-label="<?php esc_attr_e('Close', 'mydefenselaw'); ?>">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="portal-modal-body">
            <div id="previewContent" class="document-preview-container">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p><?php esc_html_e('Loading preview...', 'mydefenselaw'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Document management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // View toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const grid = document.getElementById('documentsGrid');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const view = this.dataset.view;
            grid.className = view === 'grid' ? 'documents-grid' : 'documents-list';
        });
    });

    // Upload modal
    const uploadBtn = document.getElementById('uploadDocumentBtn');
    const uploadBtnEmpty = document.getElementById('uploadDocumentBtnEmpty');
    const uploadModal = document.getElementById('uploadModal');
    const closeUploadModal = document.getElementById('closeUploadModal');

    [uploadBtn, uploadBtnEmpty].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                uploadModal.classList.add('active');
            });
        }
    });

    if (closeUploadModal) {
        closeUploadModal.addEventListener('click', () => {
            uploadModal.classList.remove('active');
        });
    }

    // Preview modal
    const previewModal = document.getElementById('previewModal');
    const closePreviewModal = document.getElementById('closePreviewModal');

    document.querySelectorAll('[data-action="preview"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.dataset.docId;
            // Load preview (implement with PDF.js or similar)
            previewModal.classList.add('active');
        });
    });

    if (closePreviewModal) {
        closePreviewModal.addEventListener('click', () => {
            previewModal.classList.remove('active');
        });
    }

    // Search functionality
    const searchInput = document.getElementById('documentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.document-card').forEach(card => {
                const name = card.querySelector('.document-name').textContent.toLowerCase();
                card.style.display = name.includes(query) ? '' : 'none';
            });
        });
    }

    // Folder filtering
    document.querySelectorAll('.folder-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.folder-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            const folder = this.dataset.folder;
            document.querySelectorAll('.document-card').forEach(card => {
                if (folder === 'all' || card.dataset.folder === folder) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
get_footer('portal');
?>
